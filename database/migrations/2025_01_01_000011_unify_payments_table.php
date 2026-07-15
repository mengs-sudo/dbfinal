<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Make purchase_order_id nullable
            $table->foreignId('purchase_order_id')->nullable()->change();

            // Add type and sales_order_id
            $table->string('type', 20)->default('purchase')->after('payment_number')
                  ->comment('purchase = pay to supplier, sales = receive from customer');
            $table->foreignId('sales_order_id')->nullable()->after('purchase_order_id')
                  ->constrained('sales_orders')->onDelete('cascade');

            // Add reference to who the payment is from/to
            $table->string('entity_name', 125)->nullable()->after('type')
                  ->comment('Supplier or Customer name for display');

            // Drop the old unique constraint on payment_number if it exists, recreate
            $table->dropUnique(['payment_number']);
        });

        // Re-add unique with shorter index
        Schema::table('payments', function (Blueprint $table) {
            $table->unique('payment_number');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['type', 'sales_order_id', 'entity_name']);
            $table->dropUnique(['payment_number']);
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unique('payment_number');
        });
    }
};
