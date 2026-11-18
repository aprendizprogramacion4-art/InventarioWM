@extends('layouts.app')
@section('title', 'Historial de movimientos | Inventario WM')
@section('content')
<section class="page-heading">
    <div><p class="eyebrow">Control y trazabilidad / 02</p><h1>Historial de movimientos</h1><p class="muted">Cada entrada y salida, con su fecha, motivo y responsable.</p></div>
    <a class="button button-outline" href="{{ route('dashboard') }}"><span aria-hidden="true">←</span> Volver al inventario</a>
</section>

<section class="panel filter-panel">
    <div class="panel-heading"><div class="panel-heading-icon icon-orange">⌕</div><div><p class="eyebrow">Afinar consulta</p><h2>Filtrar movimientos</h2></div></div>
    <form method="GET" action="{{ route('movements.index') }}" class="movement-filters">
        <label>Producto<select name="product_id"><option value="">Todos los productos</option>@foreach ($products as $product)<option value="{{ $product->id }}" @selected((string) ($filters['product_id'] ?? '') === (string) $product->id)>{{ $product->name }}</option>@endforeach</select></label>
        <label>Tipo de movimiento<select name="type"><option value="">Entradas y salidas</option><option value="entry" @selected(($filters['type'] ?? '') === 'entry')>Entrada</option><option value="exit" @selected(($filters['type'] ?? '') === 'exit')>Salida</option></select></label>
        <label>Desde<input type="date" name="from" value="{{ $filters['from'] ?? '' }}"></label>
        <label>Hasta<input type="date" name="to" value="{{ $filters['to'] ?? '' }}"></label>
        <div class="filter-actions"><button class="button button-dark" type="submit">Aplicar filtros</button><a class="button quiet" href="{{ route('movements.index') }}">Limpiar</a></div>
    </form>
</section>

<section class="panel panel-table">
    <div class="section-heading"><div><p class="eyebrow">Registro completo</p><h2>Movimientos registrados</h2><p class="muted result-count">{{ $movements->total() }} registros encontrados</p></div></div>
    <div class="table-scroll"><table>
        <thead><tr><th scope="col">Fecha y hora</th><th scope="col">Producto</th><th scope="col">Tipo</th><th scope="col">Cantidad</th><th scope="col">Motivo / observación</th><th scope="col">Responsable</th></tr></thead>
        <tbody>
            @forelse ($movements as $movement)
                <tr><td>{{ $movement->created_at->format('d/m/Y H:i') }}</td><td><strong>{{ $movement->product?->name ?? 'Producto no disponible' }}</strong></td><td><span class="movement-type {{ $movement->type === 'entry' ? 'entry' : 'exit' }}">{{ $movement->type === 'entry' ? 'Entrada' : 'Salida' }}</span></td><td><strong>{{ $movement->quantity }}</strong></td><td>{{ $movement->notes ?: '—' }}</td><td>{{ $movement->created_by ?: 'Histórico anterior' }}</td></tr>
            @empty
                <tr><td colspan="6" class="empty"><span class="empty-mark">↔</span><strong>No hay movimientos que coincidan.</strong><span>Modifica los filtros para consultar otros registros.</span></td></tr>
            @endforelse
        </tbody>
    </table></div>
    {{ $movements->links() }}
</section>
@endsection
