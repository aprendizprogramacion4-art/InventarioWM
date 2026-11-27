<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101312">
    <meta name="description" content="Inventario WM: una forma clara y sencilla de llevar el control de tu tienda.">
    <title>@yield('title', 'Inventario WM')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="@if (session('admin_authenticated')) app-shell @else auth-shell @endif">
    @if (session('admin_authenticated'))
        <header class="topbar">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="Inventario WM, inicio">
                <span class="brand-mark" aria-hidden="true"><span>W</span><i></i></span>
                <span class="brand-copy"><span class="brand-name">Inventario <strong>WM</strong></span><span class="brand-caption">CONTROL DE INVENTARIO</span></span>
            </a>
            <nav class="main-nav" aria-label="Navegación principal">
                <a href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard', 'products.*')) aria-current="page" @endif><span class="nav-number">01</span>Inventario</a>
                <a href="{{ route('movements.index') }}" @if (request()->routeIs('movements.*')) aria-current="page" @endif><span class="nav-number">02</span>Movimientos</a>
                <a href="{{ route('reports.index') }}" @if (request()->routeIs('reports.*')) aria-current="page" @endif><span class="nav-number">03</span>Reportes</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="link-button" type="submit">Salir <span aria-hidden="true">↗</span></button></form>
            </nav>
        </header>
    @endif

    <main class="container">
        @if (session('status'))
            <div class="notice" role="status"><span class="notice-mark" aria-hidden="true">✓</span><span>{{ session('status') }}</span></div>
        @endif
        @if ($errors->any())
            <div class="error-box" role="alert">
                <strong>Hay algo que revisar</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>

    @if (session('admin_authenticated'))
        <footer class="app-footer"><span>INVENTARIO WM</span><span>Hecho para trabajar con más orden <b>✳</b></span></footer>
    @endif
</body>
</html>
