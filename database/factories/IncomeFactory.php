<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Income::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'source' => $this->faker->company,
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'category' => $this->faker->randomElement(['Salary', 'Freelance', 'Investments', 'Gifts', 'Rental', 'Business', 'Other']),
            'notes' => $this->faker->sentence,
            'is_recurring' => $this->faker->boolean(30), // 30% chance of being recurring
            'recurring_interval' => $this->faker->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'next_recurring_date' => $this->faker->optional(0.7, null)->dateTimeBetween('now', '+1 year'), // 70% chance of having a next recurring date
        ];
    }

    /**
     * Configure the model factory to set recurring income.
     *
     * @return $this
     */
    public function recurring()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_recurring' => true,
                'is_paused' => false,
            ];
        });
    }
}
