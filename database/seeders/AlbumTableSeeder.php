<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlbumTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if( Album::first() ) {
            return;
        }

        $users = User::all();
        $users->each(function ($user) {
            Album::insert([
                ['user_id' => $user->id, 'name' => 'All favourites', 'status' => 'default', 'created_at' => now(), 'updated_at' => now(),],
                ['user_id' => $user->id, 'name' => fake()->realText(20), 'status' => 'published', 'created_at' => now(), 'updated_at' => now(),],
                ['user_id' => $user->id, 'name' => fake()->realText(20), 'status' => 'published', 'created_at' => now(), 'updated_at' => now(),],
                ['user_id' => $user->id, 'name' => fake()->realText(20), 'status' => 'deleted', 'created_at' => now(), 'updated_at' => now(),],
            ]);
        });
    }
}
