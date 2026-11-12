<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->orderBy('name')->get();
        $sales = Sale::query()->with('product')->latest()->paginate(10);

        return view('sales.index', compact('products', 'sales'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'sale_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $error = DB::transaction(function () use ($data) {
            $product = Product::whereKey($data['product_id'])->lockForUpdate()->firstOrFail();

            if ($product->stock < $data['quantity']) {
                return 'No hay suficiente stock para vender '.$product->name.'.';
            }

            $unitPrice = (float) $product->sale_price;
            $total = $data['quantity'] * $unitPrice;

            Sale::create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'unit_price' => $unitPrice,
                'total' => $total,
                'sale_date' => $data['sale_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            $product->stock -= $data['quantity'];
            $product->save();

            return null;
        });

        if ($error) {
            return back()->withErrors(['quantity' => $error])->withInput();
        }

        return redirect()->route('sales.index')->with('status', 'Venta registrada correctamente.');
    }
}
