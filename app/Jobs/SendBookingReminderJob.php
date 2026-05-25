<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Notifications\BookingReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBookingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function handle(): void
    {
        $this->booking->refresh();

        if ($this->booking->status === 'approved') {
            $this->booking->user->notify(new BookingReminderNotification($this->booking));
        }
    }
}
