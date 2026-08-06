<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ride;
use App\Models\Message;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function index(Ride $ride)
    {
        $messages = $ride->messages()->with('sender')->orderBy('created_at', 'asc')->get();
        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request, Ride $ride)
    {
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
