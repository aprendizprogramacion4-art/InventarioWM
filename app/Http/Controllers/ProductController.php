<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $products = Product::query()
            ->with(['supplier:id,name', 'categoryRecord:id,name'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', "%{$search}%"));
            }))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $allProducts = Product::query()->orderBy('name')->get(['id', 'name', 'stock']);

        return view('dashboard', [
            'products' => $products,
            'allProducts' => $allProducts,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'suppliers' => Supplier::query()->orderBy('name')->get(['id', 'name']),
            'recentMovements' => InventoryMovement::with('product')->latest()->limit(8)->get(),
            'totalProducts' => Product::count(),
            'totalUnits' => Product::sum('stock'),
            'lowStockCount' => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'inventoryValue' => Product::selectRaw('COALESCE(SUM(stock * buy_price), 0) as total')->value('total'),
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'sku' => Str::upper(trim((string) $request->input('sku'))),
        ]);

        $data = $request->validate([
            'sku' => ['required', 'alpha_dash', 'max:64', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
        ]);
        $data['category'] = Category::findOrFail($data['category_id'])->name;

        DB::transaction(function () use ($data): void {
            $product = new Product($data);

            if (!$product->save()) {
                throw new RuntimeException('No se pudo registrar el producto.');
            }

            if ($product->stock > 0) {
                $movement = new InventoryMovement([
                    'product_id' => $product->id,
                    'type' => 'entry',
                    'quantity' => $product->stock,
                    'stock_before' => 0,
                    'stock_after' => (int) $product->stock,
                    'notes' => 'Existencia inicial del producto',
                    'created_by' => (string) session('inventory_username', config('inventory.admin_username', 'admin')),
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
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'suppliers' => Supplier::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->merge([
            'sku' => Str::upper(trim((string) $request->input('sku'))),
        ]);

        $data = $request->validate([
            'sku' => ['required', 'alpha_dash', 'max:64', Rule::unique('products', 'sku')->ignore($product->id)],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
        ]);
        $data['category'] = Category::findOrFail($data['category_id'])->name;
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
