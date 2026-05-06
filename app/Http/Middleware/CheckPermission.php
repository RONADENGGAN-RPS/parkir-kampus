<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = auth()->user();
        if (!$user || !$user->role) abort(403);
        $permission = explode(':', $permission); // misal "user:create"
        $hasPermission = $user->role->permissions()->where('module', $permission[0])->where('action', $permission[1])->exists();
        if (!$hasPermission) abort(403);
        return $next($request);
    }
}
