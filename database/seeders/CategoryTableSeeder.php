<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if( Category::first() ) {
            return;
        }

        Category::insert([
            ['id' => 1, 'name' => "nature", 'parent_id' => null, 'icon' => 'images/categories/nature.png', 'created_at' => now()],
            ['id' => 2, 'name' => "tree", 'parent_id' => 1, 'icon' => 'images/categories/tree.png', 'created_at' => now()],
            ['id' => 3, 'name' => "mountain", 'parent_id' => 1, 'icon' => 'images/categories/mountain.png', 'created_at' => now()],
            ['id' => 4, 'name' => "beach", 'parent_id' => 1, 'icon' => 'images/categories/beach.png', 'created_at' => now()],
            ['id' => 5, 'name' => "city", 'parent_id' => null, 'icon' => 'images/categories/city.png', 'created_at' => now()],
            ['id' => 6, 'name' => "house", 'parent_id' => 5, 'icon' => 'images/categories/house.png', 'created_at' => now()],
            ['id' => 7, 'name' => "kitchen", 'parent_id' => null, 'icon' => 'images/categories/kitchen.png', 'created_at' => now()],
            ['id' => 8, 'name' => "ice-cream", 'parent_id' => null, 'icon' => 'images/categories/ice-cream.png', 'created_at' => now()],
        ]);
    }
}
