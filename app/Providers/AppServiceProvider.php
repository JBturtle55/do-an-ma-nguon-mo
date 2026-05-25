<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Room;
use App\Policies\BookingPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\RoomPolicy;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use App\Services\ReportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AvailabilityService::class);
        $this->app->singleton(ReportService::class);
        $this->app->singleton(BookingService::class, fn ($app) =>
            new BookingService($app->make(AvailabilityService::class))
        );
    }

    public function boot(): void
    {
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(Equipment::class, EquipmentPolicy::class);
    }
}
