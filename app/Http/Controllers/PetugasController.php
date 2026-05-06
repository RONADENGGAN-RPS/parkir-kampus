<?php

namespace App\Http\Controllers;

use App\Models\Parkir;
use Carbon\Carbon;

class PetugasController extends Controller
{
    public function __construct()
    {
        if (auth()->user()->role->slug !== 'petugas') {
            abort(403);
        }
    }

    public function dashboard()
    {
        $today = Carbon::today();

        // Statistik seluruh parkir hari ini
        $parkirAktif = Parkir::whereNull('check_out')->count();
        $checkinHariIni = Parkir::whereDate('check_in', $today)->count();
        $checkoutHariIni = Parkir::whereDate('check_out', $today)->count();
        $pelanggaranHariIni = Parkir::whereDate('check_in', $today)
            ->where('status', 'violation')->count();

        // Kendaraan yang sedang parkir (siapa pun petugasnya)
        $parkirAktifList = Parkir::with('kendaraan.user')
            ->whereNull('check_out')
            ->latest('check_in')
            ->limit(10)
            ->get();

        // Riwayat yang ditangani petugas ini (untuk ringkasan di dashboard)
        $riwayatSaya = Parkir::with('kendaraan')
            ->where('petugas_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        return view('petugas.dashboard', compact(
            'parkirAktif',
            'checkinHariIni',
            'checkoutHariIni',
            'pelanggaranHariIni',
            'parkirAktifList',
            'riwayatSaya'
        ));
    }

    public function parkir()
    {
        // Semua riwayat parkir (bisa difilter nanti)
        $parkirs = Parkir::with('kendaraan')
            ->latest()
            ->paginate(10);

        return view('petugas.parkir', compact('parkirs'));
    }
}
