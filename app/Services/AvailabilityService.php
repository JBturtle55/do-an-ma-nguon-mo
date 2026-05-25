<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function isAvailable(string $bookableType, int $bookableId, Carbon $start, Carbon $end): bool
    {
        return $this->getConflicts($bookableType, $bookableId, $start, $end)->isEmpty();
    }

    public function getConflicts(string $bookableType, int $bookableId, Carbon $start, Carbon $end): Collection
    {
        return Booking::conflicting($bookableType, $bookableId, $start, $end)->get();
    }

    /**
     * Returns available time slots of given duration for a bookable on a specific date.
     *
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    public function getAvailableSlots(
        string $bookableType,
        int $bookableId,
        Carbon $date,
        int $slotMinutes = 60,
        int $dayStartHour = 7,
        int $dayEndHour = 21
    ): array {
        $dayStart = $date->copy()->setTime($dayStartHour, 0);
        $dayEnd   = $date->copy()->setTime($dayEndHour, 0);

        $existingBookings = Booking::where('bookable_type', $bookableType)
            ->where('bookable_id', $bookableId)
            ->whereDate('start_time', $date->toDateString())
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);

        $scheduleBlocks = [];
        if ($bookableType === \App\Models\Room::class) {
            $dayOfWeek = (int) $date->format('w');
            $scheduleBlocks = Schedule::where('room_id', $bookableId)
                ->where(function ($q) use ($dayOfWeek) {
                    $q->where('recurring_type', 'daily')
                      ->orWhere(function ($q2) use ($dayOfWeek) {
                          $q2->where('recurring_type', 'weekly')
                             ->where('day_of_week', $dayOfWeek);
                      });
                })
                ->get(['start_time', 'end_time']);
        }

        $slots = [];
        $current = $dayStart->copy();

        while ($current->copy()->addMinutes($slotMinutes)->lte($dayEnd)) {
            $slotEnd = $current->copy()->addMinutes($slotMinutes);
            $blocked = false;

            foreach ($existingBookings as $booking) {
                if ($current->lt($booking->end_time) && $slotEnd->gt($booking->start_time)) {
                    $blocked = true;
                    break;
                }
            }

            if (!$blocked) {
                foreach ($scheduleBlocks as $schedule) {
                    $schedStart = $date->copy()->setTimeFromTimeString($schedule->start_time);
                    $schedEnd   = $date->copy()->setTimeFromTimeString($schedule->end_time);
                    if ($current->lt($schedEnd) && $slotEnd->gt($schedStart)) {
                        $blocked = true;
                        break;
                    }
                }
            }

            if (!$blocked) {
                $slots[] = ['start' => $current->copy(), 'end' => $slotEnd];
            }

            $current->addMinutes(30);
        }

        return $slots;
    }

    public function isBlockedBySchedule(int $roomId, Carbon $start, Carbon $end): bool
    {
        $dayOfWeek = (int) $start->format('w');

        return Schedule::where('room_id', $roomId)
            ->where(function ($q) use ($dayOfWeek) {
                $q->where('recurring_type', 'daily')
                  ->orWhere(function ($q2) use ($dayOfWeek) {
                      $q2->where('recurring_type', 'weekly')
                         ->where('day_of_week', $dayOfWeek);
                  });
            })
            ->get()
            ->contains(function ($schedule) use ($start, $end) {
                $schedStart = $start->copy()->setTimeFromTimeString($schedule->start_time);
                $schedEnd   = $start->copy()->setTimeFromTimeString($schedule->end_time);
                return $start->lt($schedEnd) && $end->gt($schedStart);
            });
    }
}
