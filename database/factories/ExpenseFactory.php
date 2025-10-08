<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-1 year', 'now');
        
        return [
            'user_id' => \App\Models\User::factory(),
            'description' => $this->faker->sentence(4),
            'category' => $this->faker->randomElement(['Groceries', 'Entertainment', 'Utilities', 'Transportation', 'Dining']),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'date' => $date->format('Y-m-d'),
            'notes' => $this->faker->boolean(30) ? $this->faker->sentence : null,
            'is_recurring' => $this->faker->boolean(20),
            'recurring_interval' => $this->faker->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'next_recurring_date' => $this->faker->boolean(20) ? $this->faker->dateTimeBetween('+1 day', '+1 year')->format('Y-m-d') : null,
            'is_paused' => $this->faker->boolean(10),
            'payment_method' => $this->faker->randomElement(['cash', 'credit_card', 'debit_card', 'bank_transfer', 'digital_wallet']),
            'receipt_path' => $this->faker->boolean(10) ? 'receipts/' . $this->faker->uuid . '.pdf' : null,
            'budget_id' => null,
        ];
    }
}
