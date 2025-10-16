<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Saji',
            'Rice & Sides',
            'Chapli Kabab',
            'Fried Items',
            'Chicken Karahi',
            'Beef Karahi',
            'Mutton Karahi',
            'Tawa Items',
            'Drinks',
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
