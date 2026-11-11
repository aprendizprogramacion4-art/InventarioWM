@extends('layouts.app')
@section('title', 'Inventario | Inventario WM')
@section('content')
<section class="page-heading"><div><p class="eyebrow">Panel de administración</p><h1>Inventario de la tienda</h1><p class="muted">Registra productos, controla movimientos y revisa alertas.</p></div></section>
<section class="stats">
    <article class="stat"><span>Productos</span><strong>{{ $totalProducts }}</strong></article>
    <article class="stat"><span>Unidades disponibles</span><strong>{{ $totalUnits }}</strong></article>
    <article class="stat warning"><span>Productos por reponer</span><strong>{{ $lowStockCount }}</strong></article>
    <article class="stat"><span>Valor al costo</span><strong>${{ number_format((float) $inventoryValue, 2) }}</strong></article>
</section>
<div class="columns">
<section class="panel"><h2>Agregar producto</h2>
<form method="POST" action="{{ route('products.store') }}" class="form-grid">@csrf
<label>Nombre<input name="name" value="{{ old('name') }}" required maxlength="150"></label>
<label>Categoría<input name="category" value="{{ old('category') }}" placeholder="General"></label>
<label>Existencia inicial<input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required></label>
<label>Stock mínimo<input type="number" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" min="0" required></label>
<label>Precio de compra<input type="number" name="buy_price" value="{{ old('buy_price', 0) }}" min="0" step="0.01" required></label>
<label>Precio de venta<input type="number" name="sale_price" value="{{ old('sale_price', 0) }}" min="0" step="0.01" required></label>
<button class="button" type="submit">Guardar producto</button></form></section>
<section class="panel"><h2>Registrar entrada o salida</h2>
<form method="POST" action="{{ route('movements.store') }}" class="form-stack">@csrf
<label>Producto<select name="product_id" required><option value="">Selecciona producto</option>@foreach ($products as $product)<option value="{{ $product->id }}">{{ $product->name }} ({{ $product->stock }})</option>@endforeach</select></label>
<label>Tipo<select name="type" required><option value="entry">Entrada de mercancía</option><option value="exit">Salida / venta</option></select></label>
<label>Cantidad<input type="number" name="quantity" value="1" min="1" required></label>
<label>Nota opcional<input name="notes" maxlength="500" placeholder="Proveedor o detalle"></label>
<button class="button" type="submit">Guardar movimiento</button></form></section>
</div>
<section class="panel"><div class="section-heading"><h2>Productos</h2><form method="GET" action="{{ route('dashboard') }}" class="search"><input name="search" value="{{ $search }}" placeholder="Buscar producto o categoría"><button class="button secondary">Buscar</button></form></div>
<div class="table-scroll"><table><thead><tr><th>Producto</th><th>Categoría</th><th>Existencia</th><th>Mínimo</th><th>Compra</th><th>Venta</th><th>Acciones</th></tr></thead><tbody>
@forelse ($products as $product)<tr><td><strong>{{ $product->name }}</strong></td><td>{{ $product->category }}</td><td><span class="badge {{ $product->stock <= $product->minimum_stock ? 'badge-warning' : '' }}">{{ $product->stock }}</span></td><td>{{ $product->minimum_stock }}</td><td>${{ number_format((float) $product->buy_price, 2) }}</td><td>${{ number_format((float) $product->sale_price, 2) }}</td><td class="actions"><a href="{{ route('products.edit', $product) }}">Editar</a><form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto?')">@csrf @method('DELETE')<button class="text-danger">Eliminar</button></form></td></tr>
@empty<tr><td colspan="7" class="empty">No hay productos para mostrar.</td></tr>@endforelse
</tbody></table></div>{{ $products->links() }}</section>
<section class="panel"><h2>Movimientos recientes</h2><div class="table-scroll"><table><thead><tr><th>Fecha</th><th>Producto</th><th>Movimiento</th><th>Cantidad</th><th>Nota</th></tr></thead><tbody>
@forelse ($recentMovements as $movement)<tr><td>{{ $movement->created_at->format('d/m/Y H:i') }}</td><td>{{ $movement->product->name }}</td><td>{{ $movement->type === 'entry' ? 'Entrada' : 'Salida' }}</td><td>{{ $movement->quantity }}</td><td>{{ $movement->notes ?: '—' }}</td></tr>@empty<tr><td colspan="5" class="empty">Todavía no hay movimientos.</td></tr>@endforelse
</tbody></table></div></section>
@endsection
