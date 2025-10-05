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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('source');
            $table->string('category');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_interval')->nullable(); // daily, weekly, monthly, yearly
            $table->date('next_recurring_date')->nullable();
            $table->timestamps();
            
            // Add index for better query performance
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
