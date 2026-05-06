<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parkir;
use App\Models\Kendaraan;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role->slug ?? 'mahasiswa';

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

        $kapasitas      = Setting::where('key', 'kapasitas_parkir')->value('value') ?? 100;
        $tingkatOkupansi = $kapasitas > 0 ? round(($parkirAktif / $kapasitas) * 100) : 0;

        $pelanggaran    = Parkir::where('status', 'violation')
            ->whereBetween('check_in', [$startDate, $endDate])->count();

        $jamTersibuk = Parkir::whereDate('check_in', now()->toDateString())
            ->selectRaw('HOUR(check_in) as jam, COUNT(*) as total')
            ->groupBy('jam')->orderByDesc('total')->first();
        $jamTersibukLabel = $jamTersibuk ? sprintf('%02d:00 - %02d:59', $jamTersibuk->jam, $jamTersibuk->jam) : '-';
        $jumlahTersibuk = $jamTersibuk->total ?? 0;

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

        // ** ADMIN **
        if ($role === 'admin') {
            $today = Carbon::today();
            $totalCheckinToday  = Parkir::whereDate('check_in', $today)->count();
            $totalCheckoutToday = Parkir::whereDate('check_out', $today)->count();
            $pelanggaranToday   = Parkir::whereDate('check_in', $today)->where('status', 'violation')->count();
            $parkirAktifList = Parkir::with('kendaraan.user')->whereNull('check_out')->latest('check_in')->limit(10)->get();

            return view('admin.dashboard', compact(
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
                'totalCheckinToday',
                'totalCheckoutToday',
                'pelanggaranToday',
                'parkirAktifList'
            ));
        }

        // ** SUPER ADMIN **
        if ($role === 'superadmin') {
            $dataSuperadmin = [
                'totalSuperadmin' => User::whereHas('role', fn($q) => $q->where('slug', 'superadmin'))->count(),
                'totalAdmin'      => User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->count(),
                'totalPetugas'    => User::whereHas('role', fn($q) => $q->where('slug', 'petugas'))->count(),
                'totalMahasiswa'  => User::whereHas('role', fn($q) => $q->where('slug', 'mahasiswa'))->count(),
            ];
            $parkirAktifList = Parkir::with('kendaraan.user')->whereNull('check_out')->latest('check_in')->limit(10)->get();

            return view('dashboard', compact(
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
                'parkirAktifList',
                'dataSuperadmin'
            ))->with('isSuperadmin', true);
        }

        // ** MAHASISWA **
        if ($role === 'mahasiswa') {
            $userId = auth()->id();
            $kendaraanCount = Kendaraan::where('user_id', $userId)->count();
            $parkirSelesai = Parkir::where('user_id', $userId)->whereNotNull('check_out')->count();
            $riwayatTerbaru = Parkir::with('kendaraan')
                ->where('user_id', $userId)
                ->latest()
                ->take(5)
                ->get();

            return view('mahasiswa.dashboard', compact(
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
                'kendaraanCount',
                'parkirSelesai',
                'riwayatTerbaru'
            ));
        }

        // ** PETUGAS **
        if ($role === 'petugas') {
            return view('petugas.dashboard', compact(
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
                'end'
            ));
        }

        // Fallback
        return view('dashboard', compact(
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
            'end'
        ));
    }
}
