<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('contact_name', 120)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 64)->nullable()->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
        });

        $timestamp = now();
        $defaultCategoryId = DB::table('categories')->insertGetId([
            'name' => 'General',
            'description' => 'Categoría predeterminada',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        foreach (DB::table('products')->orderBy('id')->get(['id', 'category']) as $product) {
            $categoryName = trim((string) $product->category);

            if ($categoryName === '') {
                $categoryName = 'General';
            }

            $categoryId = DB::table('categories')->where('name', $categoryName)->value('id');

            if ($categoryId === null) {
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => $categoryName,
                    'description' => null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }

            DB::table('products')->where('id', $product->id)->update([
                'sku' => 'WM-'.str_pad((string) $product->id, 6, '0', STR_PAD_LEFT),
                'category_id' => $categoryId ?: $defaultCategoryId,
                'category' => $categoryName,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropUnique(['sku']);
            $table->dropColumn(['sku', 'category_id', 'supplier_id']);
        });

        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
    }
};
