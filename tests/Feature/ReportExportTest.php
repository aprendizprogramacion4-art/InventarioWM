<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_export_neutralizes_spreadsheet_formulas_in_text_fields(): void
    {
        $supplier = Supplier::create(['name' => '+SUM(2,2)']);

        Product::create([
            'name' => '=HYPERLINK("https://example.com")',
            'supplier_id' => $supplier->id,
            'category' => '+SUM(1,1)',
            'stock' => 4,
            'minimum_stock' => 2,
            'buy_price' => 1000,
            'sale_price' => 1500,
        ]);

        $response = $this->withSession(['admin_authenticated' => true])
            ->get(route('reports.csv'));

        $response->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->assertStringContainsString("'=HYPERLINK", $response->getContent());
        $this->assertStringContainsString("'+SUM", $response->getContent());
        $this->assertStringContainsString("'+SUM(2,2)", $response->getContent());
        $this->assertStringContainsString("SKU", $response->getContent());
    }
}
