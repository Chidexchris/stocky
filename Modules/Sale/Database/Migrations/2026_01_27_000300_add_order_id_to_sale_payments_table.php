<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdToSalePaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->string('order_id')->nullable()->unique()->after('reference');
        });
    }

    public function down()
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
            $table->dropColumn('order_id');
        });
    }
}
