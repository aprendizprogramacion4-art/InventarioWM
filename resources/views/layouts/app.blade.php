<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inventario WM')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @if (session('admin_authenticated'))
    <header class="topbar">
        <a class="brand" href="{{ route('dashboard') }}">Inventario <span>WM</span></a>
        <nav>
            <a href="{{ route('dashboard') }}">Inventario</a>
            @if (session('role') === 'admin')
                <a href="{{ route('reports.index') }}">Reportes</a>
            @endif
            <span class="muted">{{ session('admin_username') }} · {{ session('role') === 'admin' ? 'Administrador' : 'Empleado' }}</span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="link-button">Cerrar sesión</button></form>
        </nav>
    </header>
    @endif
    <main class="container">
        @if (session('status')) <div class="notice">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="error-box"><strong>Revisa estos datos:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
        @yield('content')
    </main>
</body>
</html>
