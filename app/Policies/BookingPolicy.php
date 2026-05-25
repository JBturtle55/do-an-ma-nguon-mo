<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole('admin') || $booking->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function approve(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $booking->user_id === $user->id
            && in_array($booking->status, ['pending', 'approved']);
    }
}
