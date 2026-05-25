<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Role::firstOrCreate(['name' => 'lecturer', 'guard_name' => 'web']);
    $this->service = app(ReportService::class);
    $this->from    = Carbon::today()->startOfMonth();
    $this->to      = Carbon::today()->endOfMonth();
});

test('bookingSummary returns correct counts per status', function () {
    $user = User::factory()->create();
    $user->assignRole('lecturer');

    Booking::factory()->pending()->count(2)->create(['user_id' => $user->id, 'start_time' => now(), 'end_time' => now()->addHour()]);
    Booking::factory()->approved()->count(3)->create(['user_id' => $user->id, 'start_time' => now(), 'end_time' => now()->addHour()]);
    Booking::factory()->create(['user_id' => $user->id, 'status' => 'rejected', 'start_time' => now(), 'end_time' => now()->addHour()]);

    $summary = $this->service->bookingSummary($this->from, $this->to);

    expect($summary['total'])->toBe(6)
        ->and($summary['pending'])->toBe(2)
        ->and($summary['approved'])->toBe(3)
        ->and($summary['rejected'])->toBe(1)
        ->and($summary['cancelled'])->toBe(0);
});

test('roomUtilization returns entry for every room', function () {
    Room::factory()->count(3)->create();

    $results = $this->service->roomUtilization($this->from, $this->to);

    expect($results->count())->toBeGreaterThanOrEqual(3);
    expect($results->first())->toHaveKeys(['room_id', 'name', 'booked_hours', 'utilization_pct']);
});

test('roomUtilization calculates booked hours from approved bookings', function () {
    $room = Room::factory()->create();
    $user = User::factory()->create();

    Booking::factory()->forRoom($room)->approved()->create([
        'user_id'    => $user->id,
        'start_time' => now()->setHour(9)->setMinute(0)->setSecond(0),
        'end_time'   => now()->setHour(11)->setMinute(0)->setSecond(0),
    ]);

    $results  = $this->service->roomUtilization($this->from, $this->to);
    $roomData = $results->firstWhere('room_id', $room->id);

    expect($roomData['booked_hours'])->toBe(2.0);
});

test('roomUtilization ignores non-approved bookings', function () {
    $room = Room::factory()->create();
    $user = User::factory()->create();

    Booking::factory()->forRoom($room)->create([
        'user_id'    => $user->id,
        'status'     => 'pending',
        'start_time' => now()->setHour(9),
        'end_time'   => now()->setHour(11),
    ]);

    $results  = $this->service->roomUtilization($this->from, $this->to);
    $roomData = $results->firstWhere('room_id', $room->id);

    expect($roomData['booked_hours'])->toBe(0.0);
});

test('topUsers returns users ordered by booking count', function () {
    $heavy = User::factory()->create();
    $light = User::factory()->create();

    Booking::factory()->count(5)->create(['user_id' => $heavy->id, 'status' => 'approved', 'start_time' => now(), 'end_time' => now()->addHour()]);
    Booking::factory()->count(2)->create(['user_id' => $light->id, 'status' => 'approved', 'start_time' => now(), 'end_time' => now()->addHour()]);

    $results = $this->service->topUsers($this->from, $this->to, 10);

    expect($results->first()->user_id)->toBe($heavy->id)
        ->and($results->first()->count)->toBe(5);
});

test('exportCsv returns valid csv string for summary report', function () {
    $user = User::factory()->create();
    Booking::factory()->approved()->create(['user_id' => $user->id, 'start_time' => now(), 'end_time' => now()->addHour()]);

    $csv = $this->service->exportCsv('summary', $this->from, $this->to);

    expect($csv)->toContain('total')
        ->and($csv)->toContain('approved');
});

test('exportCsv returns empty string for unknown report type', function () {
    $csv = $this->service->exportCsv('unknown_type', $this->from, $this->to);

    expect($csv)->toBe('');
});
