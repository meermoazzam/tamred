<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if( Comment::first() ) {
            return;
        }

        $posts = Post::all();
        $userIds = User::all()->pluck('id');

        $posts->each(function ($post) use ($userIds) {
            for ($i=0; $i < 10; $i++) {
                Comment::create([
                    'user_id' => $userIds->random(),
                    'post_id' => $post->id,
                    'status' => 'published',
                    'description' => fake()->realText(500),
                ]);
            }
        });
    }
}
