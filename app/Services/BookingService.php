<?php

namespace App\Services;

use App\Exceptions\BookingConflictException;
use App\Jobs\SendBookingReminderJob;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingCreatedNotification;
use App\Notifications\BookingStatusChangedNotification;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class BookingService
{
    public function __construct(
        protected AvailabilityService $availabilityService
    ) {}

    /**
     * Create a booking inside a transaction with pessimistic lock to prevent race conditions.
     *
     * @throws BookingConflictException
     */
    public function createBooking(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            $start = Carbon::parse($data['start_time']);
            $end   = Carbon::parse($data['end_time']);

            // Pessimistic lock: block concurrent creates for the same bookable/time window
            Booking::where('bookable_type', $data['bookable_type'])
                ->where('bookable_id', $data['bookable_id'])
                ->where('start_time', '<', $end)
                ->where('end_time', '>', $start)
                ->lockForUpdate()
                ->get();

            $conflicts = $this->availabilityService->getConflicts(
                $data['bookable_type'],
                $data['bookable_id'],
                $start,
                $end
            );

            if ($conflicts->isNotEmpty()) {
                throw new BookingConflictException($conflicts);
            }

            $booking = Booking::create([
                'user_id'       => $user->id,
                'bookable_type' => $data['bookable_type'],
                'bookable_id'   => $data['bookable_id'],
                'title'         => $data['title'],
                'start_time'    => $start,
                'end_time'      => $end,
                'purpose'       => $data['purpose'] ?? null,
                'status'        => 'pending',
                'notes'         => $data['notes'] ?? null,
            ]);

            if (!empty($data['equipment'])) {
                $sync = [];
                foreach ($data['equipment'] as $item) {
                    $sync[$item['id']] = ['quantity' => $item['quantity'] ?? 1];
                }
                $booking->equipment()->sync($sync);
            }

            $this->scheduleReminder($booking);

            $admins = User::role('admin')->get();
            Notification::send(
                collect([$user])->merge($admins)->unique('id'),
                new BookingCreatedNotification($booking)
            );

            return $booking->load('bookable', 'user');
        });
    }

    public function approveBooking(Booking $booking, User $approver, ?string $notes = null): Booking
    {
        $booking->update([
            'status'      => 'approved',
            'approved_by' => $approver->id,
            'notes'       => $notes ?? $booking->notes,
        ]);

        $booking->user->notify(new BookingStatusChangedNotification($booking));

        return $booking->fresh();
    }

    public function rejectBooking(Booking $booking, User $approver, string $notes): Booking
    {
        $booking->update([
            'status'      => 'rejected',
            'approved_by' => $approver->id,
            'notes'       => $notes,
        ]);

        $booking->user->notify(new BookingStatusChangedNotification($booking));

        return $booking->fresh();
    }

    public function cancelBooking(Booking $booking, User $actor): Booking
    {
        $booking->update(['status' => 'cancelled']);

        return $booking->fresh();
    }

    public function scheduleReminder(Booking $booking): void
    {
        $delay = $booking->start_time->subHours(24);

        if ($delay->isFuture()) {
            SendBookingReminderJob::dispatch($booking)->delay($delay);
        }
    }

    public function getBookingsForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Booking::with(['bookable', 'user', 'approver'])
            ->when(!$user->hasRole('admin'), fn ($q) => $q->forUser($user->id))
            ->when(!empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['date_from']), fn ($q) => $q->where('start_time', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn ($q) => $q->where('start_time', '<=', $filters['date_to'] . ' 23:59:59'))
            ->when(!empty($filters['search']), fn ($q) => $q->where('title', 'like', '%' . $filters['search'] . '%'))
            ->latest('start_time');

        return $query->paginate(15)->withQueryString();
    }
}
