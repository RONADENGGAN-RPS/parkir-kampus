<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        if (auth()->user()->role->slug !== 'superadmin') {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'kapasitas_parkir'     => 'required|integer|min:1',
            'max_parking_duration' => 'required|integer|min:1',
            'qr_expiry_days'       => 'required|integer|min:1',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'integer']
            );
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan.']);
    }
}
