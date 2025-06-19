<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bookingdes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'destination_id', 'vehicle_id', 'booking_date', 'jumlah_penumpang', 'total_price', 'status'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function destination(): BelongsTo {
        return $this->belongsTo(Destination::class);
    }

    public function vehicle(): BelongsTo {
        return $this->belongsTo(Vehicle::class);
    }

    public function payment(): HasOne {
        return $this->hasOne(Payment::class);
    }
}
