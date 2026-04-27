<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\ResearchPaper;
use App\Models\PaperComment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
 // app/Http/Controllers/DashboardController.php

public function index()
{
    $user   = Auth::user();
    $groups = $user->groups()->with(['users', 'invites'])->get() ?? collect();

    // Published papers from user's groups
    $publishedPapers = ResearchPaper::whereIn('group_id', $groups->pluck('id'))
        ->where('published', true)
        ->with(['uploader', 'group'])
        ->latest('published_at')
        ->get();

    // ✅ ALL published papers across the platform (for discovery feed at bottom)
    $allPublishedPapers = ResearchPaper::where('published', true)
        ->with(['uploader', 'group'])
        ->latest('published_at')
        ->take(20)
        ->get();

    // Recent activity
    $recentUploads = ResearchPaper::whereIn('group_id', $groups->pluck('id'))
        ->with(['uploader', 'group'])
        ->latest()
        ->take(5)
        ->get()
        ->map(fn($p) => [
            'type'     => 'upload',
            'message'  => "{$p->uploader->first_name} {$p->uploader->last_name} uploaded a paper",
            'detail'   => $p->title,
            'group'    => $p->group->name,
            'time'     => $p->created_at,
            'group_id' => $p->group_id,
            'url'      => route('groups.show', $p->group_id),
        ]);

    $recentComments = PaperComment::whereHas('paper', fn($q) =>
        $q->whereIn('group_id', $groups->pluck('id'))
    )
        ->with(['user', 'paper.group'])
        ->latest()
        ->take(5)
        ->get()
        ->map(fn($c) => [
            'type'     => 'comment',
            'message'  => "{$c->user->first_name} {$c->user->last_name} commented on a paper",
            'detail'   => $c->paper->title,
            'group'    => $c->paper->group->name,
            'time'     => $c->created_at,
            'group_id' => $c->paper->group_id,
            'url'      => route('groups.show', $c->paper->group_id),
        ]);

    $recentActivity = $recentUploads->concat($recentComments)
        ->sortByDesc('time')
        ->take(10)
        ->values();

    $pendingInvites = $user->pendingInvites()->get();

    return view('dashboard', compact(
        'groups',
        'publishedPapers',
        'recentActivity',
        'pendingInvites',
        'allPublishedPapers' // ✅ Add this
    ));
}
}