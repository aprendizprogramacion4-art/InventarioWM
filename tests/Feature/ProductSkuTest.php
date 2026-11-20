<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSkuTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_receive_a_stable_unique_sku_automatically(): void
    {
        $first = Product::create([
            'name' => 'Arroz',
            'category' => 'Abarrotes',
            'stock' => 0,
            'minimum_stock' => 5,
            'buy_price' => 1000,
            'sale_price' => 1500,
        ]);
        $second = Product::create([
            'name' => 'Frijoles',
            'category' => 'Abarrotes',
            'stock' => 0,
            'minimum_stock' => 5,
            'buy_price' => 1000,
            'sale_price' => 1500,
        ]);

        $this->assertSame('INV-'.str_pad((string) $first->id, 8, '0', STR_PAD_LEFT), $first->fresh()->sku);
        $this->assertNotSame($first->fresh()->sku, $second->fresh()->sku);
    }

    public function test_admin_can_assign_a_custom_sku_and_search_by_it(): void
    {
        $this->withSession(['admin_authenticated' => true])
            ->post(route('products.store'), [
                'sku' => '  cafe-500  ',
                'name' => 'Café molido',
                'category' => 'Bebidas',
                'stock' => 0,
                'minimum_stock' => 2,
                'buy_price' => 2500,
                'sale_price' => 3200,
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Producto registrado correctamente. Código: CAFE-500.');

        $product = Product::firstOrFail();
        $this->assertSame('CAFE-500', $product->sku);

        $this->withSession(['admin_authenticated' => true])
            ->get(route('dashboard', ['search' => 'CAFE-500']))
            ->assertViewHas('products', fn ($products): bool => $products->total() === 1 && $products->first()->is($product));
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        Product::create([
            'sku' => 'ARROZ-1',
            'name' => 'Arroz blanco',
            'stock' => 0,
            'minimum_stock' => 1,
            'buy_price' => 1000,
            'sale_price' => 1500,
        ]);

        $this->withSession(['admin_authenticated' => true])
            ->post(route('products.store'), [
                'sku' => 'arroz-1',
                'name' => 'Arroz integral',
                'stock' => 0,
                'minimum_stock' => 1,
                'buy_price' => 1000,
                'sale_price' => 1500,
            ])
            ->assertSessionHasErrors('sku');

        $this->assertDatabaseCount('products', 1);
    }
}
