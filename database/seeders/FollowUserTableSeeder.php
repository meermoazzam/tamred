<?php

namespace Database\Seeders;

use App\Models\FollowUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FollowUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if( FollowUser::first() ) {
            return;
        }

        $totalUsersIds = $users = User::all()->pluck('id');
        $totalUsersIds = $totalUsersIds->toArray();

        $users->each(function ($userId) use ($totalUsersIds) {
            if (($key = array_search($userId, $totalUsersIds)) !== false) {
                unset($totalUsersIds[$key]);
            }

            for ($j=0; $j < 5; $j++) {
                $key = array_rand($totalUsersIds);
                $followed_id = $totalUsersIds[$key];
                FollowUser::updateOrCreate([
                    'user_id' => $userId,
                    'followed_id' => $followed_id,
                ],[
                    'user_id' => $userId,
                    'followed_id' => $followed_id,
                ]);
            }
        });
    }
}
