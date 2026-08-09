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
        // ── Single Ride Categories ──────────────────────────────
        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Economy', 'service_type' => 'single'],
            ['base_fare' => 1500, 'per_km_rate' => 200]
        );

        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Premium', 'service_type' => 'single'],
            ['base_fare' => 3000, 'per_km_rate' => 400]
        );

        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'XL', 'service_type' => 'single'],
            ['base_fare' => 5000, 'per_km_rate' => 600]
        );

        // ── Interstate Ride Categories ──────────────────────────
        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Interstate Economy', 'service_type' => 'interstate'],
            ['base_fare' => 8000, 'per_km_rate' => 120]
        );

        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Interstate Premium', 'service_type' => 'interstate'],
            ['base_fare' => 15000, 'per_km_rate' => 200]
        );

        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Interstate XL', 'service_type' => 'interstate'],
            ['base_fare' => 25000, 'per_km_rate' => 300]
        );

        // ── Haulage Categories ──────────────────────────────────
        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Van', 'service_type' => 'haulage'],
            ['base_fare' => 5000, 'per_km_rate' => 350]
        );

        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Truck', 'service_type' => 'haulage'],
            ['base_fare' => 12000, 'per_km_rate' => 500]
        );

        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Flatbed', 'service_type' => 'haulage'],
            ['base_fare' => 20000, 'per_km_rate' => 700]
        );

        // ── Dispatch Categories ─────────────────────────────────
        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Bike Dispatch', 'service_type' => 'dispatch'],
            ['base_fare' => 800, 'per_km_rate' => 100]
        );

        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Car Dispatch', 'service_type' => 'dispatch'],
            ['base_fare' => 2000, 'per_km_rate' => 250]
        );

        \App\Models\RideCategory::firstOrCreate(
            ['name' => 'Van Dispatch', 'service_type' => 'dispatch'],
            ['base_fare' => 4000, 'per_km_rate' => 350]
        );

        // ── Users ───────────────────────────────────────────────
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
