<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ParkirService;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    protected $parkirService;

    public function __construct(ParkirService $parkirService)
    {
        $this->parkirService = $parkirService;
    }

    // Method untuk API (jika nanti diperlukan)
    public function scan(Request $request)
    {
        // ...
    }

    // ** Baru: untuk permintaan dari halaman web **
    public function webScan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $deviceInfo = [
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            // bisa tambah data lain
        ];

        // Panggil service yang sama
        $result = $this->parkirService->processQr($request->qr_data, $deviceInfo);

        return response()->json($result);
    }
}
