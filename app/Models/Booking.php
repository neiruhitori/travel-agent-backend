<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'package_id', 'booking_date', 'total_price', 'status'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo {
        return $this->belongsTo(Package::class);
    }

    // public function payment(): HasOne {
    //     return $this->hasOne(Payment::class);
    // }
}
