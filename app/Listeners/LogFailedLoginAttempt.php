<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use App\Notifications\LoginFailedNotification;
use App\Models\LogAktivitas;

class LogFailedLoginAttempt
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? null;
        if (!$email) return;

        $user = User::where('email', $email)->first();

        // Catat ke login_histories
        LoginHistory::create([
            'user_id'     => $user->id ?? null,
            'action'      => 'login_failed',
            'module'      => 'auth',
            'description' => 'Percobaan login gagal untuk: ' . $email,
            'ip_address'  => $this->request->ip(),
            'user_agent'  => $this->request->userAgent(),
            'data'        => json_encode(['email' => $email]),
        ]);

        // Tambah jumlah percobaan gagal
        if ($user) {
            $user->increment('login_attempts');

            // Kunci akun jika >= 5 kali gagal
            if ($user->login_attempts >= 5) {
                $user->update(['locked_until' => now()->addMinutes(15)]);
            }
        }

        // 🔔 Kirim notifikasi ke semua admin & superadmin
        $admins = User::whereHas('role', fn($q) => $q->whereIn('slug', ['admin', 'superadmin']))->get();
        foreach ($admins as $admin) {
            $admin->notify(new LoginFailedNotification($email, $this->request->ip()));
        }
        
        LogAktivitas::create([
            'user_id'     => $user->id ?? null,
            'action'      => 'login_failed',
            'module'      => 'auth',
            'description' => 'Login gagal untuk email: ' . $email,
            'ip_address'  => $this->request->ip(),
            'user_agent'  => $this->request->userAgent(),
            'data'        => json_encode(['email' => $email]),
        ]);
    }
}
