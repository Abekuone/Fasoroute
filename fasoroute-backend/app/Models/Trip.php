<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'status',
        'departure_location',
        'departure_location_precise',
        'destination',
        'destination_precise',
        'route',
        'phone_number',
        'departure_time',
        'available_seats',
        'price_per_passenger',
        'is_return_trip',
        'return_departure_location',
        'return_departure_location_precise',
        'return_destination',
        'return_destination_precise',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}

