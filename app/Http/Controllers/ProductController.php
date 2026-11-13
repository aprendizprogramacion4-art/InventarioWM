<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $isAdmin = $request->session()->get('role') === 'admin';
        $search = trim((string) $request->query('search', ''));
        $productColumns = ['id', 'name', 'category', 'stock', 'minimum_stock', 'sale_price'];

        if ($isAdmin) {
            $productColumns[] = 'buy_price';
        }

        $products = Product::query()
            ->select($productColumns)
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('dashboard', [
            'products' => $products,
            'allProducts' => Product::query()->orderBy('name')->get(['id', 'name', 'stock']),
            'recentMovements' => InventoryMovement::with('product')->latest()->limit(8)->get(),
            'totalProducts' => Product::count(),
            'totalUnits' => Product::sum('stock'),
            'lowStockCount' => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'inventoryValue' => $isAdmin
                ? Product::selectRaw('COALESCE(SUM(stock * buy_price), 0) as total')->value('total')
                : null,
            'search' => $search,
            'isAdmin' => $isAdmin,
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

        DB::transaction(function () use ($data, $request): void {
            $product = Product::create($data);

            if ($product->stock > 0) {
                $movement = new InventoryMovement([
                    'product_id' => $product->id,
                    'type' => 'entry',
                    'quantity' => $product->stock,
                    'notes' => 'Existencia inicial del producto',
                    'registered_by' => $request->session()->get('admin_username', 'Sistema'),
                ]);

                if (!$movement->save()) {
                    throw new RuntimeException('No se pudo registrar la existencia inicial.');
                }
            }
        });

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
