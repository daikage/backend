<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ride;
use App\Models\Message;
use App\Events\MessageSent;

class ChatController extends Controller
{
    /**
     * Only the ride's customer and assigned driver may interact with its chat.
     */
    private function canAccess(Ride $ride, $user): bool
    {
        return (int) $ride->customer_id === (int) $user->id
            || (int) $ride->driver_id === (int) $user->id;
    }

    public function index(Request $request, Ride $ride)
    {
        if (! $this->canAccess($ride, $request->user())) {
            return response()->json(['error' => 'You are not part of this ride.'], 403);
        }

        $messages = $ride->messages()->with('sender')->orderBy('created_at', 'asc')->get();
        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request, Ride $ride)
    {
        if (! $this->canAccess($ride, $request->user())) {
            return response()->json(['error' => 'You are not part of this ride.'], 403);
        }

        $request->validate(['body' => 'required|string']);

        $message = $ride->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        $message->load('sender');

        broadcast(new MessageSent($message));

        return response()->json(['message' => $message]);
    }
}
