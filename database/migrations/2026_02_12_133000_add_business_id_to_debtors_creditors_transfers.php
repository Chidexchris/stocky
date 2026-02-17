<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['debtors', 'creditors', 'transfers'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'business_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreignId('business_id')->nullable()->after('id')
                      ->constrained('businesses')->nullOnDelete();
                });
            }
        }

        // Backfill existing records using the logged-in user's business or store relationship
        // For transfers, derive business_id from the from_store
        if (Schema::hasTable('transfers') && Schema::hasColumn('transfers', 'business_id')) {
            \DB::statement("
                UPDATE transfers t
                JOIN stores s ON s.id = t.from_store_id
                SET t.business_id = s.business_id
                WHERE t.business_id IS NULL
            ");
        }
    }

    public function down(): void
    {
        $tables = ['debtors', 'creditors', 'transfers'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'business_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['business_id']);
                    $t->dropColumn('business_id');
                });
            }
        }
    }
};
