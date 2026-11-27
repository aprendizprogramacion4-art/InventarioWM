@extends('layouts.app')
@section('title', 'Editar producto | Inventario WM')
@section('content')
<section class="page-heading">
    <div><p class="eyebrow">Catálogo / Modificar artículo</p><h1>Editar producto</h1><p class="muted">Actualiza la información comercial sin alterar el historial de existencias.</p></div>
    <a class="button button-outline" href="{{ route('dashboard') }}"><span aria-hidden="true">←</span> Volver al inventario</a>
</section>
<section class="panel form-panel edit-panel">
    <div class="panel-heading"><div class="panel-heading-icon">✎</div><div><p class="eyebrow">Ficha del producto · <code>{{ $product->sku }}</code></p><h2>{{ $product->name }}</h2></div></div>
    <form method="POST" action="{{ route('products.update', $product) }}" class="form-grid edit-form">
        @csrf @method('PUT')
        <label class="field-wide">Nombre del producto<input name="name" value="{{ old('name', $product->name) }}" required maxlength="150"></label>
        <label>Categoría<input name="category" value="{{ old('category', $product->category) }}" maxlength="100"></label>
        <label>Stock mínimo<input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}" min="0" required></label>
        <label>Precio de compra<input type="number" name="buy_price" value="{{ old('buy_price', $product->buy_price) }}" min="0" step="0.01" required></label>
        <label>Precio de venta<input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="0.01" required></label>
        <div class="stock-note"><span>Existencia actual</span><strong>{{ $product->stock }} <small>unidades</small></strong><p>Para ajustar el stock, utiliza el formulario de movimientos y conserva el historial.</p></div>
        <div class="form-actions"><button class="button button-dark" type="submit">Guardar cambios <span aria-hidden="true">↗</span></button><a class="button quiet" href="{{ route('dashboard') }}">Cancelar</a></div>
    </form>
</section>
@endsection
