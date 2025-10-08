<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
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
        return [
            'user_id' => \App\Models\User::factory(),
            'category' => $this->faker->randomElement(['Groceries', 'Entertainment', 'Utilities', 'Transportation', 'Dining']),
            'budget_name' => $this->faker->words(2, true) . ' Budget',
            'amount' => $this->faker->randomFloat(2, 100, 2000),
            'month' => $this->faker->numberBetween(1, 12),
            'year' => $this->faker->numberBetween(2020, 2030),
            'notes' => $this->faker->boolean(30) ? $this->faker->sentence : null,
            'is_active' => true,
        ];
    }
}
