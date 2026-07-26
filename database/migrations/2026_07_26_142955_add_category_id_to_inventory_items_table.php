<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $schemaBuilder = Schema::getFacadeRoot();

        // ADD category_id only if it doesn't already exist
        if (!$schemaBuilder->hasColumn('inventory_items', 'category_id')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('category')
                    ->constrained('categories')->nullOnDelete();
            });
        }

        // Backfill: turn every distinct existing free-text category value into a
        // real Category row, then point each inventory item at it.
        $distinctCategories = DB::table('inventory_items')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($distinctCategories as $categoryName) {
            $categoryId = DB::table('categories')->where('name', $categoryName)->value('id');

            if (!$categoryId) {
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => $categoryName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('inventory_items')
                ->where('category', $categoryName)
                ->update(['category_id' => $categoryId]);
        }

        // DROP the old free-text column if it still exists
        if ($schemaBuilder->hasColumn('inventory_items', 'category')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('category', 50)->nullable()->after('item_name');
        });

        // Best-effort restore of the text value from the related category.
        $items = DB::table('inventory_items')
            ->whereNotNull('category_id')
            ->select('id', 'category_id')
            ->get();

        foreach ($items as $item) {
            $name = DB::table('categories')->where('id', $item->category_id)->value('name');
            if ($name) {
                DB::table('inventory_items')->where('id', $item->id)->update(['category' => $name]);
            }
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};