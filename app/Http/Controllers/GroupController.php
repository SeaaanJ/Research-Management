<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        // ✅ Eager load users to avoid N+1 query
        $groups = Auth::user()->groups()->with('users')->get() ?? collect();

        return view('dashboard', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = Group::create([
            'name'    => $request->name,
            'user_id' => auth()->id(),
        ]);

        // ✅ Use users() not members() for consistency
        $group->users()->attach(auth()->id());

        return redirect()->route('dashboard')->with('success', 'Group created successfully!');
    }

    public function invite(Request $request, Group $group)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($group->users()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'This user is already a member.');
        }

        $group->users()->attach($request->user_id);

        return back()->with('success', 'Member added to ' . $group->name);
    }
}