<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdToAdjustmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adjustments', function (Blueprint $table) {
            $table->string('order_id')->nullable()->unique()->after('reference');
        });
    }

    public function down()
    {
        Schema::table('adjustments', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
            $table->dropColumn('order_id');
        });
    }
}
