<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'destination_id','passenger_count', 'description', 'price', 'duration', 'image', 'status'
    ];

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function destination(): BelongsTo {
        return $this->belongsTo(Destination::class);
    }

    public function bookings(): HasMany {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany {
        return $this->hasMany(Review::class);
    }
}
