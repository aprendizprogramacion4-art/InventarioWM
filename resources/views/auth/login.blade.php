@extends('layouts.app')
@section('title', 'Ingreso | Inventario WM')
@section('content')
<div class="login-shell">
    <section class="login-card">
        <p class="eyebrow">Control sencillo para tu tienda</p>
        <h1>Inventario WM</h1>
        <p class="login-subtitle">Ingresa con la cuenta de administración.</p>

        <form method="POST" action="{{ route('login.store') }}" class="form-stack">
            @csrf
            <label>
                Usuario
                <input name="username" value="{{ old('username') }}" required autocomplete="username">
            </label>
            <label>
                Contraseña
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="button primary" type="submit">Iniciar sesión</button>
        </form>
    </section>
</div>
@endsection
