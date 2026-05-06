<?php

namespace App\Http\Controllers;

use App\Models\Parkir;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ParkirController extends Controller
{
    public function index(Request $request)
    {
        $query = Parkir::with(['kendaraan.user', 'petugas']);

        // Filter periode
        if ($request->filled('start') && $request->filled('end')) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();
            $query->whereBetween('check_in', [$start, $end]);
        } elseif ($request->filled('tanggal')) {
            $date = Carbon::parse($request->tanggal);
            $query->whereDate('check_in', $date);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian plat atau merk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('kendaraan', function ($q) use ($search) {
                $q->where('plat_nomor', 'like', "%$search%")
                    ->orWhere('merk', 'like', "%$search%");
            });
        }

        // Clone query untuk statistik (tanpa pagination)
        $statQuery = clone $query;
        $parkirs = $query->latest('check_in')->paginate(15)->withQueryString();

        $statistik = [
            'total' => $statQuery->count(),
            'aktif' => (clone $statQuery)->where('status', 'active')->count(),
            'selesai' => (clone $statQuery)->where('status', 'completed')->count(),
            'pelanggaran' => (clone $statQuery)->where('status', 'violation')->count(),
            'rata_durasi' => round((clone $statQuery)->whereNotNull('check_out')->avg('durasi') ?? 0, 1),
        ];

        return view('parkir.index', compact('parkirs', 'statistik'));
    }

    public function show(Parkir $parkir)
    {
        $parkir->load(['kendaraan.user', 'petugas']);
        return view('parkir.show', compact('parkir'));
    }

    public function markViolation(Parkir $parkir)
    {
        $parkir->update(['status' => 'violation']);
        return redirect()->back()->with('success', 'Sesi parkir ditandai sebagai pelanggaran.');
    }
}
