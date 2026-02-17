<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'sales', 'sale_payments', 'sale_returns', 'sale_return_payments',
            'purchases', 'purchase_payments', 'purchase_returns', 'purchase_return_payments',
            'quotations', 'products', 'categories', 'brands', 'expenses', 'expense_categories',
            'suppliers', 'customers', 'adjustments'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'sales', 'sale_payments', 'sale_returns', 'sale_return_payments',
            'purchases', 'purchase_payments', 'purchase_returns', 'purchase_return_payments',
            'quotations', 'products', 'categories', 'brands', 'expenses', 'expense_categories',
            'suppliers', 'customers', 'adjustments'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['business_id']);
                    $table->dropColumn('business_id');
                });
            }
        }
    }
};
