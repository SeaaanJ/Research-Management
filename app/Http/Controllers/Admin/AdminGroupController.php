<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\ResearchPaper;

class AdminGroupController extends Controller
{
    public function index()
    {
        $groups = Group::with(['owner', 'users'])
            ->withCount(['papers', 'users'])
            ->latest()
            ->get();

        return view('admin.groups.index', compact('groups'));
    }

    public function show(Group $group)
    {
        $group->load(['owner', 'users', 'invites']);

        $papers = ResearchPaper::where('group_id', $group->id)
            ->with(['uploader', 'comments.user'])
            ->withCount('comments')
            ->latest()
            ->get();

        // Recent activity in this group
        $recentUploads = ResearchPaper::where('group_id', $group->id)
            ->with('uploader')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'type'    => 'upload',
                'user'    => $p->uploader->first_name . ' ' . $p->uploader->last_name,
                'message' => 'uploaded',
                'detail'  => $p->title,
                'time'    => $p->created_at,
                'icon'    => '📄',
                'color'   => 'indigo',
            ]);

        $recentComments = \App\Models\PaperComment::whereHas('paper', fn($q) =>
            $q->where('group_id', $group->id)
        )
            ->with(['user', 'paper'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($c) => [
                'type'    => 'comment',
                'user'    => $c->user->first_name . ' ' . $c->user->last_name,
                'message' => 'commented on',
                'detail'  => $c->paper->title,
                'time'    => $c->created_at,
                'icon'    => '💬',
                'color'   => 'yellow',
            ]);

        $recentActivity = $recentUploads
            ->concat($recentComments)
            ->sortByDesc('time')
            ->take(15)
            ->values();

        return view('admin.groups.show', compact('group', 'papers', 'recentActivity'));
    }
}