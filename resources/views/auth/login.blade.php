@extends('layouts.app')
@section('title', 'Ingreso | Inventario WM')
@section('content')
<div class="login-layout">
    <section class="login-story">
        <div class="login-brand-lockup"><span class="brand-mark login-symbol" aria-hidden="true"><span>W</span><i></i></span><span>INVENTARIO <b>WM</b></span></div>
        <div class="login-story-copy"><p class="eyebrow eyebrow-light"><span class="eyebrow-dot"></span> Gestión simple, todos los días</p><h1>Menos vueltas.<br><em>Más claridad.</em></h1><p>Tu tienda tiene mucho que mover. Aquí tienes una forma sencilla de mantenerlo todo en su sitio.</p></div>
        <div class="login-story-foot"><span>CONTROL DE EXISTENCIAS</span><span>01 — 03</span></div>
        <div class="login-art" aria-hidden="true"><div class="login-art-ring"></div><div class="login-art-box"></div><div class="login-art-box small-box"></div><span>WM</span></div>
    </section>
    <section class="login-side">
        <div class="login-panel">
            <p class="eyebrow">Qué bueno tenerte de vuelta</p>
            <h2>Inicia sesión</h2>
            <p class="muted">Ingresa tus datos para continuar con el inventario.</p>
            <form method="POST" action="{{ route('login.store') }}" class="form-stack">
                @csrf
                <label>Usuario<input name="username" value="{{ old('username') }}" required maxlength="100" autocomplete="username" autofocus placeholder="Tu usuario"></label>
                <label>Contraseña<input type="password" name="password" required autocomplete="current-password" placeholder="Tu contraseña"></label>
                <button class="button button-dark" type="submit">Entrar al inventario <span aria-hidden="true">↗</span></button>
            </form>
            <p class="small muted">El acceso está protegido. Las credenciales se configuran localmente.</p>
        </div>
        <div class="login-side-note"><span>INVENTARIO WM</span><span>ORDEN PARA TU NEGOCIO <b>✳</b></span></div>
    </section>
</div>
@endsection
