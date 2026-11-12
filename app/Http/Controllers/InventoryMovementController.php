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
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', 'in:entry,exit'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $error = DB::transaction(function () use ($data): ?string {
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
                throw new RuntimeException('No se pudo actualizar la existencia del producto.');
            }

            return null;
        });

        if ($error) {
            return back()->withErrors(['quantity' => $error])->withInput();
        }

        return redirect()->route('dashboard')->with('status', 'Movimiento de inventario registrado.');
    }
}
