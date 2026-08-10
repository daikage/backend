<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ride;
use App\Events\RideStatusUpdated;

class ExpireRides extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rides:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel rides that have been pending for more than 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredRides = Ride::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(5))
            ->get();

        $count = 0;
        foreach ($expiredRides as $ride) {
            $ride->update(['status' => 'cancelled']);
            broadcast(new RideStatusUpdated($ride));
            $count++;
        }

        $this->info("Expired $count pending rides.");
    }
}
