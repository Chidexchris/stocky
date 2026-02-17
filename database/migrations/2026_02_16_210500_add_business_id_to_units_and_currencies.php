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
        // Add business_id to units
        if (Schema::hasTable('units') && !Schema::hasColumn('units', 'business_id')) {
            Schema::table('units', function (Blueprint $table) {
                $table->foreignId('business_id')->nullable()->after('id')
                      ->constrained('businesses')->onDelete('cascade');
            });
        }

        // Add business_id to currencies
        if (Schema::hasTable('currencies') && !Schema::hasColumn('currencies', 'business_id')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->foreignId('business_id')->nullable()->after('id')
                      ->constrained('businesses')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('currencies')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->dropForeign(['business_id']);
                $table->dropColumn('business_id');
            });
        }

        if (Schema::hasTable('units')) {
            Schema::table('units', function (Blueprint $table) {
                $table->dropForeign(['business_id']);
                $table->dropColumn('business_id');
            });
        }
    }
};
