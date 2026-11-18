<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Existing rows remain null because their historical balances cannot be
            // reconstructed reliably from the current product stock alone.
            $table->unsignedInteger('stock_before')->nullable()->after('quantity');
            $table->unsignedInteger('stock_after')->nullable()->after('stock_before');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn(['stock_before', 'stock_after']);
        });
    }
};
