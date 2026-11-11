@extends('layouts.app')
@section('title', 'Editar producto | Inventario WM')
@section('content')
<section class="page-heading"><div><p class="eyebrow">Administración</p><h1>Editar producto</h1></div><a class="button secondary" href="{{ route('dashboard') }}">Volver</a></section>
<section class="panel"><form method="POST" action="{{ route('products.update', $product) }}" class="form-grid">@csrf @method('PUT')
<label>Nombre<input name="name" value="{{ old('name', $product->name) }}" required maxlength="150"></label>
<label>Categoría<input name="category" value="{{ old('category', $product->category) }}"></label>
<label>Stock mínimo<input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}" min="0" required></label>
<label>Precio compra<input type="number" name="buy_price" value="{{ old('buy_price', $product->buy_price) }}" min="0" step="0.01" required></label>
<label>Precio venta<input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="0.01" required></label>
<p class="muted">Existencia actual: <strong>{{ $product->stock }}</strong>. Usa el formulario de movimientos para ajustarla y conservar el historial.</p>
<button class="button" type="submit">Guardar cambios</button></form></section>
@endsection
