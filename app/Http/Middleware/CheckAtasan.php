<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAtasan
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->is_atasan || Auth::user()->is_admin)) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Akses tidak diizinkan. Hanya untuk atasan.');
    }
}