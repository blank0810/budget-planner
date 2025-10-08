<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseBudgetAssociationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_associate_expense_with_budget()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a budget
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'category' => 'Groceries',
            'budget_name' => 'Weekly Groceries',
            'amount' => 500.00,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        // Create an expense associated with the budget
        $expenseData = [
            'description' => 'Weekly grocery shopping',
            'category' => 'Groceries',
            'amount' => 125.50,
            'date' => now()->format('Y-m-d'),
            'payment_method' => 'credit_card',
            'budget_id' => $budget->id,
        ];

        // Submit the form to create the expense
        $response = $this->post(route('expenses.store'), $expenseData);
        
        // Verify the expense was created and associated with the budget
        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'description' => 'Weekly grocery shopping',
            'category' => 'Groceries',
            'amount' => 125.50,
            'budget_id' => $budget->id,
        ]);
        
        // Verify the expense is shown in the budget's expenses
        $this->assertTrue($budget->expenses()->where('description', 'Weekly grocery shopping')->exists());
    }

    /** @test */
    public function user_can_update_expense_to_associate_with_budget()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a budget
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'category' => 'Entertainment',
            'budget_name' => 'Monthly Entertainment',
            'amount' => 200.00,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        // Create an expense without a budget
        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'category' => 'Entertainment',
            'amount' => 50.00,
            'date' => now()->format('Y-m-d'),
        ]);

        // Update the expense to associate it with the budget
        $response = $this->put(route('expenses.update', $expense), [
            'description' => $expense->description,
            'category' => $expense->category,
            'amount' => $expense->amount,
            'date' => $expense->date->format('Y-m-d'),
            'payment_method' => 'debit_card',
            'budget_id' => $budget->id,
        ]);

        // Verify the expense was updated with the budget
        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'budget_id' => $budget->id,
        ]);
    }

    /** @test */
    public function budget_is_shown_in_expense_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a budget
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'category' => 'Utilities',
            'budget_name' => 'Monthly Utilities',
            'amount' => 300.00,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        // Create an expense associated with the budget
        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'category' => 'Utilities',
            'budget_id' => $budget->id,
            'amount' => 75.00,
            'date' => now()->format('Y-m-d'),
        ]);

        // View the expenses index page
        $response = $this->get(route('expenses.index'));
        
        // Verify the budget name is shown in the expense row
        $response->assertSee($budget->budget_name);
    }

    /** @test */
    public function budget_is_shown_in_expense_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a budget
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'category' => 'Transportation',
            'budget_name' => 'Monthly Transportation',
            'amount' => 200.00,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        // Create an expense associated with the budget
        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'category' => 'Transportation',
            'budget_id' => $budget->id,
            'amount' => 45.00,
            'date' => now()->format('Y-m-d'),
        ]);

        // View the expense details page
        $response = $this->get(route('expenses.show', $expense));
        
        // Verify the budget name is shown in the details
        $response->assertSee($budget->budget_name);
    }
}
