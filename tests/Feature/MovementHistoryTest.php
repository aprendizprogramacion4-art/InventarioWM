<?php

namespace Tests\Feature;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovementHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_movement_history_by_product_and_type(): void
    {
        $rice = $this->createProduct('Arroz');
        $milk = $this->createProduct('Leche');

        InventoryMovement::create([
            'product_id' => $rice->id,
            'type' => 'entry',
            'quantity' => 10,
            'notes' => 'Compra al proveedor',
            'created_by' => 'admin',
            'stock_before' => 5,
            'stock_after' => 15,
        ]);
        InventoryMovement::create([
            'product_id' => $milk->id,
            'type' => 'exit',
            'quantity' => 2,
            'notes' => 'Venta',
            'created_by' => 'admin',
        ]);

        $response = $this->withSession(['admin_authenticated' => true])
            ->get(route('movements.index', [
                'product_id' => $rice->id,
                'type' => 'entry',
            ]))
            ->assertOk()
            ->assertSee('Arroz')
            ->assertSee('Compra al proveedor')
            ->assertSee('admin')
            ->assertSee('Saldo anterior')
            ->assertSee('Saldo posterior')
            ->assertSee('15');

        // The product filter must still list every product. Check only the
        // results table to ensure unrelated movements are excluded.
        preg_match('/<tbody>(.*?)<\\/tbody>/s', $response->getContent(), $tableBody);
        $this->assertNotEmpty($tableBody[1] ?? null);
        $this->assertStringNotContainsString('Leche', $tableBody[1]);
        $this->assertStringNotContainsString('Venta', $tableBody[1]);
    }

    public function test_legacy_movements_without_balance_snapshots_remain_readable(): void
    {
        $product = $this->createProduct('Producto histórico');

        InventoryMovement::create([
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 5,
            'notes' => 'Movimiento anterior a la trazabilidad',
        ]);

        $this->withSession(['admin_authenticated' => true])
            ->get(route('movements.index'))
            ->assertOk()
            ->assertSee('Movimiento anterior a la trazabilidad')
            ->assertSee('No disponible');
    }

    public function test_guest_cannot_open_movement_history(): void
    {
        $this->get(route('movements.index'))
            ->assertRedirect(route('login'));
    }

    private function createProduct(string $name): Product
    {
        return Product::create([
            'name' => $name,
            'category' => 'Pruebas',
            'stock' => 5,
            'minimum_stock' => 1,
            'buy_price' => 1000,
            'sale_price' => 1500,
        ]);
    }
}
