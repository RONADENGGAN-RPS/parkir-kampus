<?php

namespace App\Services;

use App\Models\Kendaraan;
use App\Models\Parkir;
use App\Models\LogAktivitas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class QrScanService
{
    /**
     * Proses scan QR code.
     *
     * @param string $qrRawData  Data mentah dari QR (format: token|expiry|hash)
     * @param array  $deviceInfo Informasi perangkat (ip, user agent, dll)
     * @return array
     */
    public function process(string $qrRawData, array $deviceInfo): array
    {
        // 1. Validasi format data (token|expiry|hash)
        $parts = explode('|', $qrRawData);
        if (count($parts) !== 3) {
            return $this->response(false, 'Format QR tidak valid.');
        }

        [$encryptedToken, $expiryTimestamp, $receivedHash] = $parts;

        // 2. Cek expiry
        if (time() > (int)$expiryTimestamp) {
            return $this->response(false, 'QR Code sudah kedaluwarsa.');
        }

        // 3. Cek hash untuk anti-fake
        $secret = config('app.qr_secret_key', 'default-secret-change-me');
        $expectedHash = hash_hmac('sha256', $encryptedToken . $expiryTimestamp, $secret);
        if (!hash_equals($expectedHash, $receivedHash)) {
            return $this->response(false, 'QR Code tidak sah (hash tidak cocok).');
        }

        // 4. Dekripsi token menjadi plat nomor (atau ID kendaraan)
        try {
            $decrypted = Crypt::decryptString($encryptedToken);
            // Token format baru: plat_nomor|id_kendaraan
            $parts = explode('|', $decrypted);
            $platNomor = $parts[0] ?? '';
            $kendaraanId = $parts[1] ?? null;
        } catch (\Exception $e) {
            return $this->response(false, 'Token tidak valid.');
        }

        // 5. Cari kendaraan berdasarkan ID atau plat nomor
        if ($kendaraanId) {
            $kendaraan = Kendaraan::find($kendaraanId);
        }
        if (!isset($kendaraan) || !$kendaraan) {
            $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();
        }

        // Pastikan kendaraan ditemukan dan aktif
        if (!$kendaraan || !$kendaraan->status) {
            return $this->response(false, 'Kendaraan tidak ditemukan atau tidak aktif.');
        }

        // 6. Anti-duplikasi: cek apakah QR ini sudah dipindai dalam 30 detik terakhir
        $recentParkir = Parkir::where('kendaraan_id', $kendaraan->id)
            ->where('check_in', '>=', now()->subSeconds(30))
            ->latest('id')
            ->first();
        if ($recentParkir && $recentParkir->status === 'active') {
            return $this->response(false, 'QR baru saja dipindai. Tunggu beberapa saat.');
        }

        // 7. Cek status parkir kendaraan ini
        $activeParkir = Parkir::where('kendaraan_id', $kendaraan->id)
            ->whereNull('check_out')
            ->where('status', 'active')
            ->first();

        if ($activeParkir) {
            // ========== CHECK-OUT ==========
            $durasi = now()->diffInMinutes($activeParkir->check_in);
            $maxDuration = \App\Models\Setting::where('key', 'max_parking_duration')->value('value') ?? 300;
            $status = $durasi > $maxDuration ? 'violation' : 'completed';

            $activeParkir->update([
                'check_out' => now(),
                'status'    => $status,
                'durasi'    => $durasi,
            ]);

            $action  = 'checkout';
            $message = $status === 'violation'
                ? "Check-out tercatat sebagai pelanggaran! Durasi: {$durasi} menit (batas: {$maxDuration} mnt)."
                : "Check-out berhasil. Durasi: {$durasi} menit.";

            // Catat log aktivitas checkout
            LogAktivitas::create([
                'user_id'     => auth()->id() ?? $kendaraan->user_id,
                'action'      => 'checkout',
                'module'      => 'parkir',
                'description' => $message,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
                'data'        => json_encode([
                    'plat'    => $kendaraan->plat_nomor,
                    'petugas' => auth()->user()->name ?? 'system',
                    'status'  => $status,
                ]),
            ]);
        } else {
            // ========== CHECK-IN ==========
            Parkir::create([
                'kendaraan_id'     => $kendaraan->id,
                'user_id'          => $kendaraan->user_id,
                'petugas_id'       => auth()->id() ?? null,
                'check_in'         => now(),
                'status'           => 'active',
                'scan_device_info' => $deviceInfo,
                'qr_data_hash'     => $receivedHash,
                'duplicate_attempt' => false,
            ]);
            $action  = 'checkin';
            $message = "Check-in berhasil untuk kendaraan {$kendaraan->plat_nomor}.";

            // Catat log aktivitas checkin
            LogAktivitas::create([
                'user_id'     => auth()->id() ?? $kendaraan->user_id,
                'action'      => 'checkin',
                'module'      => 'parkir',
                'description' => $message,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
                'data'        => json_encode([
                    'plat'    => $kendaraan->plat_nomor,
                    'petugas' => auth()->user()->name ?? 'system',
                ]),
            ]);
        }

        return $this->response(true, $message, [
            'action'     => $action,
            'plat_nomor' => $kendaraan->plat_nomor,
            'tipe'       => $kendaraan->tipe,
            'waktu'      => now()->format('H:i:s'),
        ]);
    }

    private function response(bool $success, string $message, array $extra = []): array
    {
        return array_merge(['success' => $success, 'message' => $message], $extra);
    }
}
