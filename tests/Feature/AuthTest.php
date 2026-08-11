<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_register_as_a_customer(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'access_token', 'token_type'])
            ->assertJsonPath('user.email', 'jane@example.com');
    }

    public function test_a_user_can_login_with_email(): void
    {
        $user = User::factory()->customer()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->postJson('/api/login', [
            'login' => $user->email,
            'password' => 'password123',
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['user', 'access_token', 'token_type']);
    }

    public function test_a_user_can_login_with_phone(): void
    {
        $user = User::factory()->customer()->create([
            'phone' => '08012345678',
            'password' => bcrypt('password123'),
        ]);

        $this->postJson('/api/login', [
            'login' => '08012345678',
            'password' => 'password123',
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['user', 'access_token', 'token_type']);
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        $this->postJson('/api/login', [
            'login' => 'nobody@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(422);
    }

    public function test_protected_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/user')->assertStatus(401);
    }

    public function test_driver_can_toggle_availability(): void
    {
        $driver = User::factory()->driver()->create(['is_online' => false]);
        \App\Models\DriverDocument::create([
            'user_id' => $driver->id,
            'status' => 'approved',
            'type' => 'license',
            'front_image' => 'front.jpg',
            'back_image' => 'back.jpg'
        ]);

        $this->actingAs($driver, 'sanctum')
            ->postJson('/api/driver/availability')
            ->assertOk()
            ->assertJson(['is_online' => true]);
    }

    public function test_customer_cannot_toggle_availability(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/driver/availability')
            ->assertForbidden();
    }
}
