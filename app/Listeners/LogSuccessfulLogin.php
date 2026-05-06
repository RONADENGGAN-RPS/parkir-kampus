<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use App\Models\LogAktivitas;

class LogSuccessfulLogin
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Login $event): void
    {
        LoginHistory::create([
            'user_id'     => $event->user->id,
            'action'      => 'login',
            'module'      => 'auth',
            'description' => 'User berhasil login',
            'ip_address'  => $this->request->ip(),
            'user_agent'  => $this->request->userAgent(),
            'data'        => json_encode([
                'email' => $event->user->email,
                'name'  => $event->user->name,
                'role'  => $event->user->role->slug ?? 'unknown',
            ]),
        ]);

        // Reset percobaan login
        $event->user->updateQuietly([
            'last_login_at'  => now(),
            'last_login_ip'  => $this->request->ip(),
            'login_attempts' => 0,
        ]);

        // di method handle:
        LogAktivitas::create([
            'user_id'     => $event->user->id,
            'action'      => 'login',
            'module'      => 'auth',
            'description' => $event->user->name . ' berhasil login',
            'ip_address'  => $this->request->ip(),
            'user_agent'  => $this->request->userAgent(),
            'data'        => json_encode(['email' => $event->user->email]),
        ]);
    }
}
