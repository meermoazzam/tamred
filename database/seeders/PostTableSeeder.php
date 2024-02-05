<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if( Post::first() ) {
            return;
        }

        $users = User::all();

        $users->each(function ($user) {
            for ($i=0; $i < 5; $i++) {
                Post::create([
                    'user_id' => $user->id,
                    'status' => 'published',
                    'title' => fake()->realText(50),
                    'description' => fake()->realText(500),
                    'total_comments' => 10,
                    'location' => fake()->address(),
                    'latitude' => round(fake()->latitude(), 6),
                    'longitude' => round(fake()->longitude(), 6),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'country' => fake()->country(),
                ]);
            }
        });
    }
}
