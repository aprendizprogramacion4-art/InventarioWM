<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInventoryRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $currentRole = $request->session()->get('inventory_role')
            ?? ($request->session()->get('admin_authenticated') ? 'admin' : null);

        abort_unless($currentRole === $role, 403);

        return $next($request);
    }
}
