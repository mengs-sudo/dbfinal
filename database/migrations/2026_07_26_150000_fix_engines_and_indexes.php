<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fixes tables that were created with MyISAM engine (which doesn't support
     * foreign keys) and ensures all FK columns have proper indexes so that
     * phpMyAdmin Designer tab can display relationship lines.
     */
    public function up(): void
    {
        // 1. Convert any remaining MyISAM tables to InnoDB
        $dbName = DB::connection()->getDatabaseName();
        $myisamTables = DB::select("
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
              AND ENGINE = 'MyISAM'
              AND TABLE_TYPE = 'BASE TABLE'
        ", [$dbName]);

        foreach ($myisamTables as $table) {
            DB::statement("ALTER TABLE `{$table->TABLE_NAME}` ENGINE = InnoDB");
        }

        // 2. Ensure foreign key constraint exists on inventory_items.category_id -> categories.id
        $fks = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'inventory_items'
              AND COLUMN_NAME = 'category_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$dbName]);

        if (empty($fks) && Schema::hasColumn('inventory_items', 'category_id')) {
            DB::statement("ALTER TABLE `inventory_items`
                ADD CONSTRAINT `inventory_items_category_id_foreign`
                FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
                ON DELETE SET NULL");
        }

        // 3. Add indexes on FK columns that might still be missing
        $this->addIndexIfMissing('inventory_items', 'category_id', 'idx_inv_category_id');
        $this->addIndexIfMissing('product_variants', 'inventory_item_id', 'idx_pv_inventory_item_id');
    }

    /**
     * Add an index on a column only if it doesn't already have one.
     */
    private function addIndexIfMissing(string $table, string $column, string $indexName): void
    {
        $dbName = DB::connection()->getDatabaseName();
        $exists = DB::select("
            SELECT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND INDEX_NAME = ?
        ", [$dbName, $table, $column, $indexName]);

        if (empty($exists) && Schema::hasColumn($table, $column)) {
            Schema::table($table, function (Blueprint $table) use ($column, $indexName) {
                $table->index($column, $indexName);
            });
        }
    }

    public function down(): void
    {
        // Drop indexes added by this migration
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex('idx_inv_category_id');
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('idx_pv_inventory_item_id');
        });
    }
};
