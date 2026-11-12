@extends('layouts.app')
@section('title', 'Ventas | Inventario WM')
@section('content')
<section class="page-heading compact">
    <div>
        <p class="eyebrow">Ventas del día</p>
        <h1>Ventas</h1>
        <p class="muted">Registra las ventas y revisa los ingresos del negocio.</p>
    </div>
</section>

<div class="columns">
    <section class="panel">
        <h2>Registrar venta</h2>
        <form method="POST" action="{{ route('sales.store') }}" class="form-grid">
            @csrf
            <label>
                Producto
                <select name="product_id" required>
                    <option value="">Selecciona producto</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (stock: {{ $product->stock }})</option>
                    @endforeach
                </select>
            </label>
            <label>
                Cantidad
                <input type="number" name="quantity" value="1" min="1" required>
            </label>
            <label>
                Fecha
                <input type="date" name="sale_date" value="{{ old('sale_date', now()->toDateString()) }}" required>
            </label>
            <label>
                Nota
                <input name="notes" value="{{ old('notes') }}" maxlength="255" placeholder="Cliente o detalle">
            </label>
            <button class="button" type="submit">Guardar venta</button>
        </form>
    </section>

    <section class="panel">
        <h2>Historial</h2>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                            <td>{{ $sale->product->name }}</td>
                            <td>{{ $sale->quantity }}</td>
                            <td>${{ number_format((float) $sale->total, 2) }}</td>
                            <td>{{ $sale->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $sales->links() }}
    </section>
</div>
@endsection
