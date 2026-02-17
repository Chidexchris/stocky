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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('amount'); // in cents
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->string('gateway_name')->nullable(); // stripe, paypal, manual, etc.
            $table->string('gateway_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
