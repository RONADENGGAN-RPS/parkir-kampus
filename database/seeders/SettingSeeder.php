<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        Setting::firstOrCreate(
            ['key' => 'kapasitas_parkir'],
            ['value' => '100', 'type' => 'integer', 'description' => 'Kapasitas maksimal parkir bersamaan']
        );
        Setting::firstOrCreate(
            ['key' => 'max_parking_duration'],
            ['value' => '300', 'type' => 'integer', 'description' => 'Batas durasi parkir (menit) sebelum pelanggaran']
        );
        Setting::firstOrCreate(
            ['key' => 'qr_expiry_days'],
            ['value' => '365', 'type' => 'integer', 'description' => 'Masa berlaku QR Code (hari)']
        );
    }
}
