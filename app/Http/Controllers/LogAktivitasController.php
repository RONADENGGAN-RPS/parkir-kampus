<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function __construct()
    {
        if (!in_array(auth()->user()->role->slug, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')->latest();

        // Filter tanggal
        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('created_at', [
                $request->start . ' 00:00:00',
                $request->end . ' 23:59:59'
            ]);
        }

        // Filter module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Pencarian deskripsi
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20)->withQueryString();
        $modules = LogAktivitas::distinct()->pluck('module')->sort();

        return view('log-aktivitas.index', compact('logs', 'modules'));
    }
}