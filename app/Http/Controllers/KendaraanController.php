<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\User;
use App\Models\Setting;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class KendaraanController extends Controller
{
    public function index()
    {
        $kendaraans = Kendaraan::with('user')->latest()->get();
        $mahasiswas = User::whereHas('role', fn($q) => $q->where('slug', 'mahasiswa'))->get();
        return view('kendaraan.index', compact('kendaraans', 'mahasiswas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'plat_nomor' => 'required|unique:kendaraans',
            'tipe'       => 'required|in:motor,mobil',
            'merk'       => 'required',
            'warna'      => 'required',
        ]);

        // Enkripsi token
        $encryptedToken = Crypt::encryptString($validated['plat_nomor']);

        // Masa berlaku QR (default 365 hari)
        $expiryDays = (int) (Setting::where('key', 'qr_expiry_days')->value('value') ?? 365);
        $expiryTimestamp = now()->addDays($expiryDays)->timestamp;

        // Hash anti-pemalsuan
        $secret = config('app.qr_secret_key', 'default-secret-change-me');
        $hash = hash_hmac('sha256', $encryptedToken . $expiryTimestamp, $secret);

        // Data yang akan disimpan
        $validated['qr_token']      = $encryptedToken;
        $validated['qr_expired_at'] = now()->addDays($expiryDays);
        $validated['qr_code_hash']  = $hash;
        $validated['status']        = true;
        $validated['created_by']    = auth()->id();

        $kendaraan = Kendaraan::create($validated);

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'action'      => 'create',
            'module'      => 'kendaraan',
            'description' => 'Menambah kendaraan ' . $validated['plat_nomor'],
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'data'        => json_encode($validated),
        ]);

        return response()->json(['success' => true, 'message' => 'Kendaraan berhasil ditambahkan.']);
    }

    public function edit(Kendaraan $kendaraan)
    {
        return response()->json($kendaraan);
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $validated = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'plat_nomor' => 'required|unique:kendaraans,plat_nomor,' . $kendaraan->id,
            'tipe'       => 'required|in:motor,mobil',
            'merk'       => 'required',
            'warna'      => 'required',
        ]);

        $oldPlat = $kendaraan->plat_nomor; // untuk log
        $kendaraan->update($validated);

        // Catat log aktivitas update
        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'action'      => 'update',
            'module'      => 'kendaraan',
            'description' => 'Mengubah kendaraan ' . $oldPlat . ' menjadi ' . $validated['plat_nomor'],
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'data'        => json_encode($validated),
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $plat = $kendaraan->plat_nomor;
        $kendaraan->delete();

        // Catat log aktivitas delete
        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'action'      => 'delete',
            'module'      => 'kendaraan',
            'description' => 'Menghapus kendaraan ' . $plat,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'data'        => json_encode(['plat_nomor' => $plat]),
        ]);

        return response()->json(['success' => true, 'message' => 'Kendaraan dihapus.']);
    }

    public function toggleStatus(Kendaraan $kendaraan)
    {
        $kendaraan->status = !$kendaraan->status;
        $kendaraan->save();

        // Catat log aktivitas status
        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'action'      => $kendaraan->status ? 'activate' : 'deactivate',
            'module'      => 'kendaraan',
            'description' => 'Mengubah status kendaraan ' . $kendaraan->plat_nomor . ' menjadi ' . ($kendaraan->status ? 'aktif' : 'nonaktif'),
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'data'        => json_encode(['status' => $kendaraan->status]),
        ]);

        return response()->json([
            'success' => true,
            'status' => $kendaraan->status,
            'message' => $kendaraan->status ? 'Kendaraan diaktifkan.' : 'Kendaraan dinonaktifkan.'
        ]);
    }

    public function qr(Kendaraan $kendaraan)
    {
        // Mahasiswa hanya boleh lihat QR kendaraan miliknya
        if (auth()->user()->role->slug === 'mahasiswa' && $kendaraan->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil secret key dari config, fallback default
        $secret = config('app.qr_secret_key', 'default-secret-change-me');

        // Ambil masa expired dari settings, default 365 hari
        $expiryDays = (int) (Setting::where('key', 'qr_expiry_days')->value('value') ?? 365);

        // Buat token terenkripsi: gabung plat_nomor + id kendaraan
        $tokenData = $kendaraan->plat_nomor . '|' . $kendaraan->id;
        $encryptedToken = encrypt($tokenData);

        // Tentukan timestamp expired
        $expiryTimestamp = now()->addDays($expiryDays)->timestamp;

        // Buat hash untuk anti-fake
        $hash = hash_hmac('sha256', $encryptedToken . $expiryTimestamp, $secret);

        // Gabung jadi satu string QR
        $qrData = $encryptedToken . '|' . $expiryTimestamp . '|' . $hash;

        // Simpan ke database (optional, untuk referensi)
        $kendaraan->updateQuietly([
            'qr_token'      => $encryptedToken,
            'qr_expired_at' => now()->addDays($expiryDays),
            'qr_code_hash'  => $hash,
        ]);

        return response()->json([
            'success' => true,
            'qr_data' => $qrData,
            'plat'    => $kendaraan->plat_nomor,
        ]);
    }
}