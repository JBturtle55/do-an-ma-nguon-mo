<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'bookable', 'approver'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('date_from'), fn ($q) => $q->where('start_time', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->where('start_time', '<=', $request->date_to . ' 23:59:59'))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->latest('start_time')
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['user', 'bookable', 'approver', 'equipment']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('approve', $booking);
        $this->bookingService->approveBooking($booking, $request->user(), $request->input('notes'));

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Đã duyệt booking "' . $booking->title . '".');
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('approve', $booking);
        $request->validate(['notes' => 'required|string|max:500']);

        $this->bookingService->rejectBooking($booking, $request->user(), $request->input('notes'));

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Đã từ chối booking "' . $booking->title . '".');
    }
}
