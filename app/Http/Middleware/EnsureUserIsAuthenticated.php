<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthenticated
{
    public function handle(Request $request, Closure $next, string $guard = 'web'): Response
    {
        if (!Auth::guard($guard)->check()) {
            if ($guard === 'pelanggan') {
                return redirect()->route('pelanggan.login')->with('error', 'Silakan login terlebih dahulu.');
            }

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
