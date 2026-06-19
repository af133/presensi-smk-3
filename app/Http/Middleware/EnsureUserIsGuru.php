<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsGuru
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login'); 
        }
        if (auth()->user()->status != 1 || auth()->user()->hasRole('admin')){
            abort(403, 'Anda tidak memiliki akses.');
        }

        return $next($request);
    }
}