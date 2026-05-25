<?php

use App\Exceptions\BookingConflictException;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Role::firstOrCreate(['name' => 'admin',    'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'lecturer', 'guard_name' => 'web']);

    Notification::fake();
    Queue::fake();

    $this->service = app(BookingService::class);
});

test('createBooking creates a pending booking and returns it loaded', function () {
    $user = User::factory()->create();
    $user->assignRole('lecturer');
    $room = Room::factory()->create();

    $booking = $this->service->createBooking($user, [
        'bookable_type' => Room::class,
        'bookable_id'   => $room->id,
        'title'         => 'Service Test Booking',
        'start_time'    => now()->addDay()->setHour(9)->setMinute(0)->setSecond(0),
        'end_time'      => now()->addDay()->setHour(11)->setMinute(0)->setSecond(0),
        'purpose'       => 'Unit testing',
    ]);

    expect($booking->status)->toBe('pending')
        ->and($booking->title)->toBe('Service Test Booking')
        ->and($booking->relationLoaded('bookable'))->toBeTrue()
        ->and($booking->relationLoaded('user'))->toBeTrue();

    $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'pending']);
});

test('createBooking throws BookingConflictException when overlap exists', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();

    Booking::factory()->forRoom($room)->approved()->create([
        'user_id'    => $user->id,
        'start_time' => now()->addDay()->setHour(9),
        'end_time'   => now()->addDay()->setHour(11),
    ]);

    expect(fn () => $this->service->createBooking($user, [
        'bookable_type' => Room::class,
        'bookable_id'   => $room->id,
        'title'         => 'Conflicting',
        'start_time'    => now()->addDay()->setHour(10),
        'end_time'      => now()->addDay()->setHour(12),
    ]))->toThrow(BookingConflictException::class);
});

test('createBooking sends notifications to creator and admins', function () {
    $admin   = User::factory()->create();
    $admin->assignRole('admin');
    $user    = User::factory()->create();
    $user->assignRole('lecturer');
    $room    = Room::factory()->create();

    $this->service->createBooking($user, [
        'bookable_type' => Room::class,
        'bookable_id'   => $room->id,
        'title'         => 'Notify Test',
        'start_time'    => now()->addDay()->setHour(9),
        'end_time'      => now()->addDay()->setHour(11),
    ]);

    Notification::assertSentTo(
        [$user, $admin],
        \App\Notifications\BookingCreatedNotification::class
    );
});

test('approveBooking sets status to approved and notifies user', function () {
    $admin   = User::factory()->create();
    $admin->assignRole('admin');
    $user    = User::factory()->create();
    $user->assignRole('lecturer');
    $booking = Booking::factory()->pending()->create(['user_id' => $user->id]);

    $updated = $this->service->approveBooking($booking, $admin);

    expect($updated->status)->toBe('approved')
        ->and($updated->approved_by)->toBe($admin->id);

    Notification::assertSentTo($user, \App\Notifications\BookingStatusChangedNotification::class);
});

test('rejectBooking sets status to rejected with notes', function () {
    $admin   = User::factory()->create();
    $admin->assignRole('admin');
    $user    = User::factory()->create();
    $user->assignRole('lecturer');
    $booking = Booking::factory()->pending()->create(['user_id' => $user->id]);

    $updated = $this->service->rejectBooking($booking, $admin, 'Phòng đã được sử dụng.');

    expect($updated->status)->toBe('rejected')
        ->and($updated->notes)->toBe('Phòng đã được sử dụng.');
});

test('cancelBooking sets status to cancelled', function () {
    $user    = User::factory()->create();
    $booking = Booking::factory()->pending()->create(['user_id' => $user->id]);

    $updated = $this->service->cancelBooking($booking, $user);

    expect($updated->status)->toBe('cancelled');
    $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
});

test('getBookingsForUser paginates and filters by status', function () {
    $user = User::factory()->create();
    $user->assignRole('lecturer');

    Booking::factory()->pending()->count(3)->create(['user_id' => $user->id]);
    Booking::factory()->approved()->count(2)->create(['user_id' => $user->id]);

    $results = $this->service->getBookingsForUser($user, ['status' => 'pending']);

    expect($results->total())->toBe(3);
});

test('admin sees all bookings regardless of owner', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $other = User::factory()->create();

    Booking::factory()->count(5)->create(['user_id' => $other->id]);

    $results = $this->service->getBookingsForUser($admin);

    expect($results->total())->toBe(5);
});
