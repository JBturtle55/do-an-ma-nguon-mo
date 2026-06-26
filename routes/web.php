<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEquipmentController;
use App\Http\Controllers\Admin\AdminMaintenanceController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleRedirectController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', RoleRedirectController::class)->middleware('auth')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications/stream', [NotificationController::class, 'stream'])->name('notifications.stream');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Dashboard stats (JSON — for realtime refresh)
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Calendar JSON endpoints (same-origin fetch — need session auth so placed in web routes)
    Route::prefix('api/calendar')->name('api.calendar.')->group(function () {
        Route::get('/events', [CalendarController::class, 'events'])->name('events');
        Route::get('/room/{room}/events', [CalendarController::class, 'roomEvents'])->name('room-events');
        Route::get('/my-events', [CalendarController::class, 'myEvents'])->name('my-events');
    });

    // Availability check (same-origin fetch from booking form — needs session auth)
    Route::get('/api/availability/check', [\App\Http\Controllers\Api\AvailabilityController::class, 'check'])->name('api.availability.check');

    // Rooms (read-only)
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/schedule', [RoomController::class, 'schedule'])->name('rooms.schedule');

    // Equipment (read-only)
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/dashboard/pending', [AdminDashboardController::class, 'pending'])->name('dashboard.pending');

    // Users
    Route::resource('users', AdminUserController::class)->except(['show']);

    // Rooms
    Route::resource('rooms', AdminRoomController::class);

    // Equipment
    Route::resource('equipment', AdminEquipmentController::class);

    // Bookings
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
    Route::patch('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');

    // Schedules
    Route::resource('schedules', AdminScheduleController::class)->except(['show']);

    // Maintenance
    Route::get('/maintenance', [AdminMaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [AdminMaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [AdminMaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{log}', [AdminMaintenanceController::class, 'show'])->name('maintenance.show');
    Route::patch('/maintenance/{log}/progress', [AdminMaintenanceController::class, 'progress'])->name('maintenance.progress');
    Route::patch('/maintenance/{log}/resolve', [AdminMaintenanceController::class, 'resolve'])->name('maintenance.resolve');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';
