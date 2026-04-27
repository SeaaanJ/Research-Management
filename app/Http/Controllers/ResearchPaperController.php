<?php

namespace App\Http\Controllers;
use App\Models\Group;
use App\Models\ResearchPaper;
use Illuminate\Http\Request;

class ResearchPaperController extends Controller
{
    // View a group and its papers
    public function show(Group $group)
    {
        
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
            'topic'       => 'nullable|string|max:255',
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
            'topic'       => $request->topic,
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


    public function view(ResearchPaper $paper)
    {
        $filePath = storage_path('app/public/' . $paper->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->file($filePath);
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


    public function publishWithAbstract(Request $request, ResearchPaper $researchPaper)
{
    $group = $researchPaper->group;

    // Only owner or uploader can publish
    if ($group->user_id !== auth()->id() && $researchPaper->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'You do not have permission to publish this paper.',
        ], 403);
    }

    // Validate abstract
    $request->validate([
        'abstract' => 'required|string|min:100|max:2000',
    ]);

    // Publish the paper
    $researchPaper->update([
        'published'    => true,
        'published_at' => now(),
        'abstract'     => $request->abstract,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Paper published successfully!',
    ]);
}

public function unpublish(ResearchPaper $researchPaper)
{
    $group = $researchPaper->group;

    // Only owner or uploader can unpublish
    if ($group->user_id !== auth()->id() && $researchPaper->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'You do not have permission to unpublish this paper.',
        ], 403);
    }

    $researchPaper->update([
        'published'    => false,
        'published_at' => null,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Paper unpublished successfully.',
    ]);
}


          //  Publish all papers in a group
    public function publishAll(Group $group)
    {
        // Only group owner can publish all
        if ($group->user_id !== auth()->id()) {
            abort(403);
        }

        $unpublished = $group->papers()->where('published', false)->count();

        if ($unpublished === 0) {
            return back()->with('error', 'All papers are already published.');
        }

        $group->papers()->where('published', false)->update([
            'published'    => true,
            'published_at' => now(),
        ]);

        return back()->with('success', $unpublished . ' paper(s) published successfully!');
    }

    //  Unpublish all papers in a group
    public function unpublishAll(Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            abort(403);
        }

        $published = $group->papers()->where('published', true)->count();

        if ($published === 0) {
            return back()->with('error', 'No papers are currently published.');
        }

        $group->papers()->where('published', true)->update([
            'published'    => false,
            'published_at' => null,
        ]);

        return back()->with('success', $published . ' paper(s) unpublished.');
    }
         
         
         }