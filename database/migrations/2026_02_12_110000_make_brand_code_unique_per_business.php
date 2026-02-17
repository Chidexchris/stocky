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
        Schema::table('brands', function (Blueprint $table) {
            // Drop the old global unique constraint
            $table->dropUnique(['brand_code']);

            // Add composite unique: brand_code + business_id
            $table->unique(['brand_code', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropUnique(['brand_code', 'business_id']);
            $table->unique('brand_code');
        });
    }
};
