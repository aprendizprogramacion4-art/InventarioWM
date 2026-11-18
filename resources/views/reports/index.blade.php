@extends('layouts.app')
@section('title', 'Reportes | Inventario WM')
@section('content')
<section class="page-heading">
    <div><p class="eyebrow">Lectura del negocio / 03</p><h1>Los números hablan.</h1><p class="muted">Un resumen de lo que ha entrado, salido y permanece en tu tienda.</p></div>
    <a class="button button-orange" href="{{ route('reports.csv') }}"><span aria-hidden="true">↓</span> Descargar inventario CSV</a>
</section>
<section class="stats report-stats">
    <article class="stat stat-products"><div class="stat-top"><span class="stat-label">Acumulado</span><span class="stat-symbol">＋</span></div><div class="stat-content"><strong>{{ $totalEntries }}</strong><span>Unidades recibidas</span></div><div class="stat-foot"><span class="stat-line"></span> Entradas registradas</div></article>
    <article class="stat stat-warning"><div class="stat-top"><span class="stat-label">Acumulado</span><span class="stat-symbol">−</span></div><div class="stat-content"><strong>{{ $totalExits }}</strong><span>Unidades de salida</span></div><div class="stat-foot"><span class="stat-line"></span> Salidas registradas</div></article>
    <article class="stat stat-value"><div class="stat-top"><span class="stat-label">Organización</span><span class="stat-symbol">▤</span></div><div class="stat-content"><strong>{{ $categories->count() }}</strong><span>Categorías activas</span></div><div class="stat-foot"><span class="stat-line"></span> En el catálogo</div></article>
</section>
<section class="panel panel-table">
    <div class="section-heading"><div><p class="eyebrow">Cómo se distribuye</p><h2>Inventario por categoría</h2><p class="muted section-subtitle">Productos, unidades disponibles y precio de venta promedio.</p></div></div>
    <div class="table-scroll"><table><thead><tr><th>Categoría</th><th>Productos</th><th>Unidades disponibles</th><th>Precio venta promedio</th></tr></thead><tbody>
        @forelse ($categories as $category)<tr><td><strong>{{ $category->category }}</strong></td><td>{{ $category->product_count }}</td><td>{{ $category->units }}</td><td>${{ number_format((float) $category->average_price, 2) }}</td></tr>@empty<tr><td colspan="4" class="empty">Aún no existen productos.</td></tr>@endforelse
    </tbody></table></div>
</section>
<section class="panel panel-table">
    <div class="section-heading"><div><p class="eyebrow">Ritmo de la tienda</p><h2>Entradas y salidas por producto</h2><p class="muted section-subtitle">Compara las unidades que han ingresado y las que han salido.</p></div></div>
    <div class="table-scroll"><table><thead><tr><th>Producto</th><th>Entradas</th><th>Salidas</th></tr></thead><tbody>
        @forelse ($movements as $movement)<tr><td><strong>{{ $movement->product?->name ?? 'Producto eliminado' }}</strong></td><td><span class="movement-type entry">＋ {{ $movement->entries }}</span></td><td><span class="movement-type exit">− {{ $movement->exits }}</span></td></tr>@empty<tr><td colspan="3" class="empty">Aún no hay movimientos registrados.</td></tr>@endforelse
    </tbody></table></div>
</section>
@endsection
