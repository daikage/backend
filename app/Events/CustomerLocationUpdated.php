<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $rideId;
    public $lat;
    public $lng;

    public function __construct($rideId, $lat, $lng)
    {
        $this->rideId = $rideId;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ride.' . $this->rideId),
            new PrivateChannel('admin.map'),
        ];
    }
}