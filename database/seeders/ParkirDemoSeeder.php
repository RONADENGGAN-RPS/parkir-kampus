<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parkir;
use App\Models\Kendaraan;
use Carbon\Carbon;

class ParkirDemoSeeder extends Seeder
{
    public function run()
    {
        $kendaraan = Kendaraan::first(); // ambil kendaraan pertama yang ada
        if (!$kendaraan) {
            $this->command->warn('Tidak ada kendaraan, lewati.');
            return;
        }

        $petugasId = \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'petugas'))->value('id');
        if (!$petugasId) {
            $this->command->warn('Tidak ada petugas, lewati.');
            return;
        }

        // Data check‑in beberapa hari terakhir untuk dashboard
        for ($i = 4; $i >= 0; $i--) {
            $checkIn = Carbon::today()->subDays($i)->setTime(rand(7, 18), rand(0, 59));
            $checkOut = $i > 0 ? $checkIn->copy()->addMinutes(rand(30, 180)) : null;
            Parkir::create([
                'kendaraan_id'   => $kendaraan->id,
                'user_id'        => $kendaraan->user_id,
                'petugas_id'     => $petugasId,
                'check_in'       => $checkIn,
                'check_out'      => $checkOut,
                'durasi'         => $checkOut ? $checkIn->diffInMinutes($checkOut) : null,
                'status'         => $checkOut ? 'completed' : 'active',
                'scan_device_info' => json_encode(['ip' => '127.0.0.1']),
            ]);
        }

        $this->command->info('Demo parkir berhasil ditambahkan!');
    }
}