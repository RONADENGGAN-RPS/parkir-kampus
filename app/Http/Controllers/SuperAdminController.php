<?php

namespace App\Http\Controllers;

use App\Models\Parkir;
use App\Models\Kendaraan;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        if (auth()->user()->role->slug !== 'superadmin') {
            abort(403);
        }
    }

    public function dashboard(Request $request)
    {
        // Filter tanggal
        $start = $request->input('start', now()->startOfDay()->toDateString());
        $end   = $request->input('end', now()->endOfDay()->toDateString());
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate   = Carbon::parse($end)->endOfDay();

        // Data Umum
        $totalKendaraan = Kendaraan::count();
        $parkirAktif    = Parkir::whereNull('check_out')->count();
        $checkinHariIni = Parkir::whereBetween('check_in', [$startDate, $endDate])->count();
        $checkoutHariIni = Parkir::whereBetween('check_out', [$startDate, $endDate])->count();
        $totalPengguna  = User::count();
        $rataDurasi     = Parkir::whereNotNull('check_out')
            ->whereBetween('check_in', [$startDate, $endDate])
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, check_in, check_out)) as avg_dur')
            ->value('avg_dur') ?? 0;
        $rataDurasi     = round($rataDurasi, 1);

        $kapasitas       = Setting::where('key', 'kapasitas_parkir')->value('value') ?? 100;
        $tingkatOkupansi = $kapasitas > 0 ? round(($parkirAktif / $kapasitas) * 100) : 0;
        $pelanggaran     = Parkir::where('status', 'violation')
            ->whereBetween('check_in', [$startDate, $endDate])->count();

        $jamTersibuk = Parkir::whereDate('check_in', now()->toDateString())
            ->selectRaw('HOUR(check_in) as jam, COUNT(*) as total')
            ->groupBy('jam')->orderByDesc('total')->first();
        $jamTersibukLabel = $jamTersibuk ? sprintf('%02d:00 - %02d:59', $jamTersibuk->jam, $jamTersibuk->jam) : '-';
        $jumlahTersibuk = $jamTersibuk->total ?? 0;

        // Grafik 7 hari terakhir
        $grafikStart = now()->subDays(6)->startOfDay();
        $labels = $dataCheckin = $dataCheckout = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $grafikStart->copy()->addDays($i);
            $labels[] = $date->format('d M');
            $dataCheckin[]  = Parkir::whereDate('check_in', $date)->count();
            $dataCheckout[] = Parkir::whereDate('check_out', $date)->count();
        }

        $aktifMobil = Parkir::whereNull('check_out')->whereHas('kendaraan', fn($q) => $q->where('tipe', 'mobil'))->count();
        $aktifMotor = Parkir::whereNull('check_out')->whereHas('kendaraan', fn($q) => $q->where('tipe', 'motor'))->count();
        $aktivitasTerbaru = Parkir::with('kendaraan')->latest()->limit(10)->get();

        // Statistik user per role
        $totalSuperadmin = User::whereHas('role', fn($q) => $q->where('slug', 'superadmin'))->count();
        $totalAdmin      = User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->count();
        $totalPetugas    = User::whereHas('role', fn($q) => $q->where('slug', 'petugas'))->count();
        $totalMahasiswa  = User::whereHas('role', fn($q) => $q->where('slug', 'mahasiswa'))->count();

        // Parkir aktif terbaru
        $parkirAktifList = Parkir::with('kendaraan.user')
            ->whereNull('check_out')
            ->latest('check_in')
            ->limit(10)
            ->get();

        return view('superadmin.dashboard', compact(
            'totalKendaraan',
            'parkirAktif',
            'checkinHariIni',
            'checkoutHariIni',
            'totalPengguna',
            'rataDurasi',
            'tingkatOkupansi',
            'pelanggaran',
            'jamTersibukLabel',
            'jumlahTersibuk',
            'labels',
            'dataCheckin',
            'dataCheckout',
            'aktifMobil',
            'aktifMotor',
            'aktivitasTerbaru',
            'start',
            'end',
            'totalSuperadmin',
            'totalAdmin',
            'totalPetugas',
            'totalMahasiswa',
            'parkirAktifList'
        ));
    }
}