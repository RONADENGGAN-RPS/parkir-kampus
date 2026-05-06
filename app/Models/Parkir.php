<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parkir extends Model
{
    protected $fillable = [
        'kendaraan_id',
        'user_id',
        'petugas_id',
        'check_in',
        'check_out',
        'durasi',
        'status',
        'scan_device_info',
        'qr_data_hash',
        'duplicate_attempt',
    ];
    
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'scan_device_info' => 'array',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
