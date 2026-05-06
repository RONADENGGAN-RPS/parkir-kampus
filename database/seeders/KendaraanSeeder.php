<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kendaraan;
use App\Models\User;

class KendaraanSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('role_id', 4)->first(); // mahasiswa
        if (!$user) {
            $user = User::first(); // fallback
        }

        Kendaraan::create([
            'user_id' => $user->id,
            'plat_nomor' => 'B ' . rand(1000,9999) . ' XYZ',
            'tipe' => 'mobil',
            'merk' => 'Honda',
            'warna' => 'Putih',
        ]);
        Kendaraan::create([
            'user_id' => $user->id,
            'plat_nomor' => 'B ' . rand(1000,9999) . ' ABC',
            'tipe' => 'motor',
            'merk' => 'Yamaha',
            'warna' => 'Hitam',
        ]);
    }
}