<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'first_name'  => 'Super',
            'last_name'   => 'Admin',
            'email'       => 'superadmin@reach.com',
            'password'    => Hash::make('password'),
            'role'        => 'super_admin',
            'institution' => 'Reach Platform',
        ]);

        // Admin
        User::create([
            'first_name'  => 'Admin',
            'last_name'   => 'User',
            'email'       => 'admin@reach.com',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'institution' => 'Reach Platform',
        ]);

        // Regular User
        User::create([
            'first_name'  => 'Regular',
            'last_name'   => 'User',
            'email'       => 'user@reach.com',
            'password'    => Hash::make('password'),
            'role'        => 'user',
            'institution' => 'Sample University',
        ]);
    }
}