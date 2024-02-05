<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategoryTableSeeder::class,
            UserTableSeeder::class,
            AlbumTableSeeder::class,
            PostTableSeeder::class,
            CommentTableSeeder::class,
            FollowUserTableSeeder::class,
        ]);
    }
}
