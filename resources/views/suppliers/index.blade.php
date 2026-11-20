@extends('layouts.app')
@section('title', 'Proveedores | Inventario WM')
@section('content')
<section class="page-heading">
    <div><p class="eyebrow">Administración / Catálogo</p><h1>Proveedores</h1><p class="muted">Centraliza los datos de contacto de quienes abastecen tu tienda.</p></div>
    <a class="button button-outline" href="{{ route('dashboard') }}">Volver al inventario</a>
</section>

<div class="columns operation-grid">
    <section class="panel panel-form">
        <div class="panel-heading"><div class="panel-heading-icon">＋</div><div><p class="eyebrow">Nuevo registro</p><h2>Registrar proveedor</h2></div></div>
        <form method="POST" action="{{ route('suppliers.store') }}" class="form-stack">
            @csrf
            <label>Nombre del proveedor<input name="name" value="{{ old('name') }}" maxlength="150" required></label>
            <label>Persona de contacto<input name="contact_name" value="{{ old('contact_name') }}" maxlength="120"></label>
            <label>Teléfono<input name="phone" value="{{ old('phone') }}" maxlength="40"></label>
            <label>Correo electrónico<input type="email" name="email" value="{{ old('email') }}" maxlength="150"></label>
            <label>Dirección<input name="address" value="{{ old('address') }}" maxlength="255"></label>
            <button class="button button-dark" type="submit">Guardar proveedor</button>
        </form>
    </section>

    <section class="panel panel-table">
        <div class="section-heading"><div><p class="eyebrow">Directorio</p><h2>Proveedores registrados</h2><p class="muted section-subtitle">{{ $suppliers->count() }} proveedores</p></div></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Proveedor</th><th>Contacto</th><th>Teléfono / correo</th><th>Productos</th><th>Acción</th></tr></thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td><strong>{{ $supplier->name }}</strong><small class="muted">{{ $supplier->address }}</small></td>
                        <td>{{ $supplier->contact_name ?: '—' }}</td>
                        <td>{{ $supplier->phone ?: '—' }}<small class="muted">{{ $supplier->email }}</small></td>
                        <td>{{ $supplier->products_count }}</td>
                        <td><form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('¿Eliminar este proveedor?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Eliminar</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No hay proveedores registrados.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </section>
</div>
@endsection
