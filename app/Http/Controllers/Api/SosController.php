<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ride;
use App\Models\SosAlert;

class SosController extends Controller
{
    public function store(Request $request, Ride $ride)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $alert = SosAlert::create([
            'ride_id' => $ride->id,
            'user_id' => $request->user()->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'status' => 'active',
        ]);

        // In a real scenario, this might trigger an SMS, push notification to admins, etc.
        // For now, it just logs it in the database for the Admin Dashboard.

        return response()->json(['message' => 'SOS Alert sent successfully.', 'alert' => $alert]);
    }
}
