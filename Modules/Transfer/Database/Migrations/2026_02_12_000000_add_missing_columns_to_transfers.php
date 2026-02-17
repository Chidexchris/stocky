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
        Schema::table('transfers', function (Blueprint $table) {
            $table->integer('item_count')->after('to_store_id')->default(0);
            $table->integer('total_quantity')->after('item_count')->default(0);
            $table->unsignedBigInteger('user_id')->nullable()->after('note');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('transfer_details', function (Blueprint $table) {
            $table->string('product_name')->after('product_id')->nullable();
            $table->string('product_code')->after('product_name')->nullable();
            $table->integer('unit_price')->after('quantity')->default(0);
            $table->integer('sub_total')->after('unit_price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['item_count', 'total_quantity', 'user_id']);
        });

        Schema::table('transfer_details', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'product_code', 'unit_price', 'sub_total']);
        });
    }
};
