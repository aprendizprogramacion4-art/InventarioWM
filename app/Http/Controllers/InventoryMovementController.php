<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $error = DB::transaction(function () use ($data) {
            $product = Product::whereKey($data['product_id'])->lockForUpdate()->firstOrFail();
            if ($data['type'] === 'exit' && $product->stock < $data['quantity']) {
                return 'Stock insuficiente para '.$product->name.'.';
            }

            InventoryMovement::create($data);
            $product->stock += $data['type'] === 'entry' ? $data['quantity'] : -$data['quantity'];
            $product->save();

            return null;
        });

        if ($error) {
            return back()->withErrors(['quantity' => $error])->withInput();
        }

        return redirect()->route('dashboard')->with('status', 'Movimiento de inventario registrado.');
    }
}
