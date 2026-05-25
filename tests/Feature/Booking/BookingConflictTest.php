<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'lecturer', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

    Notification::fake();
    Queue::fake();
});

test('authenticated user can create a booking for an available room', function () {
    $user = User::factory()->create();
    $user->assignRole('lecturer');
    $room = Room::factory()->create(['status' => 'available']);

    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'title'         => 'Test Booking',
        'bookable_type' => Room::class,
        'bookable_id'   => $room->id,
        'start_time'    => now()->addDay()->setHour(9)->setMinute(0)->setSecond(0)->format('Y-m-d\TH:i'),
        'end_time'      => now()->addDay()->setHour(11)->setMinute(0)->setSecond(0)->format('Y-m-d\TH:i'),
        'purpose'       => 'Testing',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('bookings', ['title' => 'Test Booking', 'status' => 'pending']);
});

test('cannot book a room that has an overlapping approved booking', function () {
    $user  = User::factory()->create();
    $user->assignRole('lecturer');
    $room = Room::factory()->create();

    // Existing approved booking: 09:00–11:00
    Booking::factory()->forRoom($room)->approved()->create([
        'user_id'    => $user->id,
        'start_time' => now()->addDay()->setHour(9)->setMinute(0)->setSecond(0),
        'end_time'   => now()->addDay()->setHour(11)->setMinute(0)->setSecond(0),
    ]);

    // Overlapping attempt: 10:00–12:00
    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'title'         => 'Overlap Booking',
        'bookable_type' => Room::class,
        'bookable_id'   => $room->id,
        'start_time'    => now()->addDay()->setHour(10)->setMinute(0)->setSecond(0)->format('Y-m-d\TH:i'),
        'end_time'      => now()->addDay()->setHour(12)->setMinute(0)->setSecond(0)->format('Y-m-d\TH:i'),
    ]);

    $response->assertSessionHasErrors('conflict');
    $this->assertDatabaseCount('bookings', 1);
});

test('adjacent bookings do not conflict', function () {
    $user  = User::factory()->create();
    $user->assignRole('lecturer');
    $room = Room::factory()->create();

    Booking::factory()->forRoom($room)->approved()->create([
        'user_id'    => $user->id,
        'start_time' => now()->addDay()->setHour(9)->setMinute(0)->setSecond(0),
        'end_time'   => now()->addDay()->setHour(11)->setMinute(0)->setSecond(0),
    ]);

    // Adjacent: 11:00–13:00 (starts exactly when previous ends)
    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'title'         => 'Adjacent Booking',
        'bookable_type' => Room::class,
        'bookable_id'   => $room->id,
        'start_time'    => now()->addDay()->setHour(11)->setMinute(0)->setSecond(0)->format('Y-m-d\TH:i'),
        'end_time'      => now()->addDay()->setHour(13)->setMinute(0)->setSecond(0)->format('Y-m-d\TH:i'),
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseCount('bookings', 2);
});

test('rejected bookings do not block new bookings in the same time slot', function () {
    $user  = User::factory()->create();
    $user->assignRole('lecturer');
    $room = Room::factory()->create();

    Booking::factory()->forRoom($room)->create([
        'user_id'    => $user->id,
        'status'     => 'rejected',
        'start_time' => now()->addDay()->setHour(9)->setMinute(0)->setSecond(0),
        'end_time'   => now()->addDay()->setHour(11)->setMinute(0)->setSecond(0),
    ]);

    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'title'         => 'New Booking Same Slot',
        'bookable_type' => Room::class,
        'bookable_id'   => $room->id,
        'start_time'    => now()->addDay()->setHour(9)->setMinute(0)->setSecond(0)->format('Y-m-d\TH:i'),
        'end_time'      => now()->addDay()->setHour(11)->setMinute(0)->setSecond(0)->format('Y-m-d\TH:i'),
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('bookings', ['title' => 'New Booking Same Slot', 'status' => 'pending']);
});

test('user can cancel their own pending booking', function () {
    $user    = User::factory()->create();
    $user->assignRole('lecturer');
    $booking = Booking::factory()->pending()->create(['user_id' => $user->id]);

    $this->actingAs($user)->patch(route('bookings.cancel', $booking));

    $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
});

test('user cannot cancel another users booking', function () {
    $user    = User::factory()->create();
    $user->assignRole('lecturer');
    $other   = User::factory()->create();
    $other->assignRole('student');
    $booking = Booking::factory()->pending()->create(['user_id' => $other->id]);

    $response = $this->actingAs($user)->patch(route('bookings.cancel', $booking));

    $response->assertForbidden();
});

test('admin can approve a pending booking', function () {
    $admin   = User::factory()->create();
    $admin->assignRole('admin');
    $user    = User::factory()->create();
    $user->assignRole('lecturer');
    $booking = Booking::factory()->pending()->create(['user_id' => $user->id]);

    $this->actingAs($admin)->patch(route('admin.bookings.approve', $booking));

    $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'approved']);
});

test('admin can reject a booking with notes', function () {
    $admin   = User::factory()->create();
    $admin->assignRole('admin');
    $user    = User::factory()->create();
    $user->assignRole('lecturer');
    $booking = Booking::factory()->pending()->create(['user_id' => $user->id]);

    $this->actingAs($admin)->patch(route('admin.bookings.reject', $booking), [
        'notes' => 'Phòng đã được đặt bởi ban quản lý.',
    ]);

    $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'rejected']);
});
