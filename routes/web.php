<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ParkirController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;   // tambahkan ini
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LogAktivitasController;

// Redirect dari root ke dashboard
Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    // Login dengan pencatatan kegagalan
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('log.failed.login');

    // ** Redirect berdasarkan role (langsung setelah login) **
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        return match ($user->role->slug) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'admin'      => redirect()->route('admin.dashboard'),
            'petugas'    => redirect()->route('petugas.dashboard'),
            'mahasiswa'  => redirect()->route('mahasiswa.dashboard'),
            default      => view('dashboard'),
        };
    })->name('dashboard');

    // ** Notifikasi **
    Route::get('/notifications/data', [NotificationController::class, 'fetch'])
        ->name('notifications.data');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    // ** Kendaraan **
    Route::get('kendaraan/{kendaraan}/qr', [KendaraanController::class, 'qr'])
        ->name('kendaraan.qr');
    Route::patch('kendaraan/{kendaraan}/toggle-status', [KendaraanController::class, 'toggleStatus'])
        ->name('kendaraan.toggleStatus');
    Route::resource('kendaraan', KendaraanController::class)->except(['show', 'create', 'edit']);

    // ** Scan QR **
    Route::match(['post', 'options'], '/web-scan', [ScanController::class, 'webScan'])
        ->name('web.scan');
    Route::get('/scan', [ScanController::class, 'index'])->name('scan');

    // ** Parkir **
    Route::get('/parkir', [ParkirController::class, 'index'])->name('parkir.index');
    Route::get('/parkir/{parkir}', [ParkirController::class, 'show'])->name('parkir.show');
    Route::patch('/parkir/{parkir}/mark-violation', [ParkirController::class, 'markViolation'])
        ->name('parkir.markViolation');

    // ** Laporan **
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');

    // ** Manajemen User (admin & superadmin) **
    Route::resource('users', UserController::class)->except(['show', 'create']);
    Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.resetPassword');

    // ** Roles & Permissions (superadmin) **
    Route::resource('roles', RoleController::class)->except(['show', 'create', 'edit']);
    Route::post('roles/{role}/sync-permissions', [RoleController::class, 'syncPermissions'])
        ->name('roles.syncPermissions');

    // ** Backup **
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/create', [BackupController::class, 'create'])->name('backup.create');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/delete/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');
    Route::post('/backup/restore/{filename}', [BackupController::class, 'restore'])->name('backup.restore');

    // ** Settings **
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');

    // ** Profile (Breeze) **
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ** Dashboard khusus per role **
    Route::get('/superadmin', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // ** Mahasiswa **
    Route::get('/mahasiswa', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
    Route::get('/mahasiswa/kendaraan', [MahasiswaController::class, 'kendaraan'])->name('mahasiswa.kendaraan');
    Route::get('/mahasiswa/parkir', [MahasiswaController::class, 'parkir'])->name('mahasiswa.parkir');
    Route::get('/mahasiswa/laporan', [MahasiswaController::class, 'laporan'])->name('mahasiswa.laporan');

    // ** Petugas **
    Route::get('/petugas', [PetugasController::class, 'dashboard'])->name('petugas.dashboard');
    Route::get('/petugas/parkir', [PetugasController::class, 'parkir'])->name('petugas.parkir');

    // ** Log-Aktivitas **
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log-aktivitas.index');
});

require __DIR__ . '/auth.php';
