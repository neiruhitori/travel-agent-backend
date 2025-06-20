<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'description', 'price', 'image'
    ];

    public function packages(): HasMany {
        return $this->hasMany(Package::class);
    }
}
