<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function events(Request $request): JsonResponse
    {
        $start = $request->query('start', now()->startOfMonth()->toIso8601String());
        $end   = $request->query('end', now()->endOfMonth()->toIso8601String());

        $bookings = Booking::with('bookable')
            ->approved()
            ->where('start_time', '>=', $start)
            ->where('end_time', '<=', $end)
            ->get()
            ->map(fn ($b) => $this->formatEvent($b));

        return response()->json($bookings);
    }

    public function roomEvents(Request $request, Room $room): JsonResponse
    {
        $start = $request->query('start', now()->startOfMonth()->toIso8601String());
        $end   = $request->query('end', now()->endOfMonth()->toIso8601String());

        $bookings = Booking::where('bookable_type', Room::class)
            ->where('bookable_id', $room->id)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where('start_time', '>=', $start)
            ->where('end_time', '<=', $end)
            ->with('user')
            ->get()
            ->map(fn ($b) => $this->formatEvent($b));

        return response()->json($bookings);
    }

    public function myEvents(Request $request): JsonResponse
    {
        $start = $request->query('start', now()->startOfMonth()->toIso8601String());
        $end   = $request->query('end', now()->endOfMonth()->toIso8601String());

        $bookings = Booking::with('bookable')
            ->where('user_id', $request->user()->id)
            ->whereNotIn('status', ['cancelled'])
            ->where('start_time', '>=', $start)
            ->where('end_time', '<=', $end)
            ->get()
            ->map(fn ($b) => $this->formatEvent($b));

        return response()->json($bookings);
    }

    private function formatEvent(Booking $booking): array
    {
        $color = match ($booking->status) {
            'approved' => $booking->bookable_type === Room::class ? '#2563eb' : '#7c3aed',
            'pending'  => '#d97706',
            default    => '#6b7280',
        };

        return [
            'id'                => $booking->id,
            'title'             => $booking->title,
            'start'             => $booking->start_time->toIso8601String(),
            'end'               => $booking->end_time->toIso8601String(),
            'color'             => $color,
            'url'               => route('bookings.show', $booking),
            'extendedProps'     => [
                'status'    => $booking->status,
                'user'      => $booking->user?->name,
                'bookable'  => $booking->bookable?->name ?? '',
                'purpose'   => $booking->purpose ?? '',
            ],
        ];
    }
}
