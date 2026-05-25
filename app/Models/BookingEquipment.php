<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingEquipment extends Pivot
{
    protected $table = 'booking_equipment';

    public $timestamps = false;

    protected $fillable = ['booking_id', 'equipment_id', 'quantity'];
}
