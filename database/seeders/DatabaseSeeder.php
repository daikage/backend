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
        // Ride Categories
        $economy = \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Economy'],
            ['base_fare' => 1500, 'per_km_rate' => 200]
        );

        $premium = \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Premium'],
            ['base_fare' => 3000, 'per_km_rate' => 400]
        );

        $xl = \App\Models\RideCategory::firstOrCreate(
            ['name' => 'XL'],
            ['base_fare' => 5000, 'per_km_rate' => 600]
        );

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
