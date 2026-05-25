<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Room;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_rooms'      => Room::count(),
            'available_rooms'  => Room::available()->count(),
            'total_equipment'  => Equipment::count(),
            'total_users'      => User::count(),
            'pending_bookings' => Booking::pending()->count(),
            'today_bookings'   => Booking::approved()->whereDate('updated_at', today())->count(),
        ];

        $pendingBookings = Booking::with(['user', 'bookable'])
            ->pending()
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingBookings'));
    }
}
