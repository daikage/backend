<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Wallet;
use App\Models\Earning;

class EarningController extends Controller
{
    public function wallet(Request $request)
    {
        $wallet = Wallet::firstOrCreate(['user_id' => $request->user()->id]);
        return response()->json(['wallet' => $wallet]);
    }

    public function earnings(Request $request)
    {
        $earnings = Earning::where('driver_id', $request->user()->id)
            ->with('ride')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json(['earnings' => $earnings]);
    }
}
