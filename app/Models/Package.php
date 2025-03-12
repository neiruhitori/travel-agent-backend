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
        'name', 'destination_id', 'description', 'price', 'duration', 'image'
    ];

    public function destination(): BelongsTo {
        return $this->belongsTo(Destination::class);
    }

}
