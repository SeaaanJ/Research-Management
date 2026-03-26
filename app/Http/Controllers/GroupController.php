<?php
// app/Http/Controllers/GroupController.php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function index()
{
    $groups         = Auth::user()->groups()->with(['users', 'invites'])->get() ?? collect();
    $pendingInvites = Auth::user()->pendingInvites()->get();

    return view('dashboard', compact('groups', 'pendingInvites'));
}

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $group = Group::create([
            'name'    => $request->name,
            'user_id' => auth()->id(),
        ]);

        $group->users()->attach(auth()->id());

        return redirect()->route('groups')->with('success', 'Group created successfully!');
    }

    //  Send invite
    public function invite(Request $request, Group $group)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Can't invite yourself
        if ($request->user_id == auth()->id()) {
            return back()->with('error', 'You cannot invite yourself.');
        }

        // Already a member
        if ($group->users()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'This user is already a member.');
        }

        // Already has pending invite
        $existing = GroupInvite::where('group_id', $group->id)
            ->where('receiver_id', $request->user_id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'An invite has already been sent to this user.');
        }

        GroupInvite::create([
            'group_id'    => $group->id,
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->user_id,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Invite sent successfully!');
    }

    //  Accept invite
    public function acceptInvite(GroupInvite $invite)
    {
        if ($invite->receiver_id !== auth()->id()) {
            abort(403);
        }

        $invite->update(['status' => 'accepted']);
        $invite->group->users()->attach(auth()->id());

        return back()->with('success', 'You have joined ' . $invite->group->name . '!');
    }

    //  Decline invite
    public function declineInvite(GroupInvite $invite)
    {
        if ($invite->receiver_id !== auth()->id()) {
            abort(403);
        }

        $invite->update(['status' => 'declined']);

        return back()->with('success', 'Invite declined.');
    }

    // Delete confirmation
    public function confirmDelete(Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            abort(403);
        }

        $confirmString = Str::upper(Str::random(6));
        session(['delete_confirm_' . $group->id => $confirmString]);

        return response()->json([
            'confirm_string' => $confirmString,
            'group_name'     => $group->name,
            'group_id'       => $group->id,
        ]);
    }

    // Delete group
    public function destroy(Request $request, Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'confirm_string' => 'required|string',
            'password'       => 'required|string',
        ]);

        if (!password_verify($request->password, auth()->user()->password)) {
            return response()->json(['error' => 'password', 'message' => 'Incorrect password.'], 422);
        }

        $sessionKey     = 'delete_confirm_' . $group->id;
        $expectedString = session($sessionKey);

        if (strtoupper($request->confirm_string) !== $expectedString) {
            return response()->json(['error' => 'confirm_string', 'message' => 'Confirmation code does not match.'], 422);
        }

        foreach ($group->papers as $paper) {
            \Storage::disk('public')->delete($paper->file_path);
        }

        $group->delete();
        session()->forget($sessionKey);

        return response()->json(['success' => true, 'message' => 'Group deleted successfully.']);
    }
}