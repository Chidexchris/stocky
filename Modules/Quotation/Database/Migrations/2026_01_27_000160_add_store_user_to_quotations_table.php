<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreUserToQuotationsTable extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('reference');
            $table->unsignedBigInteger('user_id')->nullable()->after('store_id');
            $table->foreign('store_id')->references('id')->on('stores')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('store_id');
            $table->dropColumn('user_id');
        });
    }
}
