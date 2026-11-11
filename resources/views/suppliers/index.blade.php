@extends('layouts.app')
@section('title', 'Proveedores | Inventario WM')
@section('content')
<section class="page-heading compact">
    <div>
        <p class="eyebrow">Gestión de proveedores</p>
        <h1>Proveedores</h1>
        <p class="muted">Mantén actualizados tus contactos y datos de compra.</p>
    </div>
</section>

<div class="columns">
    <section class="panel">
        <h2>Registrar proveedor</h2>
        <form method="POST" action="{{ route('suppliers.store') }}" class="form-grid">
            @csrf
            <label>
                Nombre
                <input name="name" value="{{ old('name') }}" required maxlength="150">
            </label>
            <label>
                Teléfono
                <input name="phone" value="{{ old('phone') }}" maxlength="30" placeholder="Ej. 555-1234">
            </label>
            <label>
                Correo
                <input name="email" type="email" value="{{ old('email') }}" maxlength="150" placeholder="correo@ejemplo.com">
            </label>
            <label>
                Dirección
                <input name="address" value="{{ old('address') }}" maxlength="255" placeholder="Calle, ciudad, país">
            </label>
            <button class="button" type="submit">Guardar proveedor</button>
        </form>
    </section>

    <section class="panel">
        <h2>Listado</h2>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Dirección</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone ?: '—' }}</td>
                            <td>{{ $supplier->email ?: '—' }}</td>
                            <td>{{ $supplier->address ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty">No hay proveedores registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $suppliers->links() }}
    </section>
</div>
@endsection
