<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->string('registered_by', 100)->nullable()->after('notes')->comment('Usuario que registró el movimiento');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn('registered_by');
        });
    }
};
