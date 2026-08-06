<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@pairride.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '1234567890',
            ]
        );

        // Driver
        User::firstOrCreate(
            ['email' => 'driver@pairride.com'],
            [
                'name' => 'John Driver',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'phone' => '0987654321',
            ]
        );

        // Customer
        User::firstOrCreate(
            ['email' => 'customer@pairride.com'],
            [
                'name' => 'Alice Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '1122334455',
            ]
        );
    }
}
