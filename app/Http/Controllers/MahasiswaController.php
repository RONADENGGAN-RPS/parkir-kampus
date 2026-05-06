<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Parkir;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MahasiswaController extends Controller
{
    public function __construct()
    {
        // Hanya mahasiswa yang bisa akses
        if (auth()->user()->role->slug !== 'mahasiswa') {
            abort(403, 'Akses hanya untuk mahasiswa.');
        }
    }

    public function dashboard()
    {
        $user = auth()->user();
        $kendaraanCount = Kendaraan::where('user_id', $user->id)->count();
        $parkirAktif = Parkir::where('user_id', $user->id)->whereNull('check_out')->count();
        $parkirSelesai = Parkir::where('user_id', $user->id)->whereNotNull('check_out')->where('status', 'completed')->count();

        // Rata‑rata durasi (hanya yang sudah selesai)
        $rataDurasi = Parkir::where('user_id', $user->id)
            ->whereNotNull('durasi')
            ->avg('durasi') ?? 0;
        $rataDurasi = round($rataDurasi, 1);

        $riwayatTerbaru = Parkir::with('kendaraan')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard', compact(
            'kendaraanCount',
            'parkirAktif',
            'parkirSelesai',
            'rataDurasi',          // ← kirim variabel ini
            'riwayatTerbaru'
        ));
    }

    public function kendaraan()
    {
        $kendaraans = Kendaraan::where('user_id', auth()->id())->latest()->get();
        return view('mahasiswa.kendaraan', compact('kendaraans'));
    }

    public function parkir(Request $request)
    {
        $userId = auth()->id();
        $query = Parkir::with('kendaraan')->where('user_id', $userId);

        // Filter tanggal
        if ($request->filled('start') && $request->filled('end')) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();
            $query->whereBetween('check_in', [$start, $end]);
        }

        $parkirs = $query->latest()->paginate(10)->appends($request->all());

        // Statistik (tidak terpengaruh filter)
        $total       = Parkir::where('user_id', $userId)->count();
        $aktif       = Parkir::where('user_id', $userId)->whereNull('check_out')->count();
        $selesai     = Parkir::where('user_id', $userId)->whereNotNull('check_out')->where('status', 'completed')->count();
        $pelanggaran = Parkir::where('user_id', $userId)->where('status', 'violation')->count();

        return view('mahasiswa.parkir', compact(
            'parkirs',
            'total',
            'aktif',
            'selesai',
            'pelanggaran'
        ));
    }

    public function laporan(Request $request)
    {
        $userId = auth()->id();

        // Rentang default: 7 hari terakhir
        $start = $request->input('start', now()->subDays(6)->toDateString());
        $end   = $request->input('end', now()->toDateString());

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate   = Carbon::parse($end)->endOfDay();

        // Data grafik harian
        $labels = [];
        $dataParkir = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $labels[] = $current->translatedFormat('d M');
            $dataParkir[] = Parkir::where('user_id', $userId)
                ->whereDate('check_in', $current)
                ->count();
            $current->addDay();
        }

        // Total dalam rentang
        $totalParkir = array_sum($dataParkir);
        $totalViolation = Parkir::where('user_id', $userId)
            ->whereBetween('check_in', [$startDate, $endDate])
            ->where('status', 'violation')
            ->count();

        return view('mahasiswa.laporan', compact(
            'labels',
            'dataParkir',
            'totalParkir',
            'totalViolation',
            'start',
            'end'
        ));
    }
}
