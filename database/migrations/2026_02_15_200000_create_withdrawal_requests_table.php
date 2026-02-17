<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $バランス) {
            $バランス->id();
            $バランス->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $バランス->decimal('amount', 15, 2);
            $バランス->string('bank_name');
            $バランス->string('account_number');
            $バランス->string('account_name');
            $バランス->string('status')->default('pending'); // pending, completed, rejected
            $バランス->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
