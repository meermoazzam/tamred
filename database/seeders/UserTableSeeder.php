<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\User;
use App\Models\UserMeta;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if( User::first() ) {
            return;
        }

        for ($i=0; $i < 10; $i++) {
            $user = new User();
            $user->first_name = fake()->firstName();
            $user->last_name = fake()->lastName();
            $user->password = Hash::make('password');
            $user->nickname = fake()->name();
            $user->email = fake()->unique()->safeEmail();
            $user->username = $user->email;
            $user->email_verified_at = now();
            $user->date_of_birth = fake()->date();
            $user->gender = 'female';
            $user->location = fake()->address();
            $user->latitude = round(fake()->latitude(), 6);
            $user->longitude = round(fake()->longitude(), 6);
            $user->city = fake()->city();
            $user->state = fake()->state();
            $user->country = fake()->country();
            $user->status = 'active';
            $user->save();

            $userMeta = UserMeta::insert([
                ['user_id' => $user->id, 'meta_key' => 'terms_and_conditions', 'meta_value' => true, 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => $user->id, 'meta_key' => 'privacy_policy', 'meta_value' => true, 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => $user->id, 'meta_key' => 'marketing', 'meta_value' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);

            $album = Album::create([
                'user_id' => $user->id,
                'name' => 'All favourites',
                'status' => 'default'
            ]);
        }
    }
}
