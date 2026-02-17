<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('price_annual')->default(0)->after('price'); // annual price in cents (per month)
            $table->string('audience')->nullable()->after('description'); // target audience label
            $table->boolean('is_popular')->default(false)->after('audience'); // highlight flag
            $table->integer('limit_storage')->default(2)->after('limit_stores'); // storage in GB
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['price_annual', 'audience', 'is_popular', 'limit_storage']);
        });
    }
};
