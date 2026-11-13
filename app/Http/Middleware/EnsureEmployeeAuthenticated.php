<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('admin_authenticated') || $request->session()->get('role') !== 'employee') {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
