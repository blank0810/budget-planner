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
        Schema::table('expenses', function (Blueprint $table) {
            // Add budget_id as a nullable foreign key
            $table->foreignId('budget_id')
                ->nullable()
                ->after('category')
                ->constrained('budgets')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop the foreign key constraint and the column
            $table->dropForeign(['budget_id']);
            $table->dropColumn('budget_id');
        });
    }
};
