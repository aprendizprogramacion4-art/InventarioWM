<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'category', 'stock', 'minimum_stock', 'buy_price', 'sale_price'];

    protected function casts(): array
    {
        return ['stock' => 'integer', 'minimum_stock' => 'integer', 'buy_price' => 'decimal:2', 'sale_price' => 'decimal:2'];
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
