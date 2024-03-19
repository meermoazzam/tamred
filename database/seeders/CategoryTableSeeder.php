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
            ['id' => 1, 'name' => "nature", 'italian_name' => 'natura', 'parent_id' => null, 'icon' => 'images/categories/nature.png', 'created_at' => now()],
            ['id' => 2, 'name' => "tree", 'italian_name' => 'albero', 'parent_id' => 1, 'icon' => 'images/categories/tree.png', 'created_at' => now()],
            ['id' => 3, 'name' => "mountain", 'italian_name' => 'montagna', 'parent_id' => 1, 'icon' => 'images/categories/mountain.png', 'created_at' => now()],
            ['id' => 4, 'name' => "beach", 'italian_name' => 'spiaggia', 'parent_id' => 1, 'icon' => 'images/categories/beach.png', 'created_at' => now()],
            ['id' => 5, 'name' => "city", 'italian_name' => 'città', 'parent_id' => null, 'icon' => 'images/categories/city.png', 'created_at' => now()],
            ['id' => 6, 'name' => "house", 'italian_name' => 'casa', 'parent_id' => 5, 'icon' => 'images/categories/house.png', 'created_at' => now()],
            ['id' => 7, 'name' => "kitchen", 'italian_name' => 'cucina', 'parent_id' => null, 'icon' => 'images/categories/kitchen.png', 'created_at' => now()],
            ['id' => 8, 'name' => "ice-cream", 'italian_name' => 'gelato', 'parent_id' => null, 'icon' => 'images/categories/ice-cream.png', 'created_at' => now()],
        ]);
    }
}
