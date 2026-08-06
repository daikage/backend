<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Ride;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('drivers', function ($user) {
    return $user->role === 'customer' || $user->role === 'driver';
});

Broadcast::channel('driver.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id || $user->role === 'customer';
});

Broadcast::channel('ride.{id}', function ($user, $id) {
    // Only the ride's customer or assigned driver may join this channel.
    $canJoin = Ride::where('id', $id)
        ->where(function ($query) use ($user) {
            $query->where('customer_id', $user->id)
                ->orWhere('driver_id', $user->id);
        })
        ->exists();
        
    return $canJoin ? ['id' => $user->id, 'name' => $user->name, 'role' => $user->role] : false;
});

Broadcast::channel('admin.map', function ($user) {
    return $user->role === 'admin';
});
