<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isAdministrator()) {
            abort(403, 'Akses ditolak. Fitur ini hanya tersedia untuk Administrator.');
        }

        return $next($request);
    }
}
