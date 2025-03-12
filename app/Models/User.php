<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'role'
    ];

    protected $hidden = ['password'];

    public function bookings(): HasMany {
        return $this->hasMany(Booking::class);
    }

    public function payments(): HasMany {
        return $this->hasMany(Payment::class);
    }

    public function reviews(): HasMany {
        return $this->hasMany(Review::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }
}
