@extends('layouts.app')
@section('title', 'Usuarios | Inventario WM')
@section('content')
<section class="page-heading">
    <div><p class="eyebrow">Administración / 04</p><h1>Usuarios del sistema</h1><p class="muted">Crea y administra las cuentas de los empleados.</p></div>
    <a class="button button-outline" href="{{ route('dashboard') }}">Volver al inventario</a>
</section>

<div class="columns operation-grid">
    <section class="panel panel-form">
        <div class="panel-heading"><div class="panel-heading-icon">＋</div><div><p class="eyebrow">Nuevo acceso</p><h2>Crear empleado</h2></div></div>
        <p class="panel-description">Los empleados pueden consultar el inventario y registrar movimientos, pero no administrar productos ni usuarios.</p>
        <form method="POST" action="{{ route('users.store') }}" class="form-stack">
            @csrf
            <label>Nombre completo<input name="name" value="{{ old('name') }}" maxlength="120" required></label>
            <label>Nombre de usuario<input name="username" value="{{ old('username') }}" maxlength="100" autocomplete="off" required></label>
            <label>Contraseña<input type="password" name="password" minlength="8" autocomplete="new-password" required></label>
            <label>Confirmar contraseña<input type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required></label>
            <button class="button button-dark" type="submit">Crear usuario</button>
        </form>
    </section>

    <section class="panel panel-table">
        <div class="section-heading"><div><p class="eyebrow">Cuentas registradas</p><h2>Empleados</h2><p class="muted section-subtitle">{{ $users->count() }} usuarios</p></div></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Estado</th><th>Acción</th></tr></thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->is_active ? 'Activo' : 'Inactivo' }}</td>
                        <td><form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Eliminar</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Todavía no hay empleados registrados.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </section>
</div>
@endsection
