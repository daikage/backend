<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function rate(Request $request, Ride $ride)
    {
        $user = $request->user();

        // Must be part of the ride
        if ($ride->customer_id !== $user->id && $ride->driver_id !== $user->id) {
            return response()->json(['error' => 'You cannot rate this ride.'], 403);
        }

        if ($ride->status !== 'completed') {
            return response()->json(['error' => 'Can only rate completed rides.'], 400);
        }

        $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $rateeId = $user->id === $ride->customer_id ? $ride->driver_id : $ride->customer_id;

        // Check if already rated
        $existing = Rating::where('ride_id', $ride->id)->where('rater_id', $user->id)->first();
        if ($existing) {
            return response()->json(['error' => 'You have already rated this ride.'], 400);
        }

        $rating = Rating::create([
            'ride_id' => $ride->id,
            'rater_id' => $user->id,
            'ratee_id' => $rateeId,
            'stars' => $request->stars,
            'comment' => $request->comment,
        ]);

        return response()->json(['rating' => $rating]);
    }
}
