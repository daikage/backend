<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Get or create the authenticated customer's wallet.
     */
    public function wallet(Request $request)
    {
        $wallet = Wallet::firstOrCreate(['user_id' => $request->user()->id]);

        return response()->json(['wallet' => $wallet]);
    }

    /**
     * List the authenticated customer's transactions.
     */
    public function transactions(Request $request)
    {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->with('ride')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['transactions' => $transactions]);
    }
}
