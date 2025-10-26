<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) return redirect()->route('login');

        $current = strtolower($user->role ?? '');
        $roles   = array_map('strtolower', $roles);
        if (!in_array($current, $roles, true)) abort(403, 'Forbidden');

        return $next($request);
    }
}
