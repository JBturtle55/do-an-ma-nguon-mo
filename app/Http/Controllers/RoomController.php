<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $rooms = Room::query()
            ->when($request->filled('type'), fn ($q) => $q->ofType($request->type))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->withCount(['bookings as active_bookings_count' => fn ($q) => $q->approved()->where('end_time', '>', now())])
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    public function show(Room $room): View
    {
        $room->load(['equipment', 'schedules']);
        $upcomingBookings = $room->bookings()
            ->approved()
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return view('rooms.show', compact('room', 'upcomingBookings'));
    }

    public function schedule(Room $room): View
    {
        $room->load('schedules');

        return view('rooms.schedule', compact('room'));
    }
}
