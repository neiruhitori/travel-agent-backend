<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Destination;

class Pengajuan extends Model

{
    protected $table = 'pengajuan';

    protected $fillable = [
        'institution',
        'applicant',
        'email',
        'destination_id',
        'vehicle_id',
        'departure_date',
        'return_date',
        'participants',
        'notes',
        'user_id',
        'status',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    // public function vehicle()
    // {
    //     return $this->belongsTo(Vehicle::class);
    // }

    public function paymentsub()
    {
        return $this->hasOne(Paymentsub::class, 'pengajuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getVehicleTypeAttribute()
    {
        return $this->vehicle ? $this->vehicle->type : null;
    }

    public function getDestinationLocationAttribute()
    {
        return $this->destination ? $this->destination->location : null;
    }

    // Status enum sesuai migration
    public const STATUS_ENUM = [
        'menunggu_konfirmasi',
        'menunggu_persetujuan',
        'disetujui',
        'dalam_perjalanan',
        'menunggu_pembayaran',
        'menunggu_verifikasi_pembayaran',
        'pembayaran_ditolak',
        'lunas',
        'ditolak',
    ];

    public static function getStatusEnum()
    {
        return self::STATUS_ENUM;
    }
}
