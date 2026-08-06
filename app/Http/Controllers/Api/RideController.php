<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\RideCategory;
use App\Models\Earning;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Events\RideRequested;
use App\Events\RideStatusUpdated;
use App\Events\DriverLocationUpdated;

class RideController extends Controller
{
    public function requestRide(Request $request)
    {
        $user = $request->user();

        if (! $user->isCustomer()) {
            return response()->json(['error' => 'Only customers can request rides.'], 403);
        }

        $request->validate([
            'ride_category_id' => 'nullable|exists:ride_categories,id',
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'pickup_address' => 'required|string',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
            'dropoff_address' => 'required|string',
            'distance_km' => 'required|numeric',
        ]);

        $category = null;
        if ($request->ride_category_id) {
            $category = RideCategory::find($request->ride_category_id);
        }
        if (!$category) {
            $category = RideCategory::first(); // Fallback to economy
        }

        $fare = $category->base_fare + ($request->distance_km * $category->per_km_rate);

        $ride = Ride::create([
            'customer_id' => $user->id,
            'ride_category_id' => $category->id,
            'platform_commission' => 0.15, // 15% default platform commission
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
        $user = $request->user();

        if (! $user->isDriver()) {
            return response()->json(['error' => 'Only drivers can accept rides.'], 403);
        }

        if ($ride->status !== 'pending') {
            return response()->json(['error' => 'Ride is no longer available'], 400);
        }

        $ride->update([
            'driver_id' => $user->id,
            'status' => 'accepted'
        ]);

        broadcast(new RideStatusUpdated($ride))->toOthers();

        return response()->json(['ride' => $ride]);
    }

    public function updateStatus(Request $request, Ride $ride)
    {
        $user = $request->user();

        if ($ride->driver_id !== $user->id) {
            return response()->json(['error' => 'Only the assigned driver can update this ride.'], 403);
        }

        $request->validate([
            'status' => 'required|in:arrived,started,completed,cancelled'
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'started') {
            $data['started_at'] = now();
        }

        if ($request->status === 'completed' || $request->status === 'cancelled') {
            $data['completed_at'] = now();
        }

        $ride->update($data);

        if ($request->status === 'completed') {
            // Process earnings
            $fare = $ride->actual_fare ?? $ride->estimated_fare;
            $commission = $fare * ($ride->platform_commission ?? 0.15);
            $driverEarning = $fare - $commission;

            Earning::create([
                'driver_id' => $ride->driver_id,
                'ride_id' => $ride->id,
                'amount' => $driverEarning,
                'commission_deducted' => $commission,
            ]);

            $wallet = Wallet::firstOrCreate(['user_id' => $ride->driver_id]);
            $wallet->increment('balance', $driverEarning);
        }

        broadcast(new RideStatusUpdated($ride))->toOthers();

        return response()->json(['ride' => $ride]);
    }

    public function updateLocation(Request $request, Ride $ride)
    {
        $user = $request->user();

        if ($ride->driver_id !== $user->id) {
            return response()->json(['error' => 'Only the assigned driver can update the location.'], 403);
        }

        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'heading' => 'nullable|numeric'
        ]);

        broadcast(new DriverLocationUpdated($ride->id, $request->lat, $request->lng, $request->heading))->toOthers();

        return response()->json(['success' => true]);
    }

    public function show(Request $request, Ride $ride)
    {
        $user = $request->user();

        if ($ride->customer_id !== $user->id && $ride->driver_id !== $user->id) {
            return response()->json(['error' => 'You are not part of this ride.'], 403);
        }

        return response()->json(['ride' => $ride->load('customer', 'driver')]);
    }

    public function active(Request $request)
    {
        $user = $request->user();

        $query = Ride::whereIn('status', ['pending', 'accepted', 'arrived', 'started'])
            ->where(function ($q) use ($user) {
                $q->where('customer_id', $user->id)->orWhere('driver_id', $user->id);
            })
            ->with('customer', 'driver')
            ->latest();

        return response()->json(['ride' => $query->first()]);
    }

    public function available(Request $request)
    {
        $user = $request->user();

        if (! $user->isDriver()) {
            return response()->json(['error' => 'Only drivers can view available rides.'], 403);
        }

        $rides = Ride::where('status', 'pending')
            ->whereNull('driver_id')
            ->with('customer')
            ->latest()
            ->get();

        return response()->json(['rides' => $rides]);
    }
}
