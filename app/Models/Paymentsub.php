<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pengajuan;

class Paymentsub extends Model
{
    protected $table = 'paymentsub';

    protected $fillable = [
        'pengajuan_id',
        'paid_at',
        'amount_paid',
        'method',
        'path_file',
        'verified_by',
        'verified_at',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }
}
