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
        // Add store_id to debtors
        if (Schema::hasTable('debtors') && !Schema::hasColumn('debtors', 'store_id')) {
            Schema::table('debtors', function (Blueprint $table) {
                $table->foreignId('store_id')->nullable()->after('business_id')
                      ->constrained('stores')->nullOnDelete();
            });
        }

        // Add store_id to creditors
        if (Schema::hasTable('creditors') && !Schema::hasColumn('creditors', 'store_id')) {
            Schema::table('creditors', function (Blueprint $table) {
                $table->foreignId('store_id')->nullable()->after('business_id')
                      ->constrained('stores')->nullOnDelete();
            });
        }

        // Add store_id to brands and update unique constraint
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if (!Schema::hasColumn('brands', 'store_id')) {
                    $table->foreignId('store_id')->nullable()->after('business_id')
                          ->constrained('stores')->nullOnDelete();
                }

                // Drop old business-level unique if it exists
                try {
                    $table->dropUnique(['brand_code', 'business_id']);
                } catch (\Exception $e) {}

                // Add store-level unique
                $table->unique(['brand_code', 'store_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropUnique(['brand_code', 'store_id']);
                $table->unique(['brand_code', 'business_id']);
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            });
        }

        if (Schema::hasTable('creditors')) {
            Schema::table('creditors', function (Blueprint $table) {
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            });
        }

        if (Schema::hasTable('debtors')) {
            Schema::table('debtors', function (Blueprint $table) {
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            });
        }
    }
};
