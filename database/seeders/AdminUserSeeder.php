<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Seed the Filament admin user
        User::updateOrCreate(
            ['email' => 'jaeron.rivera@gmail.com'],
            [
                'name' => 'allan',
                'password' => '123456789', // Model casts will hash
                'email_verified_at' => now(),
            ]
        );
    }
}
