<?php

namespace App\Http\Controllers;

use App\Models\Parkir;
use App\Models\Kendaraan;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        // Hanya admin & superadmin yang bisa akses
        if (!in_array(auth()->user()->role->slug, ['admin', 'superadmin'])) {
            abort(403);
        }
    }

    public function dashboard()
    {
        $today = Carbon::today();

        // Statistik hari ini
        $totalCheckin = Parkir::whereDate('check_in', $today)->count();
        $totalCheckout = Parkir::whereDate('check_out', $today)->count();
        $parkirAktif = Parkir::whereNull('check_out')->count();
        $totalKendaraan = Kendaraan::count();
        $pelanggaran = Parkir::whereDate('check_in', $today)
            ->where('status', 'violation')->count();

        // Parkir aktif terbaru
        $parkirAktifList = Parkir::with('kendaraan.user')
            ->whereNull('check_out')
            ->latest('check_in')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalCheckin',
            'totalCheckout',
            'parkirAktif',
            'totalKendaraan',
            'pelanggaran',
            'parkirAktifList'
        ));
    }
}
