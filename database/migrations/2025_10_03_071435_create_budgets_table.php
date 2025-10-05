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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category'); // Expense category this budget applies to
            $table->decimal('amount', 10, 2); // Budget amount for the period
            $table->integer('year'); // Budget year
            $table->integer('month'); // Budget month (1-12)
            $table->text('notes')->nullable(); // Optional notes
            $table->boolean('is_active')->default(true); // Whether this budget is currently active
            $table->timestamps();

            // Add indexes for better query performance
            $table->index(['user_id', 'year', 'month']);
            $table->index(['user_id', 'category']);
            $table->unique(['user_id', 'category', 'year', 'month']); // Prevent duplicate budgets for same category/month
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
