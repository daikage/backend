<?php

namespace Tests\Feature;

use App\Models\Ride;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RideTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        return User::factory()->customer()->create();
    }

    private function driver(): User
    {
        return User::factory()->driver()->create();
    }

    private function createRide(array $overrides = []): Ride
    {
        return Ride::create(array_merge([
            'customer_id' => $this->customer()->id,
            'pickup_address' => 'Lagos Island',
            'pickup_lat' => 6.5244,
            'pickup_lng' => 3.3792,
            'dropoff_address' => 'Ikeja',
            'dropoff_lat' => 6.6018,
            'dropoff_lng' => 3.3515,
            'distance' => 12,
            'estimated_fare' => 3900,
            'status' => 'pending',
        ], $overrides));
    }

    private function ridePayload(array $overrides = []): array
    {
        return array_merge([
            'pickup_lat' => 6.5244,
            'pickup_lng' => 3.3792,
            'pickup_address' => 'Lagos Island',
            'dropoff_lat' => 6.6018,
            'dropoff_lng' => 3.3515,
            'dropoff_address' => 'Ikeja',
            'distance_km' => 12,
        ], $overrides);
    }

    public function test_customer_can_request_a_ride(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/rides/request', $this->ridePayload())
            ->assertOk()
            ->assertJsonPath('ride.status', 'pending')
            ->assertJsonPath('ride.customer_id', $customer->id)
            ->assertJsonPath('ride.estimated_fare', 3900);
    }

    public function test_driver_cannot_request_a_ride(): void
    {
        $driver = $this->driver();

        $this->actingAs($driver, 'sanctum')
            ->postJson('/api/rides/request', $this->ridePayload())
            ->assertForbidden();
    }

    public function test_driver_can_accept_a_pending_ride(): void
    {
        $driver = $this->driver();
        $ride = $this->createRide();

        $this->actingAs($driver, 'sanctum')
            ->postJson("/api/rides/{$ride->id}/accept")
            ->assertOk()
            ->assertJsonPath('ride.status', 'accepted')
            ->assertJsonPath('ride.driver_id', $driver->id);
    }

    public function test_customer_cannot_accept_a_ride(): void
    {
        $otherCustomer = $this->customer();
        $ride = $this->createRide();

        $this->actingAs($otherCustomer, 'sanctum')
            ->postJson("/api/rides/{$ride->id}/accept")
            ->assertForbidden();
    }

    public function test_driver_cannot_accept_an_already_accepted_ride(): void
    {
        $driver = $this->driver();
        $ride = $this->createRide(['status' => 'accepted']);

        $this->actingAs($driver, 'sanctum')
            ->postJson("/api/rides/{$ride->id}/accept")
            ->assertStatus(400);
    }

    public function test_driver_cannot_update_status_of_ride_they_are_not_assigned_to(): void
    {
        $driverA = $this->driver();
        $driverB = $this->driver();
        $ride = $this->createRide(['driver_id' => $driverA->id, 'status' => 'accepted']);

        $this->actingAs($driverB, 'sanctum')
            ->postJson("/api/rides/{$ride->id}/status", ['status' => 'started'])
            ->assertForbidden();
    }

    public function test_driver_can_update_ride_status_to_started(): void
    {
        $driver = $this->driver();
        $ride = $this->createRide(['driver_id' => $driver->id, 'status' => 'accepted']);

        $this->actingAs($driver, 'sanctum')
            ->postJson("/api/rides/{$ride->id}/status", ['status' => 'started'])
            ->assertOk()
            ->assertJsonPath('ride.status', 'started');
    }

    public function test_active_ride_returns_the_current_ride_for_a_customer(): void
    {
        $customer = $this->customer();
        $driver = $this->driver();
        $this->createRide([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'status' => 'started',
        ]);

        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/rides/active')
            ->assertOk()
            ->assertJsonPath('ride.status', 'started');
    }

    public function test_unauthorized_user_cannot_view_a_ride(): void
    {
        $stranger = $this->customer();
        $ride = $this->createRide();

        $this->actingAs($stranger, 'sanctum')
            ->getJson("/api/rides/{$ride->id}")
            ->assertForbidden();
    }

    public function test_driver_can_list_available_rides(): void
    {
        $driver = $this->driver();
        $this->createRide();
        $this->createRide();

        $this->actingAs($driver, 'sanctum')
            ->getJson('/api/rides/available')
            ->assertOk()
            ->assertJsonCount(2, 'rides');
    }

    public function test_customer_cannot_list_available_rides(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/rides/available')
            ->assertForbidden();
    }
}
