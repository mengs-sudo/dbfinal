<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensures all tables use InnoDB engine and adds explicit indexes
     * on foreign key columns so phpMyAdmin Designer can show relationship lines.
     */
    public function up(): void
    {
        // Ensure all tables use InnoDB engine for FK support
        $tables = [
            'suppliers', 'customers', 'inventory_items',
            'purchase_orders', 'purchase_items',
            'sales_orders', 'sales_items',
            'payments', 'receipts',
            'categories', 'product_variants',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table}` ENGINE = InnoDB");
        }

        // Add explicit indexes on FK columns for phpMyAdmin Designer visibility

        // purchase_orders -> suppliers, users
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index('supplier_id', 'idx_po_supplier_id');
            $table->index('created_by', 'idx_po_created_by');
        });

        // purchase_items -> purchase_orders, inventory_items
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->index('purchase_order_id', 'idx_pi_purchase_order_id');
            $table->index('inventory_item_id', 'idx_pi_inventory_item_id');
        });

        // sales_orders -> customers, users
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->index('customer_id', 'idx_so_customer_id');
            $table->index('created_by', 'idx_so_created_by');
        });

        // sales_items -> sales_orders, inventory_items
        Schema::table('sales_items', function (Blueprint $table) {
            $table->index('sales_order_id', 'idx_si_sales_order_id');
            $table->index('inventory_item_id', 'idx_si_inventory_item_id');
        });

        // payments -> purchase_orders, sales_orders, users
        Schema::table('payments', function (Blueprint $table) {
            $table->index('purchase_order_id', 'idx_pay_purchase_order_id');
            $table->index('sales_order_id', 'idx_pay_sales_order_id');
            $table->index('created_by', 'idx_pay_created_by');
        });

        // receipts -> sales_orders, payments, users
        Schema::table('receipts', function (Blueprint $table) {
            $table->index('sales_order_id', 'idx_rec_sales_order_id');
            $table->index('payment_id', 'idx_rec_payment_id');
            $table->index('created_by', 'idx_rec_created_by');
        });

        // inventory_items -> categories
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->index('category_id', 'idx_inv_category_id');
        });

        // product_variants -> inventory_items
        Schema::table('product_variants', function (Blueprint $table) {
            $table->index('inventory_item_id', 'idx_pv_inventory_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex('idx_po_supplier_id');
            $table->dropIndex('idx_po_created_by');
        });
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropIndex('idx_pi_purchase_order_id');
            $table->dropIndex('idx_pi_inventory_item_id');
        });
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropIndex('idx_so_customer_id');
            $table->dropIndex('idx_so_created_by');
        });
        Schema::table('sales_items', function (Blueprint $table) {
            $table->dropIndex('idx_si_sales_order_id');
            $table->dropIndex('idx_si_inventory_item_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_pay_purchase_order_id');
            $table->dropIndex('idx_pay_sales_order_id');
            $table->dropIndex('idx_pay_created_by');
        });
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropIndex('idx_rec_sales_order_id');
            $table->dropIndex('idx_rec_payment_id');
            $table->dropIndex('idx_rec_created_by');
        });
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex('idx_inv_category_id');
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('idx_pv_inventory_item_id');
        });
    }
};
