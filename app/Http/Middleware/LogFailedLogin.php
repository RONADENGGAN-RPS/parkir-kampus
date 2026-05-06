<?php

namespace App\Http\Middleware;

use App\Models\LoginHistory;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class LogFailedLogin
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Jika login gagal dan ada email
        if ($request->isMethod('post') && $request->routeIs('login') && auth()->guest()) {
            $email = $request->input('email');
            $user = User::where('email', $email)->first();

            LoginHistory::create([
                'user_id'     => $user->id ?? null,
                'action'      => 'login_failed',
                'module'      => 'auth',
                'description' => 'Percobaan login gagal untuk email: ' . $email,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'data'        => json_encode(['email' => $email]),
            ]);
        }

        return $response;
    }
}