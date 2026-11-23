<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use App\Models\PointsLedger;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $normal = Profile::where('user_type', 'normal')->firstOrFail();
        $restricted = Profile::where('user_type', 'restricted')->firstOrFail();

        for ($i = 1; $i <= 20000; $i++) {

            // Every 10th user is restricted
            $profile = ($i % 10 === 0) ? $restricted : $normal;

            $user = User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password123'),
                'profile_id' => $profile->id,
            ]);

            // Give each user 1000 starting points
            PointsLedger::create([
                'user_id' => $user->id,
                'type' => 'earn',
                'points' => 1000,
            ]);
        }
    }
}
