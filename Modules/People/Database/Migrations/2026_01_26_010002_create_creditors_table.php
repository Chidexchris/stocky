<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditorsTable extends Migration
{
    /** Create creditors table to track supplier payables */
    public function up()
    {
        Schema::create('creditors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            /** amount_owed stored in minor units (cents) */
            $table->bigInteger('amount_owed')->default(0);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('creditors');
    }
}
