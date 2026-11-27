<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        // Codespaces serves the application through a forwarded HTTPS proxy.
        // Trust forwarded host/protocol headers so Laravel generates links and
        // redirects using the public Codespaces URL instead of localhost.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->alias(['admin' => App\Http\Middleware\EnsureAdminAuthenticated::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Laravel registra el manejo estándar de excepciones.
    })->create();
