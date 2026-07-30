<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@telkom.com'],
            [
                'name' => 'Administrator',
                'email' => 'admin@telkom.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Agency User
        User::updateOrCreate(
            ['email' => 'agency@telkom.com'],
            [
                'name' => 'Agency User',
                'email' => 'agency@telkom.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: admin@telkom.com / password');
        $this->command->info('Agency: agency@telkom.com / password');
    }
}