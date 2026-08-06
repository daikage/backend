<?php

use Illuminate\Support\Facades\Broadcast;

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
    return true; // add proper authorization later (driver or customer of this ride)
});
