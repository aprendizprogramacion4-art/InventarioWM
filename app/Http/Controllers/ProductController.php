<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $products = Product::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('dashboard', [
            'products' => $products,
            'recentMovements' => \App\Models\InventoryMovement::with('product')->latest()->limit(8)->get(),
            'totalProducts' => Product::count(),
            'totalUnits' => Product::sum('stock'),
            'lowStockCount' => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'inventoryValue' => Product::selectRaw('COALESCE(SUM(stock * buy_price), 0) as total')->value('total'),
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
        ]);
        $data['category'] = $data['category'] ?: 'General';
        Product::create($data);

        return redirect()->route('dashboard')->with('status', 'Producto registrado correctamente.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
        ]);
        $data['category'] = $data['category'] ?: 'General';
        $product->update($data);

        return redirect()->route('dashboard')->with('status', 'Producto actualizado.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->movements()->exists()) {
            return back()->withErrors(['product' => 'No se puede eliminar un producto con movimientos registrados.']);
        }

        $product->delete();

        return redirect()->route('dashboard')->with('status', 'Producto eliminado.');
    }
}
