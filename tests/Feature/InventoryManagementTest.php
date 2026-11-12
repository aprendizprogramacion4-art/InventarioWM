<?php

namespace Tests\Feature;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_register_a_product(): void
    {
        $this->post(route('products.store'), [
            'name' => 'Arroz',
            'category' => 'Abarrotes',
            'stock' => 10,
            'minimum_stock' => 2,
            'buy_price' => 2500,
            'sale_price' => 3200,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_register_a_product_and_its_initial_stock_movement(): void
    {
        $this->withSession(['admin_authenticated' => true])
            ->post(route('products.store'), [
                'name' => 'Arroz',
                'category' => 'Abarrotes',
                'stock' => 10,
                'minimum_stock' => 2,
                'buy_price' => 2500,
                'sale_price' => 3200,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('products', [
            'name' => 'Arroz',
            'category' => 'Abarrotes',
            'stock' => 10,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => Product::where('name', 'Arroz')->value('id'),
            'type' => 'entry',
            'quantity' => 10,
            'notes' => 'Existencia inicial del producto',
        ]);

        $this->assertDatabaseCount('inventory_movements', 1);
    }

    public function test_admin_can_register_a_product_without_an_initial_stock_movement(): void
    {
        $this->withSession(['admin_authenticated' => true])
            ->post(route('products.store'), [
                'name' => 'Leche',
                'category' => 'Lácteos',
                'stock' => 0,
                'minimum_stock' => 2,
                'buy_price' => 2500,
                'sale_price' => 3200,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('products', [
            'name' => 'Leche',
            'stock' => 0,
        ]);

        $this->assertDatabaseCount('inventory_movements', 0);
    }

    public function test_product_creation_is_rolled_back_when_initial_movement_cannot_be_saved(): void
    {
        InventoryMovement::creating(static function (InventoryMovement $movement): bool {
            return false;
        });

        $this->withoutExceptionHandling();

        try {
            $this->withSession(['admin_authenticated' => true])
                ->post(route('products.store'), [
                    'name' => 'Azúcar',
                    'category' => 'Abarrotes',
                    'stock' => 8,
                    'minimum_stock' => 2,
                    'buy_price' => 3000,
                    'sale_price' => 3800,
                ]);

            $this->fail('The product creation should fail when the initial movement cannot be saved.');
        } catch (RuntimeException $exception) {
            $this->assertSame('No se pudo registrar la existencia inicial.', $exception->getMessage());
        }

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('inventory_movements', 0);
    }

    public function test_movement_selector_includes_products_beyond_the_first_page(): void
    {
        $lastProduct = null;

        foreach (range(1, 13) as $number) {
            $lastProduct = $this->createNamedProduct(sprintf('Producto %02d', $number));
        }

        $this->withSession(['admin_authenticated' => true])
            ->get(route('dashboard'))
            ->assertViewHas('products', fn ($products): bool => $products->count() === 12 && $products->total() === 13)
            ->assertViewHas('allProducts', fn ($allProducts): bool => $allProducts->count() === 13)
            ->assertSee('<option value="'.$lastProduct->id.'">Producto 13 (0)</option>', false);
    }

    public function test_movement_selector_is_not_limited_by_product_search(): void
    {
        $this->createNamedProduct('Alpha');
        $beta = $this->createNamedProduct('Beta');

        $this->withSession(['admin_authenticated' => true])
            ->get(route('dashboard', ['search' => 'Alpha']))
            ->assertViewHas('products', fn ($products): bool => $products->total() === 1)
            ->assertViewHas('allProducts', fn ($allProducts): bool => $allProducts->count() === 2)
            ->assertSee('<option value="'.$beta->id.'">Beta (0)</option>', false);
    }

    public function test_inventory_entry_increases_stock_and_creates_a_movement(): void
    {
        $product = $this->createProduct(stock: 10);

        $this->withSession(['admin_authenticated' => true])
            ->post(route('movements.store'), [
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => 5,
                'notes' => 'Compra de proveedor',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 15,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 5,
            'notes' => 'Compra de proveedor',
        ]);
    }

    public function test_exit_larger_than_available_stock_is_rejected(): void
    {
        $product = $this->createProduct(stock: 3);

        $this->withSession(['admin_authenticated' => true])
            ->post(route('movements.store'), [
                'product_id' => $product->id,
                'type' => 'exit',
                'quantity' => 4,
                'notes' => 'Salida de prueba',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);

        $this->assertDatabaseCount('inventory_movements', 0);
    }

    public function test_product_with_registered_movements_cannot_be_deleted(): void
    {
        $product = $this->createProduct(stock: 5);

        InventoryMovement::create([
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 5,
            'notes' => 'Registro inicial de prueba',
        ]);

        $this->withSession(['admin_authenticated' => true])
            ->delete(route('products.destroy', $product))
            ->assertRedirect()
            ->assertSessionHasErrors('product');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    private function createNamedProduct(string $name): Product
    {
        return Product::create([
            'name' => $name,
            'category' => 'Pruebas',
            'stock' => 0,
            'minimum_stock' => 2,
            'buy_price' => 1000,
            'sale_price' => 1500,
        ]);
    }

    private function createProduct(int $stock): Product
    {
        return Product::create([
            'name' => 'Producto de prueba',
            'category' => 'Pruebas',
            'stock' => $stock,
            'minimum_stock' => 2,
            'buy_price' => 1000,
            'sale_price' => 1500,
        ]);
    }
}
