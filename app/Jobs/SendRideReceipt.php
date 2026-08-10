<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Ride;
use App\Mail\RideReceiptMail;

class SendRideReceipt implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $ride;

    /**
     * Create a new job instance.
     */
    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customer = $this->ride->customer;
        if ($customer && $customer->email) {
            try {
                Mail::to($customer->email)->send(new RideReceiptMail($this->ride));
            } catch (\Exception $e) {
                // Ignore or log mail errors
            }
        }
    }
}
