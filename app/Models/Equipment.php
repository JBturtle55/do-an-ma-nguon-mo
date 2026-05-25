<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'room_id',
        'quantity',
        'status',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    public function maintenanceLogs(): MorphMany
    {
        return $this->morphMany(MaintenanceLog::class, 'loggable');
    }

    public function bookingPivots(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_equipment')
            ->withPivot('quantity')
            ->using(BookingEquipment::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
