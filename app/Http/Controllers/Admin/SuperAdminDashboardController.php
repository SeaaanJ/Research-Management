<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.super-dashboard');
    }

    public function createAdmin()
    {
       return view('admin.super-dashboard');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'admin',
            'institution' => 'Reach Admins',
        ]);

        return redirect()->route('super-admin.dashboard')
                         ->with('status', 'New Admin account created successfully!');
    }
}