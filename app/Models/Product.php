<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
improve/project-quality-baseline
    protected $fillable = [
        'sku',
        'name',
        'category',
        'category_id',
        'supplier_id',
        'stock',
        'minimum_stock',
        'buy_price',
        'sale_price',
    ];

    protected $fillable = ['sku', 'name', 'category', 'stock', 'minimum_stock', 'buy_price', 'sale_price'];
main

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'minimum_stock' => 'integer',
            'buy_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
        ];
    }

    public function categoryRecord(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    protected static function booted(): void
    {
        static::created(function (Product $product): void {
            if (blank($product->sku)) {
                $product->forceFill([
                    'sku' => 'INV-'.str_pad((string) $product->getKey(), 8, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
