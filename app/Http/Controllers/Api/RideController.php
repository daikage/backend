<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use Illuminate\Http\Request;
use App\Events\RideRequested;
use App\Events\RideStatusUpdated;
use App\Events\DriverLocationUpdated;

class RideController extends Controller
{
    public function requestRide(Request $request)
    {
        $request->validate([
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'pickup_address' => 'required|string',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
            'dropoff_address' => 'required|string',
            'distance_km' => 'required|numeric',
        ]);

        $fare = 1500 + ($request->distance_km * 200); // Base fare + Per KM

        $ride = Ride::create([
            'customer_id' => $request->user()->id,
            'pickup_address' => $request->pickup_address,
            'pickup_lat' => $request->pickup_lat,
            'pickup_lng' => $request->pickup_lng,
            'dropoff_address' => $request->dropoff_address,
            'dropoff_lat' => $request->dropoff_lat,
            'dropoff_lng' => $request->dropoff_lng,
            'distance' => $request->distance_km,
            'estimated_fare' => $fare,
            'status' => 'pending',
        ]);

        broadcast(new RideRequested($ride))->toOthers();

        return response()->json(['ride' => $ride]);
    }

    public function acceptRide(Request $request, Ride $ride)
    {
        if ($ride->status !== 'pending') {
            return response()->json(['error' => 'Ride is no longer available'], 400);
        }

        $ride->update([
            'driver_id' => $request->user()->id,
            'status' => 'accepted'
        ]);

        broadcast(new RideStatusUpdated($ride))->toOthers();

        return response()->json(['ride' => $ride]);
    }

    public function updateStatus(Request $request, Ride $ride)
    {
        $request->validate([
            'status' => 'required|in:arrived,started,completed,cancelled'
        ]);

        $ride->update(['status' => $request->status]);

        broadcast(new RideStatusUpdated($ride))->toOthers();

        return response()->json(['ride' => $ride]);
    }

    public function updateLocation(Request $request, Ride $ride)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'heading' => 'nullable|numeric'
        ]);

        broadcast(new DriverLocationUpdated($ride->id, $request->lat, $request->lng, $request->heading))->toOthers();

        return response()->json(['success' => true]);
    }
}
