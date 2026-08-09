<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RideCategory;

class RideCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = RideCategory::query();

        // Optional filter by service type
        if ($request->has('service_type')) {
            $query->forServiceType($request->service_type);
        }

        return response()->json(['categories' => $query->get()]);
    }
}
