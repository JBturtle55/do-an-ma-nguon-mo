<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Role::firstOrCreate(['name' => 'lecturer', 'guard_name' => 'web']);
    $this->service = app(AvailabilityService::class);
});

test('isAvailable returns true when no bookings exist', function () {
    $room = Room::factory()->create();

    $result = $this->service->isAvailable(
        Room::class,
        $room->id,
        Carbon::tomorrow()->setHour(9),
        Carbon::tomorrow()->setHour(11),
    );

    expect($result)->toBeTrue();
});

test('isAvailable returns false when overlapping approved booking exists', function () {
    $room = Room::factory()->create();
    $user = User::factory()->create();

    Booking::factory()->forRoom($room)->approved()->create([
        'user_id'    => $user->id,
        'start_time' => Carbon::tomorrow()->setHour(9),
        'end_time'   => Carbon::tomorrow()->setHour(11),
    ]);

    $result = $this->service->isAvailable(
        Room::class,
        $room->id,
        Carbon::tomorrow()->setHour(10),
        Carbon::tomorrow()->setHour(12),
    );

    expect($result)->toBeFalse();
});

test('isAvailable returns true for adjacent bookings', function () {
    $room = Room::factory()->create();
    $user = User::factory()->create();

    Booking::factory()->forRoom($room)->approved()->create([
        'user_id'    => $user->id,
        'start_time' => Carbon::tomorrow()->setHour(9),
        'end_time'   => Carbon::tomorrow()->setHour(11),
    ]);

    $result = $this->service->isAvailable(
        Room::class,
        $room->id,
        Carbon::tomorrow()->setHour(11),
        Carbon::tomorrow()->setHour(13),
    );

    expect($result)->toBeTrue();
});

test('isAvailable ignores rejected bookings', function () {
    $room = Room::factory()->create();
    $user = User::factory()->create();

    Booking::factory()->forRoom($room)->create([
        'user_id'    => $user->id,
        'status'     => 'rejected',
        'start_time' => Carbon::tomorrow()->setHour(9),
        'end_time'   => Carbon::tomorrow()->setHour(11),
    ]);

    $result = $this->service->isAvailable(
        Room::class,
        $room->id,
        Carbon::tomorrow()->setHour(9),
        Carbon::tomorrow()->setHour(11),
    );

    expect($result)->toBeTrue();
});

test('isAvailable ignores cancelled bookings', function () {
    $room = Room::factory()->create();
    $user = User::factory()->create();

    Booking::factory()->forRoom($room)->create([
        'user_id'    => $user->id,
        'status'     => 'cancelled',
        'start_time' => Carbon::tomorrow()->setHour(9),
        'end_time'   => Carbon::tomorrow()->setHour(11),
    ]);

    $result = $this->service->isAvailable(
        Room::class,
        $room->id,
        Carbon::tomorrow()->setHour(9),
        Carbon::tomorrow()->setHour(11),
    );

    expect($result)->toBeTrue();
});

test('getConflicts returns all overlapping active bookings', function () {
    $room = Room::factory()->create();
    $user = User::factory()->create();

    Booking::factory()->forRoom($room)->approved()->create([
        'user_id'    => $user->id,
        'start_time' => Carbon::tomorrow()->setHour(9),
        'end_time'   => Carbon::tomorrow()->setHour(11),
    ]);
    Booking::factory()->forRoom($room)->create([
        'user_id'    => $user->id,
        'status'     => 'pending',
        'start_time' => Carbon::tomorrow()->setHour(10),
        'end_time'   => Carbon::tomorrow()->setHour(12),
    ]);
    Booking::factory()->forRoom($room)->create([
        'user_id'    => $user->id,
        'status'     => 'cancelled',
        'start_time' => Carbon::tomorrow()->setHour(9),
        'end_time'   => Carbon::tomorrow()->setHour(11),
    ]);

    $conflicts = $this->service->getConflicts(
        Room::class,
        $room->id,
        Carbon::tomorrow()->setHour(10)->setMinute(30),
        Carbon::tomorrow()->setHour(13),
    );

    expect($conflicts)->toHaveCount(2); // approved + pending, not cancelled
});

test('getAvailableSlots returns slots not blocked by bookings', function () {
    $room = Room::factory()->create();
    $user = User::factory()->create();

    // Block 09:00–11:00
    Booking::factory()->forRoom($room)->approved()->create([
        'user_id'    => $user->id,
        'start_time' => Carbon::tomorrow()->setHour(9)->setMinute(0)->setSecond(0),
        'end_time'   => Carbon::tomorrow()->setHour(11)->setMinute(0)->setSecond(0),
    ]);

    $slots = $this->service->getAvailableSlots(
        Room::class,
        $room->id,
        Carbon::tomorrow(),
        120, // 2h slots
        7,
        13   // 07:00–13:00 window to keep test fast
    );

    // 07:00–09:00 should be available, 09:00–11:00 blocked, 11:00–13:00 should be available
    $startTimes = array_map(fn ($s) => $s['start']->format('H:i'), $slots);
    expect($startTimes)->toContain('07:00')
        ->and($startTimes)->toContain('11:00')
        ->and($startTimes)->not->toContain('09:00');
});

test('isBlockedBySchedule returns true when weekly schedule covers time slot', function () {
    $room = Room::factory()->create();
    $tomorrow = Carbon::tomorrow();
    $dayOfWeek = (int) $tomorrow->format('w');

    Schedule::factory()->create([
        'room_id'        => $room->id,
        'recurring_type' => 'weekly',
        'day_of_week'    => $dayOfWeek,
        'start_time'     => '09:00:00',
        'end_time'       => '11:00:00',
    ]);

    $result = $this->service->isBlockedBySchedule(
        $room->id,
        $tomorrow->copy()->setHour(10),
        $tomorrow->copy()->setHour(12),
    );

    expect($result)->toBeTrue();
});

test('isBlockedBySchedule returns false when schedule is on a different day', function () {
    $room      = Room::factory()->create();
    $tomorrow  = Carbon::tomorrow();
    $otherDay  = ($tomorrow->dayOfWeek + 2) % 7;

    Schedule::factory()->create([
        'room_id'        => $room->id,
        'recurring_type' => 'weekly',
        'day_of_week'    => $otherDay,
        'start_time'     => '09:00:00',
        'end_time'       => '11:00:00',
    ]);

    $result = $this->service->isBlockedBySchedule(
        $room->id,
        $tomorrow->copy()->setHour(9),
        $tomorrow->copy()->setHour(11),
    );

    expect($result)->toBeFalse();
});
