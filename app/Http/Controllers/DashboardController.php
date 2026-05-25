<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        $pendingCount   = Booking::forUser($user->id)->pending()->count();
        $approvedCount  = Booking::forUser($user->id)->approved()->count();
        $availableRooms = Room::available()->count();
        $unreadNotifs   = $user->unreadNotifications()->count();

        return view('dashboard', compact(
            'pendingCount',
            'approvedCount',
            'availableRooms',
            'unreadNotifs'
        ));
    }
}
