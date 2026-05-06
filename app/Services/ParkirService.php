<?php
namespace App\Services;

use App\Models\Kendaraan;
use App\Models\Parkir;
use Illuminate\Support\Facades\Hash;

class ParkirService
{
    public function processQr(string $qrData, array $deviceInfo): array
    {
        // Decode QR (dummy, nanti implementasi enkripsi)
        $kendaraanId = $qrData; // sementara
        $kendaraan = Kendaraan::findOrFail($kendaraanId);
        if (!$kendaraan) return ['status' => 'error', 'message' => 'Kendaraan tidak ditemukan'];

        $activeParkir = Parkir::where('kendaraan_id', $kendaraan->id)
            ->where('status', 'active')->first();

        if ($activeParkir) {
            // Check-out
            $activeParkir->update([
                'check_out' => now(),
                'status' => 'completed',
                'durasi' => now()->diffInMinutes($activeParkir->check_in),
            ]);
            return ['status' => 'success', 'action' => 'checkout'];
        } else {
            // Check-in
            Parkir::create([
                'kendaraan_id' => $kendaraan->id,
                'user_id' => $kendaraan->user_id,
                'petugas_id' => auth()->id() ?? null,
                'check_in' => now(),
                'status' => 'active',
                'scan_device_info' => $deviceInfo,
            ]);
            return ['status' => 'success', 'action' => 'checkin'];
        }
    }
}