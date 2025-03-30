<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->updateOrInsert(
            ['id' => 1],
            [
                'role_id' => 0,
                'name' => 'Super Admin',
                'email' => 'admin@site.com',
                'mobile' => null,
                'username' => 'admin',
                'image' => '668faec6ec6401720692422.png',
                'password' => '$2y$12$UUyNnwC9xYZNPWVXLZLcbOq2C5bRqMUtZq8YSnnG7TpBCkwlBJ9QK',
                'status' => 1,
                'remember_token' => 'JmxdPhgysjZMN2EacgMB1dqFTzYgRC3sOCImVrCTGOz0cdWqIiC7BzjuYdK0',
                'created_at' => null,
                'updated_at' => '2025-03-22 20:28:24',
            ]
        );
    }
}
