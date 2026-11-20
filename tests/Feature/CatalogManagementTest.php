<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_category(): void
    {
        $this->withSession(['admin_authenticated' => true])
            ->post(route('categories.store'), [
                'name' => 'Aseo',
                'description' => 'Productos de limpieza',
            ])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Aseo',
            'description' => 'Productos de limpieza',
        ]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::create(['name' => 'Abarrotes']);

        Product::create([
            'sku' => 'WM-ARROZ-003',
            'name' => 'Arroz',
            'category' => 'Abarrotes',
            'category_id' => $category->id,
            'stock' => 0,
            'minimum_stock' => 1,
            'buy_price' => 1000,
            'sale_price' => 1500,
        ]);

        $this->withSession(['admin_authenticated' => true])
            ->delete(route('categories.destroy', $category))
            ->assertRedirect()
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_create_a_supplier(): void
    {
        $this->withSession(['admin_authenticated' => true])
            ->post(route('suppliers.store'), [
                'name' => 'Distribuciones WM',
                'contact_name' => 'María Pérez',
                'phone' => '3001234567',
                'email' => 'ventas@example.com',
                'address' => 'Barranquilla',
            ])
            ->assertRedirect(route('suppliers.index'));

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Distribuciones WM',
            'contact_name' => 'María Pérez',
            'phone' => '3001234567',
            'email' => 'ventas@example.com',
        ]);
    }

    public function test_supplier_with_products_cannot_be_deleted(): void
    {
        $supplier = Supplier::create(['name' => 'Proveedor principal']);
        $category = Category::create(['name' => 'Bebidas']);

        Product::create([
            'sku' => 'WM-JUGO-001',
            'name' => 'Jugo',
            'category' => 'Bebidas',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'stock' => 0,
            'minimum_stock' => 1,
            'buy_price' => 1000,
            'sale_price' => 1500,
        ]);

        $this->withSession(['admin_authenticated' => true])
            ->delete(route('suppliers.destroy', $supplier))
            ->assertRedirect()
            ->assertSessionHasErrors('supplier');

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }

    public function test_employee_cannot_manage_categories_or_suppliers(): void
    {
        $this->withSession([
            'inventory_authenticated' => true,
            'inventory_role' => 'employee',
        ]);

        $this->get(route('categories.index'))->assertForbidden();
        $this->get(route('suppliers.index'))->assertForbidden();

        $this->post(route('categories.store'), ['name' => 'No autorizada'])
            ->assertForbidden();
        $this->post(route('suppliers.store'), ['name' => 'No autorizado'])
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', ['name' => 'No autorizada']);
        $this->assertDatabaseMissing('suppliers', ['name' => 'No autorizado']);
    }
}
