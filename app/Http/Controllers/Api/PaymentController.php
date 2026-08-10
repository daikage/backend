<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\Ride;

class PaymentController extends Controller
{
    public function initialize(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|exists:rides,id',
            'amount' => 'required|numeric',
            'gateway' => 'required|in:paystack,flutterwave'
        ]);

        $user = $request->user();
        $reference = uniqid('txn_');

        $transaction = Transaction::create([
            'ride_id' => $request->ride_id,
            'user_id' => $user->id,
            'amount' => $request->amount,
            'payment_method' => $request->gateway,
            'transaction_reference' => $reference,
        ]);

        if ($request->gateway === 'paystack') {
            return $this->initializePaystack($user, $request->amount, $reference);
        } else {
            return $this->initializeFlutterwave($user, $request->amount, $reference);
        }
    }

    private function initializePaystack($user, $amount, $reference)
    {
        $response = Http::withToken(config('services.paystack.secret_key'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $user->email ?? 'no-email@pairride.com',
                'amount' => $amount * 100, // Paystack uses kobo
                'reference' => $reference,
                'callback_url' => url('/api/payment/verify/paystack')
            ]);

        if ($response->successful()) {
            return response()->json([
                'authorization_url' => $response['data']['authorization_url'],
                'reference' => $reference,
            ]);
        }

        return response()->json(['error' => 'Could not initialize Paystack'], 500);
    }

    private function initializeFlutterwave($user, $amount, $reference)
    {
        $response = Http::withToken(config('services.flutterwave.secret_key'))
            ->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $reference,
                'amount' => $amount,
                'currency' => 'NGN', // Customize based on your needs
                'redirect_url' => url('/api/payment/verify/flutterwave'),
                'customer' => [
                    'email' => $user->email ?? 'no-email@pairride.com',
                    'name' => $user->name,
                    'phonenumber' => $user->phone
                ]
            ]);

        if ($response->successful()) {
            return response()->json([
                'authorization_url' => $response['data']['link'],
                'reference' => $reference,
            ]);
        }

        return response()->json(['error' => 'Could not initialize Flutterwave'], 500);
    }

    public function verify(Request $request, $gateway)
    {
        $reference = $request->reference;

        $transaction = Transaction::where('transaction_reference', $reference)->firstOrFail();

        if ($transaction->payment_status === 'completed') {
             return response()->json(['message' => 'Already completed']);
        }

        $isSuccessful = false;

        if ($gateway === 'paystack') {
            $response = Http::withToken(config('services.paystack.secret_key'))
                ->get("https://api.paystack.co/transaction/verify/{$reference}");
            
            if ($response->successful() && $response['data']['status'] === 'success') {
                $isSuccessful = true;
            }
        } else if ($gateway === 'flutterwave') {
            $transactionId = $request->transaction_id;
            $response = Http::withToken(config('services.flutterwave.secret_key'))
                ->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

            if ($response->successful() && $response['data']['status'] === 'successful') {
                $isSuccessful = true;
            }
        }

        if ($isSuccessful) {
            $transaction->update(['payment_status' => 'completed']);
            return response()->json(['message' => 'Payment successful', 'transaction' => $transaction]);
        }

        $transaction->update(['payment_status' => 'failed']);
        return response()->json(['message' => 'Payment failed'], 400);
    }

    /**
     * Paystack server-to-server webhook — called by Paystack after payment.
     */
    public function paystackWebhook(Request $request)
    {
        // Verify signature
        $signature = $request->header('x-paystack-signature');
        $secret = config('services.paystack.secret_key');
        $computedSignature = hash_hmac('sha512', $request->getContent(), $secret);

        if ($signature !== $computedSignature) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $payload = $request->all();
        if (($payload['event'] ?? '') !== 'charge.success') {
            return response()->json(['message' => 'Event ignored']);
        }

        $reference = $payload['data']['reference'] ?? null;
        if (!$reference) {
            return response()->json(['error' => 'No reference'], 400);
        }

        $transaction = Transaction::where('transaction_reference', $reference)->first();
        if ($transaction && $transaction->payment_status !== 'completed') {
            $transaction->update(['payment_status' => 'completed']);
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    /**
     * Flutterwave server-to-server webhook.
     */
    public function flutterwaveWebhook(Request $request)
    {
        // Verify signature
        $signature = $request->header('verif-hash');
        $secret = config('services.flutterwave.encryption_key');

        if ($signature !== $secret) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $payload = $request->all();
        $reference = $payload['data']['tx_ref'] ?? null;
        $status = $payload['data']['status'] ?? '';

        if (!$reference) {
            return response()->json(['error' => 'No reference'], 400);
        }

        $transaction = Transaction::where('transaction_reference', $reference)->first();
        if ($transaction && $transaction->payment_status !== 'completed' && $status === 'successful') {
            $transaction->update(['payment_status' => 'completed']);
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
