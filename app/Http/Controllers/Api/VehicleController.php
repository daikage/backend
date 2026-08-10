<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::where('user_id', $request->user()->id)
            ->with('rideCategory')
            ->get();

        return response()->json(['vehicles' => $vehicles]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->isDriver()) {
            return response()->json(['error' => 'Only drivers can register vehicles.'], 403);
        }

        $request->validate([
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'ride_category_id' => 'nullable|exists:ride_categories,id',
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'make' => $request->make,
            'model' => $request->model,
            'year' => $request->year,
            'color' => $request->color,
            'plate_number' => $request->plate_number,
            'ride_category_id' => $request->ride_category_id,
            'is_active' => true,
        ]);

        return response()->json(['vehicle' => $vehicle->load('rideCategory')], 201);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $user = $request->user();

        if ($vehicle->user_id !== $user->id) {
            return response()->json(['error' => 'You can only update your own vehicles.'], 403);
        }

        $request->validate([
            'make' => 'sometimes|string|max:100',
            'model' => 'sometimes|string|max:100',
            'year' => 'sometimes|integer|min:1990|max:' . (date('Y') + 1),
            'color' => 'sometimes|string|max:50',
            'plate_number' => 'sometimes|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'ride_category_id' => 'sometimes|nullable|exists:ride_categories,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $vehicle->update($request->only([
            'make', 'model', 'year', 'color', 'plate_number', 'ride_category_id', 'is_active',
        ]));

        return response()->json(['vehicle' => $vehicle->fresh()->load('rideCategory')]);
    }

    public function destroy(Request $request, Vehicle $vehicle)
    {
        $user = $request->user();

        if ($vehicle->user_id !== $user->id) {
            return response()->json(['error' => 'You can only delete your own vehicles.'], 403);
        }

        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted successfully.']);
    }
}
