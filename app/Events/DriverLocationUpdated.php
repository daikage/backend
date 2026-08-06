<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $rideId;
    public $lat;
    public $lng;
    public $heading;

    public function __construct($rideId, $lat, $lng, $heading = null)
    {
        $this->rideId = $rideId;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->heading = $heading;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ride.' . $this->rideId),
            new PrivateChannel('admin.map'),
        ];
    }
}
