<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pengajuan;
use App\Models\User;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'pengajuan_id',
        'user_id',
        'total',
        'status', // misal: 'pending', 'sent', 'paid'
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
