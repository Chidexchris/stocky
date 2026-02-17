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
        Schema::table('login_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('business_id')->nullable()->after('id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        // Backfill business_id based on user
        \DB::table('login_logs')
            ->join('users', 'login_logs.user_id', '=', 'users.id')
            ->update(['login_logs.business_id' => \DB::raw('users.business_id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });
    }
};
