<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index', [
            'categories' => Product::selectRaw('category, COUNT(*) as product_count, SUM(stock) as units, ROUND(AVG(sale_price), 2) as average_price')
                ->groupBy('category')->orderBy('category')->get(),
            'movements' => InventoryMovement::query()
                ->selectRaw("product_id, SUM(CASE WHEN type = 'entry' THEN quantity ELSE 0 END) as entries, SUM(CASE WHEN type = 'exit' THEN quantity ELSE 0 END) as exits")
                ->with('product:id,name,sale_price')->groupBy('product_id')->latest('product_id')->limit(20)->get(),
            'totalEntries' => InventoryMovement::where('type', 'entry')->sum('quantity'),
            'totalExits' => InventoryMovement::where('type', 'exit')->sum('quantity'),
        ]);
    }

    public function csv(): Response
    {
        $products = Product::orderBy('name')->get(['name', 'category', 'stock', 'minimum_stock', 'buy_price', 'sale_price']);
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, ['Producto', 'Categoría', 'Existencias', 'Stock mínimo', 'Precio compra', 'Precio venta']);
        foreach ($products as $product) {
            fputcsv($stream, [$product->name, $product->category, $product->stock, $product->minimum_stock, $product->buy_price, $product->sale_price]);
        }
        rewind($stream);
        $csv = "\xEF\xBB\xBF".stream_get_contents($stream);
        fclose($stream);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="inventario-wm.csv"',
        ]);
    }
}
