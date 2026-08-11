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
use App\Jobs\SendRideReceipt;

class RideController extends Controller
{
    /**
     * Valid service types for rides.
     */
    private const VALID_SERVICE_TYPES = ['single', 'interstate', 'haulage', 'dispatch'];

    public function requestRide(Request $request)
    {
        $user = $request->user();

        if (! $user->isCustomer()) {
            return response()->json(['error' => 'Only customers can request rides.'], 403);
        }

        $request->validate([
            'ride_category_id' => 'nullable|exists:ride_categories,id',
            'service_type' => 'nullable|in:single,interstate,haulage,dispatch',
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'pickup_address' => 'required|string',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
            'dropoff_address' => 'required|string',
            'distance_km' => 'required|numeric',
            'service_meta' => 'nullable|array',
            // Interstate fields
            'service_meta.destination_state' => 'nullable|string|max:100',
            'service_meta.departure_date' => 'nullable|date',
            'service_meta.num_passengers' => 'nullable|integer|min:1|max:50',
            // Haulage fields
            'service_meta.cargo_description' => 'nullable|string|max:500',
            'service_meta.cargo_weight_kg' => 'nullable|numeric|min:0',
            'service_meta.vehicle_type_required' => 'nullable|in:van,truck,flatbed',
            // Dispatch fields
            'service_meta.package_description' => 'nullable|string|max:500',
            'service_meta.recipient_name' => 'nullable|string|max:100',
            'service_meta.recipient_phone' => 'nullable|string|max:20',
        ]);

        $serviceType = $request->service_type ?? 'single';

        $category = null;
        if ($request->ride_category_id) {
            $category = RideCategory::find($request->ride_category_id);
        }
        if (!$category) {
            $category = RideCategory::where('service_type', $serviceType)->first()
                ?? RideCategory::first(); // Fallback
        }

        // Calculate fare with service type multiplier
        $baseFare = $category->base_fare + ($request->distance_km * $category->per_km_rate);
        $multiplier = Ride::fareMultiplier($serviceType);
        $fare = $baseFare * $multiplier;

        $ride = Ride::create([
            'customer_id' => $user->id,
            'ride_category_id' => $category->id,
            'service_type' => $serviceType,
            'service_meta' => $request->service_meta,
            'platform_commission' => 0.20, // 20% default platform commission
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

        return response()->json(['ride' => $ride->load('rideCategory')]);
    }

    public function acceptRide(Request $request, Ride $ride)
    {
        $user = $request->user();

        if (! $user->isDriver()) {
            return response()->json(['error' => 'Only drivers can accept rides.'], 403);
        }

        // Atomically claim the ride so two drivers can't accept the same request.
        $claimed = Ride::where('id', $ride->id)
            ->where('status', 'pending')
            ->whereNull('driver_id')
            ->update([
                'driver_id' => $user->id,
                'status' => 'accepted',
            ]);

        if (! $claimed) {
            return response()->json(['error' => 'Ride is no longer available'], 400);
        }

        $ride->refresh();

        broadcast(new RideStatusUpdated($ride))->toOthers();

        return response()->json(['ride' => $ride->load('rideCategory')]);
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
            $commission = $fare * ($ride->platform_commission ?? 0.20);
            $driverEarning = $fare - $commission;

            Earning::create([
                'driver_id' => $ride->driver_id,
                'ride_id' => $ride->id,
                'amount' => $driverEarning,
                'commission_deducted' => $commission,
            ]);

            $wallet = Wallet::firstOrCreate(['user_id' => $ride->driver_id]);
            $wallet->increment('balance', $driverEarning);
            
            SendRideReceipt::dispatch($ride);
        }

        broadcast(new RideStatusUpdated($ride))->toOthers();

        return response()->json(['ride' => $ride->load('rideCategory')]);
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

        // Persist the driver's last known location for proximity matching
        $user->update([
            'last_lat' => $request->lat,
            'last_lng' => $request->lng,
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

        return response()->json(['ride' => $ride->load('customer', 'driver', 'rideCategory')]);
    }

    public function active(Request $request)
    {
        $user = $request->user();

        $query = Ride::whereIn('status', ['pending', 'accepted', 'arrived', 'started'])
            ->where(function ($q) use ($user) {
                $q->where('customer_id', $user->id)->orWhere('driver_id', $user->id);
            })
            ->with('customer', 'driver', 'rideCategory')
            ->latest();

        return response()->json(['ride' => $query->first()]);
    }

    public function available(Request $request)
    {
        $user = $request->user();

        if (! $user->isDriver()) {
            return response()->json(['error' => 'Only drivers can view available rides.'], 403);
        }

        $query = Ride::where('status', 'pending')
            ->whereNull('driver_id')
            ->with('customer', 'rideCategory')
            ->latest();

        // Optional service type filter
        if ($request->has('service_type') && in_array($request->service_type, self::VALID_SERVICE_TYPES)) {
            $query->where('service_type', $request->service_type);
        }

        $rides = $query->get();

        return response()->json(['rides' => $rides]);
    }

    public function history(Request $request)
    {
        $user = $request->user();

        $query = Ride::whereIn('status', ['completed', 'cancelled'])
            ->where(function ($q) use ($user) {
                $q->where('customer_id', $user->id)->orWhere('driver_id', $user->id);
            })
            ->with(['customer', 'driver', 'rideCategory'])
            ->latest();

        // Optional pagination or limit
        $rides = $query->paginate(20);

        return response()->json(['rides' => $rides]);
    }

    /**
     * Cancel a ride — only the customer can cancel, and only if it's still pending or accepted.
     */
    public function cancelRide(Request $request, Ride $ride)
    {
        $user = $request->user();

        if ($ride->customer_id !== $user->id) {
            return response()->json(['error' => 'Only the customer who requested this ride can cancel it.'], 403);
        }

        if (! in_array($ride->status, ['pending', 'accepted'])) {
            return response()->json(['error' => 'This ride can no longer be cancelled.'], 400);
        }

        $ride->update([
            'status' => 'cancelled',
            'completed_at' => now(),
        ]);

        broadcast(new RideStatusUpdated($ride))->toOthers();

        return response()->json(['message' => 'Ride cancelled successfully.', 'ride' => $ride->load('rideCategory')]);
    }

    /**
     * Get a fare estimate without creating a ride.
     */
    public function estimate(Request $request)
    {
        $request->validate([
            'ride_category_id' => 'nullable|exists:ride_categories,id',
            'service_type' => 'nullable|in:single,interstate,haulage,dispatch',
            'distance_km' => 'required|numeric|min:0.1',
        ]);

        $serviceType = $request->service_type ?? 'single';

        $category = null;
        if ($request->ride_category_id) {
            $category = RideCategory::find($request->ride_category_id);
        }
        if (!$category) {
            $category = RideCategory::where('service_type', $serviceType)->first()
                ?? RideCategory::first();
        }

        if (!$category) {
            return response()->json(['error' => 'No ride categories configured.'], 500);
        }

        $baseFare = $category->base_fare + ($request->distance_km * $category->per_km_rate);
        $multiplier = Ride::fareMultiplier($serviceType);
        $fare = round($baseFare * $multiplier, 2);

        return response()->json([
            'estimated_fare' => $fare,
            'category' => $category,
            'service_type' => $serviceType,
            'distance_km' => $request->distance_km,
            'multiplier' => $multiplier,
        ]);
    }
}

