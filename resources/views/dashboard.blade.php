@extends('layouts.app')
@section('title', 'Inventario | Inventario WM')
@section('content')
<section class="page-heading">
    <div>
        <p class="eyebrow">{{ $isAdmin ? 'Panel de administración' : 'Panel de empleado' }}</p>
        <h1>Inventario de la tienda</h1>
        <p class="muted">Consulta existencias, registra movimientos y revisa alertas.</p>
    </div>
</section>

<section class="stats">
    <article class="stat"><div class="stat-icon">📦</div><div class="stat-content"><span>Productos</span><strong>{{ $totalProducts }}</strong></div></article>
    <article class="stat"><div class="stat-icon">📊</div><div class="stat-content"><span>Unidades disponibles</span><strong>{{ $totalUnits }}</strong></div></article>
    <article class="stat warning"><div class="stat-icon">⚠️</div><div class="stat-content"><span>Productos por reponer</span><strong>{{ $lowStockCount }}</strong></div></article>
    @if ($isAdmin)
    <article class="stat success"><div class="stat-icon">💰</div><div class="stat-content"><span>Valor al costo</span><strong>${{ number_format((float) $inventoryValue, 2) }}</strong></div></article>
    @endif
</section>

<div class="columns">
    @if ($isAdmin)
    <section class="panel">
        <h2>Agregar producto</h2>
        <form method="POST" action="{{ route('products.store') }}" class="form-grid">
            @csrf
            <label>Nombre<input name="name" value="{{ old('name') }}" required maxlength="150" placeholder="Ej. Galletas"></label>
            <label>Categoría<input name="category" value="{{ old('category') }}" placeholder="General"></label>
            <label>Existencia inicial<input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required></label>
            <label>Stock mínimo<input type="number" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" min="0" required></label>
            <label>Precio de compra<input type="number" name="buy_price" value="{{ old('buy_price', 0) }}" min="0" step="0.01" required></label>
            <label>Precio de venta<input type="number" name="sale_price" value="{{ old('sale_price', 0) }}" min="0" step="0.01" required></label>
            <button class="button" type="submit">Guardar producto</button>
        </form>
    </section>
    @endif

    <section class="panel">
        <h2>Registrar entrada o salida</h2>
        <form method="POST" action="{{ route('movements.store') }}" class="form-stack">
            @csrf
            <label>Producto<select name="product_id" required><option value="">Selecciona producto</option>@foreach ($allProducts as $product)<option value="{{ $product->id }}">{{ $product->name }} ({{ $product->stock }})</option>@endforeach</select></label>
            <label>Tipo<select name="type" required><option value="entry">Entrada de mercancía</option><option value="exit">Salida / venta</option></select></label>
            <label>Cantidad<input type="number" name="quantity" value="1" min="1" required></label>
            <label>Nota opcional<input name="notes" maxlength="500" placeholder="Proveedor o detalle"></label>
            <button class="button" type="submit">Guardar movimiento</button>
        </form>
    </section>
</div>

<section class="panel">
    <div class="section-heading">
        <h2>Productos</h2>
        <form method="GET" action="{{ route('dashboard') }}" class="search"><input name="search" value="{{ $search }}" placeholder="Buscar producto o categoría"><button class="button secondary" type="submit">Buscar</button></form>
    </div>
    <div class="table-scroll"><table>
        <thead><tr><th>Producto</th><th>Categoría</th><th>Existencia</th><th>Estado</th><th>Mínimo</th>@if ($isAdmin)<th>Compra</th>@endif<th>Venta</th>@if ($isAdmin)<th>Acciones</th>@endif</tr></thead>
        <tbody>
            @forelse ($products as $product)
                @php $status = $product->stock <= $product->minimum_stock ? 'Bajo' : 'Normal'; $statusClass = $product->stock <= $product->minimum_stock ? 'badge-warning' : 'badge-good'; @endphp
                <tr>
                    <td><strong>{{ $product->name }}</strong></td><td>{{ $product->category }}</td><td>{{ $product->stock }}</td><td><span class="badge {{ $statusClass }}">{{ $status }}</span></td><td>{{ $product->minimum_stock }}</td>
                    @if ($isAdmin)<td>${{ number_format((float) $product->buy_price, 2) }}</td>@endif
                    <td>${{ number_format((float) $product->sale_price, 2) }}</td>
                    @if ($isAdmin)<td class="actions"><a href="{{ route('products.edit', $product) }}">Editar</a><form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Eliminar</button></form></td>@endif
                </tr>
            @empty
                <tr><td colspan="{{ $isAdmin ? 8 : 6 }}" class="empty">No hay productos para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table></div>
    {{ $products->links() }}
</section>

<section class="panel">
    <h2>Movimientos recientes</h2>
    <div class="table-scroll"><table>
        <thead><tr><th>Fecha</th><th>Producto</th><th>Movimiento</th><th>Cantidad</th><th>Registrado por</th><th>Nota</th></tr></thead>
        <tbody>
            @forelse ($recentMovements as $movement)
                <tr><td>{{ $movement->created_at->format('d/m/Y H:i') }}</td><td>{{ $movement->product->name }}</td><td><span class="movement-type {{ $movement->type === 'entry' ? 'entry' : 'exit' }}">{{ $movement->type === 'entry' ? 'Entrada' : 'Salida' }}</span></td><td>{{ $movement->quantity }}</td><td>{{ $movement->registered_by ?? 'Sistema' }}</td><td>{{ $movement->notes ?: '—' }}</td></tr>
            @empty
                <tr><td colspan="6" class="empty">Todavía no hay movimientos.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</section>
@endsection
