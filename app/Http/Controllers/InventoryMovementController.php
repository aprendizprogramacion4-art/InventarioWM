<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class InventoryMovementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'type' => ['nullable', 'in:entry,exit'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $movements = InventoryMovement::query()
            ->with('product:id,name')
            ->when($filters['product_id'] ?? null, fn ($query, $productId) => $query->where('product_id', $productId))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('movements.index', [
            'movements' => $movements,
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', 'in:entry,exit'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['required', 'string', 'max:500'],
        ]);
        $data['created_by'] = (string) config('inventory.admin_username', 'admin');

        $error = DB::transaction(function () use ($data) {
            $product = Product::whereKey($data['product_id'])->lockForUpdate()->firstOrFail();
            if ($data['type'] === 'exit' && $product->stock < $data['quantity']) {
                return 'Stock insuficiente para '.$product->name.'.';
            }

            $movement = new InventoryMovement($data);

            if (!$movement->save()) {
                throw new RuntimeException('No se pudo registrar el movimiento de inventario.');
            }

            $product->stock += $data['type'] === 'entry' ? $data['quantity'] : -$data['quantity'];

            if (!$product->save()) {
                throw new RuntimeException('No se pudo actualizar las existencias del producto.');
            }

            return null;
        });

        if ($error) {
            return back()->withErrors(['quantity' => $error])->withInput();
        }

        return redirect()->route('dashboard')->with('status', 'Movimiento de inventario registrado.');
    }
}
