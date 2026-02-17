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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('from_store_id');
            $table->unsignedBigInteger('to_store_id');
            $table->string('status')->default('Pending');
            $table->text('note')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('from_store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('to_store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
