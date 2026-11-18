@extends('layouts.app')
@section('title', 'Editar producto | Inventario WM')
@section('content')
<section class="page-heading">
    <div><p class="eyebrow">Catálogo / Modificar artículo</p><h1>Editar producto</h1><p class="muted">Actualiza la ficha comercial sin alterar el historial de existencias.</p></div>
    <a class="button button-outline" href="{{ route('dashboard') }}"><span aria-hidden="true">←</span> Volver al inventario</a>
</section>
<section class="panel form-panel edit-panel">
    <div class="panel-heading"><div class="panel-heading-icon">✎</div><div><p class="eyebrow">Ficha del producto</p><h2>{{ $product->name }}</h2></div></div>
    <form method="POST" action="{{ route('products.update', $product) }}" class="form-grid edit-form">
        @csrf @method('PUT')
        <label>Código SKU<input name="sku" value="{{ old('sku', $product->sku) }}" required maxlength="64" pattern="[A-Za-z0-9_-]+" placeholder="Ej. WM-CAFE-500"></label>
        <label>Nombre del producto<input name="name" value="{{ old('name', $product->name) }}" required maxlength="150"></label>
        <label>Categoría<select name="category_id" required><option value="">Selecciona una categoría</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label>Proveedor <span class="muted">(opcional)</span><select name="supplier_id"><option value="">Sin proveedor asignado</option>@foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $product->supplier_id) === (string) $supplier->id)>{{ $supplier->name }}</option>@endforeach</select></label>
        <label>Stock mínimo<input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}" min="0" required></label>
        <label>Precio de compra<input type="number" name="buy_price" value="{{ old('buy_price', $product->buy_price) }}" min="0" step="0.01" required></label>
        <label>Precio de venta<input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="0.01" required></label>
        <div class="stock-note"><span>Existencia actual</span><strong>{{ $product->stock }} <small>unidades</small></strong><p>Para ajustar el stock, utiliza el formulario de movimientos y conserva el historial.</p></div>
        <div class="form-actions"><button class="button button-dark" type="submit">Guardar cambios <span aria-hidden="true">↗</span></button><a class="button quiet" href="{{ route('dashboard') }}">Cancelar</a></div>
    </form>
</section>
@endsection
