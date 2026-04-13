<?php
// app/Http/Controllers/PaperCommentController.php

namespace App\Http\Controllers;

use App\Models\PaperComment;
use App\Models\ResearchPaper;
use Illuminate\Http\Request;

class PaperCommentController extends Controller
{
    // Get all comments for a paper
    public function index(ResearchPaper $researchPaper)
{
    $group = $researchPaper->group;
    if (!$group->users->contains(auth()->id())) {
        abort(403);
    }

    $comments = $researchPaper->comments()
        ->with('user:id,first_name,last_name') // ✅ always load user
        ->latest()
        ->get();

    return response()->json($comments);
}

    // Post a new comment
    public function store(Request $request, ResearchPaper $researchPaper)
{
    $group = $researchPaper->group;
    if (!$group->users->contains(auth()->id())) {
        abort(403);
    }

    $request->validate([
        'comment' => 'required|string|max:1000',
    ]);

    $comment = PaperComment::create([
        'research_paper_id' => $researchPaper->id,
        'user_id'           => auth()->id(),
        'comment'           => $request->comment,
    ]);

    // ✅ Always load full user relationship
    $comment->load('user:id,first_name,last_name');

    return response()->json($comment, 201);
}
    // Delete a comment
    public function destroy(ResearchPaper $researchPaper, PaperComment $comment)
    {
        // Only the comment author or group owner can delete
        $group = $researchPaper->group;
        if ($comment->user_id !== auth()->id() && $group->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }
}