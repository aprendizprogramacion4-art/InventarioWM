<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('suppliers.index', [
            'suppliers' => Supplier::query()->withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:suppliers,name'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('status', 'Proveedor registrado correctamente.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->products()->exists()) {
            return back()->withErrors([
                'supplier' => 'No puedes eliminar un proveedor que tiene productos asociados.',
            ]);
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('status', 'Proveedor eliminado.');
    }
}
