<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        Profile::insert([
            ['user_type' => 'normal'],
            ['user_type' => 'restricted'],
        ]);
    }
}
