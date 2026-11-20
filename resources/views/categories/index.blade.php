@extends('layouts.app')
@section('title', 'Categorías | Inventario WM')
@section('content')
<section class="page-heading">
    <div><p class="eyebrow">Administración / Catálogo</p><h1>Categorías</h1><p class="muted">Organiza los productos con categorías consistentes.</p></div>
    <a class="button button-outline" href="{{ route('dashboard') }}">Volver al inventario</a>
</section>

<div class="columns operation-grid">
    <section class="panel panel-form">
        <div class="panel-heading"><div class="panel-heading-icon">＋</div><div><p class="eyebrow">Nuevo registro</p><h2>Crear categoría</h2></div></div>
        <form method="POST" action="{{ route('categories.store') }}" class="form-stack">
            @csrf
            <label>Nombre<input name="name" value="{{ old('name') }}" maxlength="100" required placeholder="Ej. Abarrotes"></label>
            <label>Descripción <span class="muted">(opcional)</span><input name="description" value="{{ old('description') }}" maxlength="255"></label>
            <button class="button button-dark" type="submit">Guardar categoría</button>
        </form>
    </section>

    <section class="panel panel-table">
        <div class="section-heading"><div><p class="eyebrow">Catálogo disponible</p><h2>Categorías registradas</h2><p class="muted section-subtitle">{{ $categories->count() }} categorías</p></div></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Nombre</th><th>Descripción</th><th>Productos</th><th>Acción</th></tr></thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ $category->description ?: '—' }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td><form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta categoría?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Eliminar</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No hay categorías registradas.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </section>
</div>
@endsection
