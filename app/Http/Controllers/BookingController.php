<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingConflictException;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request): View
    {
        $bookings = $this->bookingService->getBookingsForUser(
            $request->user(),
            $request->only(['status', 'date_from', 'date_to', 'search'])
        );

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request): View
    {
        $rooms      = Room::available()->orderBy('name')->get();
        $equipment  = Equipment::available()->with('category')->orderBy('name')->get();
        $preselect  = [
            'type' => $request->query('type', 'App\\Models\\Room'),
            'id'   => $request->query('id'),
        ];

        return view('bookings.create', compact('rooms', 'equipment', 'preselect'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $booking = $this->bookingService->createBooking($request->user(), $request->validated());

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Yêu cầu đặt lịch đã được gửi và đang chờ duyệt.');
        } catch (BookingConflictException $e) {
            return back()->withInput()
                ->withErrors(['conflict' => $e->getMessage()]);
        }
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);
        $booking->load(['bookable', 'user', 'approver', 'equipment']);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);
        $this->bookingService->cancelBooking($booking, $request->user());

        return back()->with('success', 'Đã huỷ booking thành công.');
    }
}
