@extends('layouts.app')
@section('title', 'Inventario | Inventario WM')
@section('content')
<section class="dashboard-hero">
    <div class="hero-copy">
        <p class="eyebrow eyebrow-light"><span class="eyebrow-dot"></span> Tu tienda, en orden</p>
        <h1>El inventario<br><em>bajo control.</em></h1>
        <p>Una mirada clara a tus productos, existencias y movimientos del día a día.</p>
        <a class="hero-link" href="#productos">Explorar productos <span aria-hidden="true">↓</span></a>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
        <div class="art-box box-back"><span></span></div><div class="art-box box-front"><span></span><i></i></div>
        <div class="art-sticker">WM<br><small>STOCK<br>ROOM</small></div><span class="art-caption">TODO CUENTA.</span>
    </div>
    <div class="hero-index">01 <span>/ 03</span></div>
</section>

<section class="stats" aria-label="Resumen del inventario">
    <article class="stat stat-products"><div class="stat-top"><span class="stat-label">Catálogo</span><span class="stat-symbol" aria-hidden="true">▦</span></div><div class="stat-content"><strong>{{ $totalProducts }}</strong><span>Productos registrados</span></div><div class="stat-foot"><span class="stat-line"></span> En tu catálogo</div></article>
    <article class="stat stat-units"><div class="stat-top"><span class="stat-label">Existencias</span><span class="stat-symbol" aria-hidden="true">↗</span></div><div class="stat-content"><strong>{{ $totalUnits }}</strong><span>Unidades disponibles</span></div><div class="stat-foot"><span class="stat-line"></span> Stock actual</div></article>
    <article class="stat stat-warning {{ $lowStockCount > 0 ? 'has-alert' : '' }}"><div class="stat-top"><span class="stat-label">Atención</span><span class="stat-symbol" aria-hidden="true">!</span></div><div class="stat-content"><strong>{{ $lowStockCount }}</strong><span>Por debajo del mínimo</span></div><div class="stat-foot"><span class="stat-line"></span> Productos por reponer</div></article>
    <article class="stat stat-value"><div class="stat-top"><span class="stat-label">Valor de compra</span><span class="stat-symbol" aria-hidden="true">$</span></div><div class="stat-content"><strong>{{ '$' }}{{ number_format((float) $inventoryValue, 2) }}</strong><span>Valor al costo</span></div><div class="stat-foot"><span class="stat-line"></span> Capital en inventario</div></article>
</section>

<div class="section-kicker"><span>02</span><span>Operación diaria</span><i></i></div>
<div class="columns operation-grid">
    @if (session('inventory_role') === 'admin' || session('admin_authenticated'))
        <section class="panel panel-form panel-create">
            <div class="panel-heading"><div class="panel-heading-icon">＋</div><div><p class="eyebrow">Ampliar catálogo</p><h2>Nuevo producto</h2></div></div>
            <p class="panel-description">Registra el artículo con un código único y sus datos comerciales.</p>
            <form method="POST" action="{{ route('products.store') }}" class="form-grid">
                @csrf
                <label>Código SKU<input name="sku" value="{{ old('sku') }}" required maxlength="64" pattern="[A-Za-z0-9_-]+" placeholder="Ej. WM-CAFE-500"></label>
                <label>Nombre del producto<input name="name" value="{{ old('name') }}" required maxlength="150" placeholder="Ej. Café molido 500 g"></label>
                <label>Categoría<select name="category_id" required><option value="">Selecciona una categoría</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                <label>Proveedor <span class="muted">(opcional)</span><select name="supplier_id"><option value="">Sin proveedor asignado</option>@foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected((string) old('supplier_id') === (string) $supplier->id)>{{ $supplier->name }}</option>@endforeach</select></label>
                <label>Existencia inicial<input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required></label>
                <label>Stock mínimo<input type="number" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" min="0" required></label>
                <label>Precio de compra<input type="number" name="buy_price" value="{{ old('buy_price', 0) }}" min="0" step="0.01" required></label>
                <label>Precio de venta<input type="number" name="sale_price" value="{{ old('sale_price', 0) }}" min="0" step="0.01" required></label>
                <button class="button button-dark" type="submit">Guardar producto <span aria-hidden="true">↗</span></button>
            </form>
        </section>
    @endif

    <section class="panel panel-form panel-movement">
        <div class="panel-heading"><div class="panel-heading-icon icon-orange">⇄</div><div><p class="eyebrow">Actualizar existencias</p><h2>Registrar movimiento</h2></div></div>
        <p class="panel-description">Registra mercancía que entra o sale de tu tienda.</p>
        <form method="POST" action="{{ route('movements.store') }}" class="form-stack">
            @csrf
            <label>Producto<select name="product_id" required><option value="">Selecciona un producto</option>@foreach ($allProducts as $product)<option value="{{ $product->id }}" @selected((string) old('product_id') === (string) $product->id)>{{ $product->name }} ({{ $product->stock }} disponibles)</option>@endforeach</select></label>
            <div class="form-grid movement-form-row">
                <label>Tipo de movimiento<select name="type" required><option value="entry" @selected(old('type', 'entry') === 'entry')>＋ Entrada</option><option value="exit" @selected(old('type') === 'exit')>− Salida / venta</option></select></label>
                <label>Cantidad<input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required></label>
            </div>
            <label>Motivo u observación<input name="notes" value="{{ old('notes') }}" maxlength="500" placeholder="Proveedor, venta o ajuste" required></label>
            <button class="button button-orange" type="submit">Guardar movimiento <span aria-hidden="true">↗</span></button>
        </form>
    </section>
