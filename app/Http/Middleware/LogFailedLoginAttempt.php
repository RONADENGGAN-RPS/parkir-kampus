<?php

namespace App\Http\Middleware;

use App\Models\LoginHistory;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class LogFailedLoginAttempt
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->isMethod('post') && $request->routeIs('login') && auth()->guest()) {
            $email = $request->input('email');
            $user = User::where('email', $email)->first();

            LoginHistory::create([
                'user_id'     => $user->id ?? null,
                'action'      => 'login_failed',
                'module'      => 'auth',
                'description' => 'Login gagal',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'data'        => json_encode(['email' => $email]),
            ]);

            if ($user) {
                $user->increment('login_attempts');
                if ($user->login_attempts >= 5) {
                    $user->update(['locked_until' => now()->addMinutes(30)]);
                }
            }
        }

        return $response;
    }
}
