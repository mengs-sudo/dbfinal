<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->string('variant_name', 50);   // e.g. "Small", "Red / L", "500ml"
            $table->string('sku', 40)->nullable()->unique();
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->timestamps();

            // The same item shouldn't have two variants with the identical name (e.g. two "Medium").
            $table->unique(['inventory_item_id', 'variant_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};