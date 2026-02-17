<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->safeDropUnique('categories', 'categories_category_code_unique');
        $this->safeAddUnique('categories', ['store_id', 'category_code'], 'categories_store_id_category_code_unique');

        // Expense categories usually don't have a unique name constraint, but checking just in case
        $this->safeDropUnique('expense_categories', 'expense_categories_category_name_unique');
        $this->safeAddUnique('expense_categories', ['store_id', 'category_name'], 'expense_categories_store_id_category_name_unique');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->safeDropUnique('categories', 'categories_store_id_category_code_unique');
        // Restore old unique if we knew it existed. Since we can't easily know if it existed before, 
        // we might leave it or attempt to restore it if needed. 
        // For now, let's just reverse the new one.
        // $this->safeAddUnique('categories', 'category_code', 'categories_category_code_unique');

        $this->safeDropUnique('expense_categories', 'expense_categories_store_id_category_name_unique');
    }

    protected function safeDropUnique($table, $indexName)
    {
        $exists = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        if (count($exists) > 0) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });
        }
    }

    protected function safeAddUnique($table, $columns, $indexName)
    {
        $exists = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        if (count($exists) === 0) {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->unique($columns, $indexName);
            });
        }
    }
};
