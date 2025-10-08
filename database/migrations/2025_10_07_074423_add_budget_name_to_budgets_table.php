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
        Schema::table('budgets', function (Blueprint $table) {
            // Add the budget_name column
            $table->string('budget_name')->after('category')->default('Default');
            
            // Remove the old unique constraint
            $table->dropUnique(['user_id', 'category', 'year', 'month']);
            
            // Add new unique constraint that includes budget_name
            $table->unique(['user_id', 'category', 'budget_name', 'year', 'month'], 'budgets_user_category_name_year_month_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('budgets_user_category_name_year_month_unique');
            
            // Remove the budget_name column
            $table->dropColumn('budget_name');
            
            // Re-add the old unique constraint
            $table->unique(['user_id', 'category', 'year', 'month']);
        });
    }
};
