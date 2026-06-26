<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
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

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'pending'  => Booking::forUser($user->id)->pending()->count(),
            'approved' => Booking::forUser($user->id)->approved()->count(),
            'rooms'    => Room::available()->count(),
            'notifs'   => $user->unreadNotifications()->count(),
        ]);
    }
}
