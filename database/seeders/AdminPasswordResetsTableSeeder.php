<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminPasswordResetsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('admin_password_resets')->updateOrInsert(
            ['id' => 1],
            [
                'email' => 'admin@site.com',
                'token' => '774522',
                'status' => 0,
                'created_at' => '2024-07-11 04:22:15',
                'updated_at' => null,
            ]
        );
    }
}
