<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
  /**
   * Display a listing of the expenses.
   */
  public function index()
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $expenses = $user->expenses()
      ->with('budget')
      ->latest('date')
      ->paginate(10);

    return view('expenses.index', compact('expenses'));
  }

  /**
   * Show the form for creating a new expense.
   */
  public function create()
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    
    return view('expenses.create', [
      'expense' => new Expense(),
      'categories' => $this->getExpenseCategories(),
      'recurringIntervals' => $this->getRecurringIntervals(),
      'paymentMethods' => $this->getPaymentMethods(),
      'budgets' => $user->budgets()->orderBy('category')->orderBy('budget_name')->get(),
    ]);
  }

  /**
   * Store a newly created expense in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'description' => 'required|string|max:255',
      'category' => 'required|string|max:255',
      'budget_id' => 'nullable|exists:budgets,id,user_id,' . Auth::id(),
      'amount' => 'required|numeric|min:0.01|max:10000000',
      'date' => 'required|date',
      'notes' => 'nullable|string|max:1000',
      'is_recurring' => 'boolean',
      'recurring_interval' => [
        Rule::requiredIf(fn() => $request->boolean('is_recurring')),
        'nullable',
        Rule::in(array_keys($this->getRecurringIntervals())),
      ],
      'payment_method' => 'required|string|max:255',
      'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
    ]);

    try {
      // Handle receipt upload
      if ($request->hasFile('receipt')) {
        $receiptPath = $request->file('receipt')->store('receipts', 'public');
        $validated['receipt_path'] = $receiptPath;
      }

      /** @var \App\Models\User $user */
      $user = Auth::user();
      $expense = $user->expenses()->create($validated);

      return redirect()
        ->route('expenses.index')
        ->with('success', 'Expense added successfully!');
    } catch (\Exception $e) {
      Log::error('Error creating expense: ' . $e->getMessage());

      return back()
        ->withInput()
        ->with('error', 'Failed to add expense. Please try again.');
    }
  }

  /**
   * Display the specified expense.
   */
  public function show(Expense $expense)
  {
    $this->authorize('view', $expense);
    
    // Eager load the budget relationship
    $expense->load('budget');

    return view('expenses.show', compact('expense'));
  }

  /**
   * Show the form for editing the specified expense.
   */
  public function edit(Expense $expense)
  {
    $this->authorize('update', $expense);
    
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $budgets = $user->budgets()
        ->orderBy('category')
        ->orderBy('budget_name')
        ->get();
        
    // Eager load the budget relationship
    $expense->load('budget');

    return view('expenses.edit', [
      'expense' => $expense,
      'categories' => $this->getExpenseCategories(),
      'recurringIntervals' => $this->getRecurringIntervals(),
      'paymentMethods' => $this->getPaymentMethods(),
      'budgets' => $budgets,
    ]);
  }

  /**
   * Update the specified expense in storage.
   */
  public function update(Request $request, Expense $expense)
  {
    $this->authorize('update', $expense);

    $validated = $request->validate([
      'description' => 'required|string|max:255',
      'category' => 'required|string|max:255',
      'budget_id' => 'nullable|exists:budgets,id,user_id,' . Auth::id(),
      'amount' => 'required|numeric|min:0.01|max:10000000',
      'date' => 'required|date',
      'notes' => 'nullable|string|max:1000',
      'is_recurring' => 'boolean',
      'recurring_interval' => [
        Rule::requiredIf(fn() => $request->boolean('is_recurring')),
        'nullable',
        Rule::in(array_keys($this->getRecurringIntervals())),
      ],
      'payment_method' => 'required|string|max:255',
      'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
    ]);

    try {
      // Handle receipt upload
      if ($request->hasFile('receipt')) {
        // Delete old receipt if exists
        if ($expense->receipt_path) {
          Storage::disk('public')->delete($expense->receipt_path);
        }

        $receiptPath = $request->file('receipt')->store('receipts', 'public');
        $validated['receipt_path'] = $receiptPath;
      }

      $expense->update($validated);

      return redirect()
        ->route('expenses.index')
        ->with('success', 'Expense updated successfully!');
    } catch (\Exception $e) {
      Log::error('Error updating expense: ' . $e->getMessage());

      return back()
        ->withInput()
        ->with('error', 'Failed to update expense. Please try again.');
    }
  }

  /**
   * Remove the specified expense from storage.
   */
  public function destroy(Expense $expense)
  {
    $this->authorize('delete', $expense);

    try {
      // Delete receipt file if exists
      if ($expense->receipt_path) {
        Storage::disk('public')->delete($expense->receipt_path);
      }

      $expense->delete();

      return redirect()
        ->route('expenses.index')
        ->with('success', 'Expense deleted successfully!');
    } catch (\Exception $e) {
      Log::error('Error deleting expense: ' . $e->getMessage());

      return back()
        ->with('error', 'Failed to delete expense. Please try again.');
    }
  }

  /**
   * Get the list of expense categories.
   */
  protected function getExpenseCategories(): array
  {
    return [
      'Food & Dining',
      'Transportation',
      'Utilities',
      'Shopping',
      'Entertainment',
      'Healthcare',
      'Education',
      'Travel',
      'Insurance',
      'Subscriptions',
      'Other',
    ];
  }

  /**
   * Get the list of recurring intervals.
   */
  protected function getRecurringIntervals(): array
  {
    return [
      'daily' => 'Daily',
      'weekly' => 'Weekly',
      'monthly' => 'Monthly',
      'yearly' => 'Yearly',
    ];
  }

  /**
   * Get the list of payment methods.
   */
  protected function getPaymentMethods(): array
  {
    return [
      'cash' => 'Cash',
      'credit_card' => 'Credit Card',
      'debit_card' => 'Debit Card',
      'bank_transfer' => 'Bank Transfer',
      'digital_wallet' => 'Digital Wallet',
      'check' => 'Check',
      'other' => 'Other',
    ];
  }
}
