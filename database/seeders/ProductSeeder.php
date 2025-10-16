<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Create one brand
        $brandId = DB::table('brands')->updateOrInsert(
            ['name' => 'Chishtia'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Get the brand record (to fetch its ID)
        $brand = DB::table('brands')->where('name', 'Chishtia')->first();

        // 2️⃣ Create possible units
        $units = ['Plate', 'Kg', 'Piece', 'Bottle', 'Glass'];
        foreach ($units as $unit) {
            DB::table('units')->updateOrInsert(
                ['name' => $unit],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // Get default unit (e.g., 'Plate') for assigning to products
        $defaultUnit = DB::table('units')->where('name', 'Plate')->first();

        // 3️⃣ Define all menu categories and products
        $menu = [
            'Saji' => [
                ['name' => 'Full Saji with rice', 'price' => 1700],
                ['name' => 'Full Saji without rice', 'price' => 1500],
                ['name' => 'Half Saji', 'price' => 900],
                ['name' => 'Half Saji without rice', 'price' => 750],
                ['name' => 'Quarter Saji with rice', 'price' => 550],
                ['name' => 'Quarter Saji without rice', 'price' => 350],
            ],

            'Rice & Sides' => [
                ['name' => 'Full Rice', 'price' => 400],
                ['name' => 'Half Rice', 'price' => 200],
                ['name' => 'Mint Raita', 'price' => 50],
                ['name' => 'Fresh Salad', 'price' => 70],
                ['name' => 'Special Allu Bukhara Chatni', 'price' => 50],
            ],

            'Chapli Kabab' => [
                ['name' => 'Beef Chapli Kabab', 'price' => 250],
                ['name' => 'Chicken Chapli Kabab', 'price' => 150],
                ['name' => 'Nalli Chapli Kabab', 'price' => 300],
                ['name' => 'Mutton Chapli Kabab', 'price' => 380],
                ['name' => 'Egg Chapli Kabab', 'price' => 300],
            ],

            'Fried Items' => [
                ['name' => 'Leg Piece Fry', 'price' => 350],
                ['name' => 'Chest Piece Fry', 'price' => 400],
            ],

            'Chicken Karahi' => [
                ['name' => 'Chicken Karahi (Kali Mirchi) Full', 'price' => 1600],
                ['name' => 'Chicken Karahi (Kali Mirchi) Half', 'price' => 850],
                ['name' => 'Chicken Karahi (Kali Mirchi) Quarter', 'price' => 550],
                ['name' => 'Chicken Karahi White Full', 'price' => 1700],
                ['name' => 'Chicken Karahi White Half', 'price' => 900],
                ['name' => 'Chicken Karahi White Quarter', 'price' => 600],
                ['name' => 'Chicken Karahi Makhni Full', 'price' => 1700],
                ['name' => 'Chicken Karahi Makhni Half', 'price' => 900],
                ['name' => 'Chicken Karahi Makhni Quarter', 'price' => 600],
                ['name' => 'Chicken Karahi Shanwari Full', 'price' => 1800],
                ['name' => 'Chicken Karahi Shanwari Half', 'price' => 1000],
                ['name' => 'Chicken Karahi Shanwari Quarter', 'price' => 650],
                ['name' => 'Chicken Karahi Achari Full', 'price' => 1700],
                ['name' => 'Chicken Karahi Achari Half', 'price' => 900],
                ['name' => 'Chicken Karahi Achari Quarter', 'price' => 600],
            ],

            'Beef Karahi' => [
                ['name' => 'Beef Karahi Boneless (Kali Mirchi) Full', 'price' => 2200],
                ['name' => 'Beef Karahi Boneless (Kali Mirchi) Half', 'price' => 1100],
                ['name' => 'Beef Karahi Boneless (Kali Mirchi) Quarter', 'price' => 600],
                ['name' => 'Beef Karahi White Boneless Full', 'price' => 2400],
                ['name' => 'Beef Karahi White Boneless Half', 'price' => 1200],
                ['name' => 'Beef Karahi White Boneless Quarter', 'price' => 650],
                ['name' => 'Beef Karahi Makhni Boneless Full', 'price' => 2400],
                ['name' => 'Beef Karahi Makhni Boneless Half', 'price' => 1250],
                ['name' => 'Beef Karahi Makhni Boneless Quarter', 'price' => 650],
                ['name' => 'Beef Karahi Shanwari Full', 'price' => 2400],
                ['name' => 'Beef Karahi Shanwari Half', 'price' => 1250],
                ['name' => 'Beef Karahi Shanwari Quarter', 'price' => 650],
                ['name' => 'Beef Karahi Achari Full', 'price' => 2300],
                ['name' => 'Beef Karahi Achari Half', 'price' => 1200],
                ['name' => 'Beef Karahi Achari Quarter', 'price' => 650],
            ],

            'Mutton Karahi' => [
                ['name' => 'Mutton Karahi (Kali Mirchi) Full', 'price' => 3300],
                ['name' => 'Mutton Karahi (Kali Mirchi) Half', 'price' => 1680],
                ['name' => 'Mutton Karahi (Kali Mirchi) Quarter', 'price' => 880],
                ['name' => 'Mutton Karahi White Full', 'price' => 3500],
                ['name' => 'Mutton Karahi White Half', 'price' => 1780],
                ['name' => 'Mutton Karahi White Quarter', 'price' => 950],
                ['name' => 'Mutton Karahi Makhni Full', 'price' => 3500],
                ['name' => 'Mutton Karahi Makhni Half', 'price' => 1780],
                ['name' => 'Mutton Karahi Makhni Quarter', 'price' => 950],
                ['name' => 'Mutton Karahi Shanwari Full', 'price' => 3500],
                ['name' => 'Mutton Karahi Shanwari Half', 'price' => 1780],
                ['name' => 'Mutton Karahi Shanwari Quarter', 'price' => 950],
                ['name' => 'Mutton Karahi Achari Full', 'price' => 3500],
                ['name' => 'Mutton Karahi Achari Half', 'price' => 1780],
                ['name' => 'Mutton Karahi Achari Quarter', 'price' => 950],
            ],

            'Tawa Items' => [
                ['name' => 'Tawa Leg Piece', 'price' => 550],
                ['name' => 'Tawa Chest Piece', 'price' => 600],
                ['name' => 'Tawa Boneless', 'price' => 650],
                ['name' => 'Tawa Karahi Piece', 'price' => 550],
                ['name' => 'Desi Ghee Tawa Leg Piece', 'price' => 700],
                ['name' => 'Desi Ghee Tawa Chest Piece', 'price' => 750],
                ['name' => 'Desi Ghee Tawa Boneless', 'price' => 750],
                ['name' => 'Butter Tawa Piece', 'price' => 650],
                ['name' => 'Zaitoon Tawa Piece', 'price' => 700],
            ],

            'Drinks' => [
                ['name' => 'Mineral Water', 'price' => 120],
                ['name' => '1.5L Cold Drink', 'price' => 220],
                ['name' => '1L Cold Drink', 'price' => 170],
                ['name' => '500ml Cold Drink', 'price' => 120],
                ['name' => 'Regular 300ml Cold Drink', 'price' => 70],
            ],
        ];

        // 4️⃣ Create all categories first
        foreach ($menu as $categoryName => $products) {
            DB::table('categories')->updateOrInsert(
                ['name' => $categoryName],
                [ 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 5️⃣ Now insert products under their categories
        foreach ($menu as $categoryName => $products) {
            $category = DB::table('categories')->where('name', $categoryName)->first();

            foreach ($products as $product) {
                DB::table('products')->updateOrInsert(
                    ['name' => $product['name'], 'category_id' => $category->id],
                    [
                        'selling_price' => $product['price'],
                        'brand_id' => $brand->id,
                        'unit_id' => $defaultUnit->id,
                        'sku' => strtoupper(Str::random(8)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
