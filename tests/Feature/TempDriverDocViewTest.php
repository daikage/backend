<?php

namespace Tests\Feature;

use App\Models\DriverDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TempDriverDocViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_documents_are_visible_on_the_driver_data_view()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $driver = User::factory()->create([
            'role' => 'driver',
            'name' => 'John Driver',
        ]);

        DriverDocument::create([
            'user_id' => $driver->id,
            'license_path' => 'documents/license_' . $driver->id . '_999.png',
            'insurance_path' => null,
            'vehicle_license_path' => 'documents/vehicle_license_' . $driver->id . '_999.pdf',
            'road_worthiness_path' => null,
            'hackney_permit_path' => null,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get('/admin/users/' . $driver->id)
            ->assertOk()
            ->assertSee('Driver Documents')
            // Image upload: thumbnail section + clickable filename link.
            ->assertSee('license_' . $driver->id . '_999.png')
            // PDF upload: clickable filename link (no image thumbnail).
            ->assertSee('vehicle_license_' . $driver->id . '_999.pdf')
            // Section description.
            ->assertSee('Click a filename to open the file');
    }
}