<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'capacity',
        'license_plate',
        'status',
        'description'
    ];

    /**
     * The possible vehicle types.
     */
    public const TYPES = [
        'sedan',
        'suv',
        'mpv',
        'bus',
        'truck',
        'motorcycle', 
        'van'
    ];

    /**
     * The possible vehicle statuses.
     */
    public const STATUSES = [
        'available',
        'in_use',
        'maintenance'
    ];

    /**
     * Get the bookings for the vehicle.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

