<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    public function run()
    {
        // Default Admin User
        Admin::updateOrCreate(
            ['username' => 'admin'], // unique lookup
            [
                'email' => 'admin@site.com',
                'name' => 'Super Admin',
                'role_id' => 0, // 0 = admin
                'password' => Hash::make('password123'),
                'status' => 1,
            ]
        );

        // Default Manager User
        Admin::updateOrCreate(
            ['username' => 'manager'], // unique lookup
            [
                'email' => 'manager@site.com',
                'name' => 'Project Manager',
                'role_id' => 1, // 1 = manager
                'password' => Hash::make('password123'),
                'status' => 1,
            ]
        );

        // Default Normal User
        Admin::updateOrCreate(
            ['username' => 'user'], // unique lookup
            [
                'email' => 'user@site.com',
                'name' => 'Basic User',
                'role_id' => 2, // 2 = normal user
                'password' => Hash::make('password123'),
                'status' => 1,
            ]
        );
    }
}
