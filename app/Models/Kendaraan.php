<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'plat_nomor', 'tipe', 'merk', 'warna',
        'qr_code_hash', 'qr_token', 'qr_expired_at', 'status', 'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}