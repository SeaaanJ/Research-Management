<?php

namespace App\Http\Controllers;

use App\Models\PaperAnnotation;
use App\Models\ResearchPaper;
use Illuminate\Http\Request;

class PaperAnnotationController extends Controller
{
    // Get all annotations for a paper
    public function index(ResearchPaper $researchPaper)
    {
        // Only group members can view annotations
        $group = $researchPaper->group;
        if (!$group->users->contains(auth()->id())) {
            abort(403);
        }

        $annotations = $researchPaper->annotations()
            ->with('user:id,first_name,last_name')
            ->latest()
            ->get();

        return response()->json($annotations);
    }

    // Save a new annotation
    public function store(Request $request, ResearchPaper $researchPaper)
    {
        $group = $researchPaper->group;
        if (!$group->users->contains(auth()->id())) {
            abort(403);
        }

        $request->validate([
            'x'       => 'required|numeric|min:0|max:100',
            'y'       => 'required|numeric|min:0|max:100',
            'comment' => 'required|string|max:1000',
        ]);

        $annotation = PaperAnnotation::create([
            'paper_id' => $researchPaper->id,
            'user_id'  => auth()->id(),
            'x'        => $request->x,
            'y'        => $request->y,
            'comment'  => $request->comment,
        ]);

        // Load user for the response so JS can display name
        $annotation->load('user:id,first_name,last_name');

        return response()->json($annotation, 201);
    }

    // Delete an annotation
    public function destroy(ResearchPaper $researchPaper, PaperAnnotation $annotation)
    {
        // // Only owner of the group can delete annotations
        // $group = $researchPaper->group;
        // if ($group->user_id !== auth()->id()) {
        //     abort(403);
        // }

            // Only the annotation creator or group owner can delete
            $group = $researchPaper->group;
            if ($annotation->user_id !== auth()->id() && $group->user_id !== auth()->id()) {
                abort(403);
            }
        $annotation->delete();

        return response()->json(['success' => true]);
    }
}