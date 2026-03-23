<?php
// app/Http/Controllers/ResearchPaperController.php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\ResearchPaper;
use Illuminate\Http\Request;

class ResearchPaperController extends Controller
{
    // View a group and its papers
    public function show(Group $group)
    {
        // Make sure only group members can view
        if (!$group->users->contains(auth()->id())) {
            abort(403, 'You are not a member of this group.');
        }

        $papers = $group->papers()->with('uploader')->latest()->get();

        return view('groups.show', compact('group', 'papers'));
    }

    // Upload a paper to a group
    public function store(Request $request, Group $group)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'required|file|mimes:pdf,doc,docx|max:20480', // 20MB max
        ]);

        // Store file in storage/app/public/papers
        $file     = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('papers', $fileName, 'public');

        ResearchPaper::create([
            'group_id'    => $group->id,
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $filePath,
            'file_name'   => $file->getClientOriginalName(),
            'file_type'   => $file->getClientOriginalExtension(),
        ]);

        return back()->with('success', 'Paper uploaded successfully!');
    }

    // Download a paper
    public function download(ResearchPaper $paper)
    {
        return response()->download(storage_path('app/public/' . $paper->file_path), $paper->file_name);
    }

    // Delete a paper
    public function destroy(ResearchPaper $paper)
    {
        // Only uploader can delete
        if ($paper->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete file from storage
        \Storage::disk('public')->delete($paper->file_path);
        $paper->delete();

        return back()->with('success', 'Paper deleted.');
    }
}