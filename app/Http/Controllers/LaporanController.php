<?php

namespace App\Http\Controllers;

use App\Models\Parkir;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Tentukan rentang tanggal: default 7 hari terakhir
        $start = $request->input('start', now()->subDays(6)->toDateString());
        $end = $request->input('end', now()->toDateString());
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        // Ringkasan Statistik
        $totalParkir = Parkir::whereBetween('check_in', [$startDate, $endDate])->count();
        $totalCheckin = Parkir::whereBetween('check_in', [$startDate, $endDate])->whereNull('check_out')->count();
        $totalCheckout = Parkir::whereBetween('check_in', [$startDate, $endDate])->whereNotNull('check_out')->count();
        $rataDurasi = round(Parkir::whereBetween('check_in', [$startDate, $endDate])
            ->whereNotNull('durasi')
            ->avg('durasi') ?? 0, 1);
        $pelanggaran = Parkir::whereBetween('check_in', [$startDate, $endDate])
            ->where('status', 'violation')->count();

        // Data grafik: grouping per hari
        $labels = [];
        $dataCheckin = [];
        $dataCheckout = [];
        $dataViolation = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $labels[] = $current->format('d M');
            $dataCheckin[] = Parkir::whereDate('check_in', $current)->whereNull('check_out')->count();
            $dataCheckout[] = Parkir::whereDate('check_in', $current)->whereNotNull('check_out')->count();
            $dataViolation[] = Parkir::whereDate('check_in', $current)->where('status', 'violation')->count();
            $current->addDay();
        }

        // Perbandingan jenis kendaraan (keseluruhan)
        $totalMobil = Kendaraan::where('tipe', 'mobil')->count();
        $totalMotor = Kendaraan::where('tipe', 'motor')->count();

        return view('laporan', compact(
            'start',
            'end',
            'totalParkir',
            'totalCheckin',
            'totalCheckout',
            'rataDurasi',
            'pelanggaran',
            'labels',
            'dataCheckin',
            'dataCheckout',
            'dataViolation',
            'totalMobil',
            'totalMotor'
        ));
    }
}
