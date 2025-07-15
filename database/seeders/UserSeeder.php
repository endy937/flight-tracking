<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'type' => 1, // Admin
            ]
        );
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'user',
                'password' => Hash::make('password'),
                'type' => 0, // user
            ]
        );

    }
}