</div>

<section class="panel panel-table" id="productos">
    <div class="section-heading">
        <div><p class="eyebrow">03 / Catálogo</p><h2>Todos los productos</h2><p class="muted section-subtitle">Consulta el código, proveedor, estado y precios de cada artículo.</p></div>
        <form method="GET" action="{{ route('dashboard') }}" class="search">
            <label class="sr-only" for="product-search">Buscar por producto, SKU, categoría o proveedor</label><input id="product-search" name="search" value="{{ $search }}" placeholder="Buscar por nombre, SKU o categoría…">
            <button class="button button-search" type="submit">Buscar</button>
            @if ($search !== '')<a class="button quiet" href="{{ route('dashboard') }}">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-scroll"><table>
        <thead><tr><th scope="col">SKU</th><th scope="col">Producto</th><th scope="col">Categoría</th><th scope="col">Proveedor</th><th scope="col">Existencia</th><th scope="col">Estado</th><th scope="col">Mínimo</th><th scope="col">Compra</th><th scope="col">Venta</th>@if (session('inventory_role') === 'admin' || session('admin_authenticated'))<th scope="col">Acciones</th>@endif</tr></thead>
        <tbody>
            @forelse ($products as $product)
                @php $status = $product->stock <= $product->minimum_stock ? 'Reponer' : 'Disponible'; $statusClass = $product->stock <= $product->minimum_stock ? 'badge-warning' : 'badge-good'; @endphp
                <tr>
                    <td><strong>{{ $product->sku ?: '—' }}</strong></td><td><strong class="product-name">{{ $product->name }}</strong></td><td><span class="category-label">{{ $product->category }}</span></td><td>{{ $product->supplier?->name ?? '—' }}</td><td><strong>{{ $product->stock }}</strong></td>
                    <td><span class="badge {{ $statusClass }}"><i></i>{{ $status }}</span></td><td>{{ $product->minimum_stock }}</td><td>{{ '$' }}{{ number_format((float) $product->buy_price, 2) }}</td><td>{{ '$' }}{{ number_format((float) $product->sale_price, 2) }}</td>
                    @if (session('inventory_role') === 'admin' || session('admin_authenticated'))
                        <td class="actions"><a href="{{ route('products.edit', $product) }}">Editar</a><form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Eliminar</button></form></td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ session('inventory_role') === 'admin' || session('admin_authenticated') ? 10 : 9 }}" class="empty"><span class="empty-mark">▦</span><strong>No hay productos para mostrar.</strong><span>Prueba con otra búsqueda o agrega un producto nuevo.</span></td></tr>
            @endforelse
        </tbody>
    </table></div>
    {{ $products->links() }}
</section>

<section class="panel panel-table recent-panel">
    <div class="section-heading"><div><p class="eyebrow">Actividad reciente</p><h2>Últimos movimientos</h2></div><a class="button button-outline" href="{{ route('movements.index') }}">Ver historial completo <span aria-hidden="true">→</span></a></div>
    <div class="table-scroll"><table>
        <thead><tr><th>Fecha</th><th>Producto</th><th>Movimiento</th><th>Cantidad</th><th>Motivo / observación</th><th>Responsable</th></tr></thead>
        <tbody>
            @forelse ($recentMovements as $movement)
                <tr><td>{{ $movement->created_at->format('d/m/Y H:i') }}</td><td><strong>{{ $movement->product->name }}</strong></td><td><span class="movement-type {{ $movement->type === 'entry' ? 'entry' : 'exit' }}">{{ $movement->type === 'entry' ? 'Entrada' : 'Salida' }}</span></td><td>{{ $movement->quantity }}</td><td>{{ $movement->notes ?: '—' }}</td><td>{{ $movement->created_by ?: 'Histórico anterior' }}</td></tr>
            @empty
                <tr><td colspan="6" class="empty">Todavía no hay movimientos.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</section>
@endsection
