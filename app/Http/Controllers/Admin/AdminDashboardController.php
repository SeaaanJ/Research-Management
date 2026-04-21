<?php
// app/Http/Controllers/Admin/AdminDashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Models\ResearchPaper;
use App\Models\PaperComment;
use App\Models\GroupInvite;

class AdminDashboardController extends Controller
{
    public function index()
    {

     $users = User::where('role', 'user')
        ->with('activeBan.banner')
        ->withCount([
            'groups',
            'receivedInvites' => fn($q) => $q->where('status', 'pending')
        ])
        ->latest()
        ->get();
        // All users
        $users = User::where('role', 'user')
            ->withCount(['groups', 'receivedInvites' => fn($q) => $q->where('status', 'pending')])
            ->latest()
            ->get();

        // Stats
        $totalUsers       = User::where('role', 'user')->count();
        $totalGroups      = Group::count();
        $totalPapers      = ResearchPaper::count();
        $publishedPapers  = ResearchPaper::where('published', true)->count();

        // Recent activity — uploads, comments, group creation, invites
        $recentUploads = ResearchPaper::with(['uploader', 'group'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'type'      => 'upload',
                'user'      => $p->uploader->first_name . ' ' . $p->uploader->last_name,
                'user_id'   => $p->uploader->id,
                'message'   => 'uploaded a paper',
                'detail'    => $p->title,
                'context'   => 'in ' . $p->group->name,
                'time'      => $p->created_at,
                'icon'      => '📄',
                'color'     => 'indigo',
            ]);

        $recentComments = PaperComment::with(['user', 'paper.group'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($c) => [
                'type'      => 'comment',
                'user'      => $c->user->first_name . ' ' . $c->user->last_name,
                'user_id'   => $c->user->id,
                'message'   => 'commented on',
                'detail'    => $c->paper->title,
                'context'   => 'in ' . $c->paper->group->name,
                'time'      => $c->created_at,
                'icon'      => '💬',
                'color'     => 'yellow',
            ]);

        $recentGroups = Group::with('owner')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($g) => [
                'type'      => 'group',
                'user'      => $g->owner->first_name . ' ' . $g->owner->last_name,
                'user_id'   => $g->owner->id,
                'message'   => 'created group',
                'detail'    => $g->name,
                'context'   => '',
                'time'      => $g->created_at,
                'icon'      => '👥',
                'color'     => 'green',
            ]);

        $recentInvites = GroupInvite::with(['sender', 'receiver', 'group'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($i) => [
                'type'      => 'invite',
                'user'      => $i->sender->first_name . ' ' . $i->sender->last_name,
                'user_id'   => $i->sender->id,
                'message'   => 'invited',
                'detail'    => $i->receiver->first_name . ' ' . $i->receiver->last_name,
                'context'   => 'to ' . $i->group->name,
                'time'      => $i->created_at,
                'icon'      => '✉️',
                'color'     => 'purple',
            ]);

        // Merge and sort all activity
        $recentActivity = $recentUploads
            ->concat($recentComments)
            ->concat($recentGroups)
            ->concat($recentInvites)
            ->sortByDesc('time')
            ->take(20)
            ->values();

        return view('admin.dashboard', compact(
            'users',
            'totalUsers',
            'totalGroups',
            'totalPapers',
            'publishedPapers',
            'recentActivity'
        ));
    }
}