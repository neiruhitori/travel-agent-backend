<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'user_id', 'amount', 'payment_method', 'status', 'payment_date'
    ];

    public function booking(): BelongsTo {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // public function transaction(): HasOne {
    //     return $this->hasOne(Transaction::class);
    // }

}
