<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // Get all admins
        $admins = User::where('role', 'admin')
            ->withCount(['bans' => fn($q) => $q->where('is_active', true)])
            ->latest()
            ->get();

        // Get recent admin activities
        $recentActivities = AdminActivity::with('admin')
            ->latest()
            ->take(50)
            ->get();

        $stats = [
            'total_admins' => User::where('role', 'admin')->count(),
            'total_users'  => User::where('role', 'user')->count(),
            'total_groups' => \App\Models\Group::count(),
            'active_bans'  => \App\Models\UserBan::where('is_active', true)->count(),
        ];

        return view('admin.super-dashboard', compact('admins', 'recentActivities', 'stats'));
    }

    public function createAdminForm()
    {
        return view('admin.create-admin');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin = User::create([
             'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'admin',
            'institution' => 'Reach Admins',
        ]);

        //  Log activity
        AdminActivity::log(
            'create_admin',
            auth()->user()->first_name . ' created new admin account for ' . $admin->first_name . ' ' . $admin->last_name,
            [
                'new_admin_id'    => $admin->id,
                'new_admin_email' => $admin->email,
            ]
        );

        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Admin account created successfully.');
    }
}