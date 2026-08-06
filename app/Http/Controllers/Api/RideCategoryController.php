<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RideCategory;

class RideCategoryController extends Controller
{
    public function index()
    {
        return response()->json(['categories' => RideCategory::all()]);
    }
}
