<?php

namespace App\Http\Controllers;

use App\Services\QrScanService;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan');
    }

    public function webScan(Request $request, QrScanService $qrService)
    {
        // Paksa header Accept JSON untuk mencegah 405
        $request->headers->set('Accept', 'application/json');

        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $deviceInfo = [
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp'  => now()->toDateTimeString(),
        ];

        $result = $qrService->process($request->qr_data, $deviceInfo);

        return response()->json($result);
    }
}
