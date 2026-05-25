<?php

use App\Http\Controllers\Api\AvailabilityController;
use Illuminate\Support\Facades\Route;

Route::get('/availability/check', [AvailabilityController::class, 'check'])->name('api.availability.check');
