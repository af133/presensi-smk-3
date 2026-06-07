<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsGuru
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login'); 
        }

        // 2. Cek apakah user memiliki role 'guru'
        if (!auth()->user()->hasRole('guru')&&!auth()->user()->hasRole('bk') ) {
            abort(403, 'Anda tidak memiliki akses sebagai guru.');
        }

        return $next($request);
    }
}