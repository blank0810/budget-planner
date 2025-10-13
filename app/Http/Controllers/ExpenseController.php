<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
  protected $expenseService;

  public function __construct(ExpenseService $expenseService)
  {
    $this->expenseService = $expenseService;
  }

  /**
   * Display monthly summaries of expenses
   */
  public function monthly(Request $request)
  {
    $user = $request->user();
    $selectedYear = $request->input('year', date('Y'));

    $monthlySummaries = $this->expenseService->getMonthlySummaries($user, $selectedYear);
    $availableYears = $this->expenseService->getAvailableYears($user);

    // Convert collection of objects to array of arrays for the view
    $monthlySummariesArray = $monthlySummaries->map(function ($item) {
      return (array) $item;
    })->toArray();

    return view('expenses.monthly', [
      'monthlySummaries' => $monthlySummariesArray,
      'selectedYear' => $selectedYear,
      'availableYears' => $availableYears,
    ]);
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = $request->user();
    $selectedMonth = $request->input('month', date('m'));
    $selectedYear = $request->input('year', date('Y'));
    $selectedCategory = $request->input('category');
    $viewMode = $request->input('view', 'list');

    // Get available years for the filter dropdown
    $availableYears = $this->expenseService->getAvailableYears($user);

    // Get categories for filter dropdown
    $categories = $this->expenseService->getExpenseCategories();

    // Get monthly summaries for the selected year
    $monthlySummaries = $this->expenseService->getMonthlySummaries($user, $selectedYear);

    // Convert to array and format for the view
    $monthlySummaries = $monthlySummaries->map(function ($item) {
      return [
        'month' => $item->month_name . ' ' . $item->year,
        'month_name' => $item->month_name,
        'month_number' => $item->month,
        'year' => $item->year,
        'total_amount' => $item->total_amount,
        'transaction_count' => $item->transaction_count,
        'payment_methods' => $item->payment_methods ?? [],
        'category_breakdown' => $item->category_breakdown ?? [],
        'busiest_day' => $item->busiest_day ?? null,
        'expenses' => $item->expenses->map(function ($expense) {
          return [
            'id' => $expense->id,
            'amount' => $expense->amount,
            'description' => $expense->description,
            'date' => $expense->date->toDateString(),
            'category' => $expense->category ? [
              'name' => $expense->category
            ] : null
          ];
        })->toArray()
      ];
    })->values()->toArray();

    // dd($monthlySummaries);

    // Get yearly summary for the dashboard
    $yearlySummary = $this->expenseService->getYearlySummary($user, $selectedYear);

    // dd($yearlySummary);

    // Get previous year's summary for comparison
    $previousYear = $selectedYear - 1;
    $previousYearSummary = $this->expenseService->getYearlySummary($user, $previousYear);

    // Ensure all required keys exist in the yearly summary
    $yearlySummary = array_merge([
      'transaction_count' => 0,
      'monthly_average_transactions' => 0,
      'year' => $selectedYear
    ], (array) $yearlySummary);

    // Ensure previous year summary has required keys
    $previousYearSummary = array_merge([
      'transaction_count' => 0,
      'monthly_average_transactions' => 0,
      'year' => $previousYear
    ], (array) $previousYearSummary);

    // dd($monthlySummaries);

    // If in monthly view, return early with just the monthly summaries
    if ($viewMode === 'monthly') {
      return view('expenses.index', [
        'viewMode' => 'monthly',
        'monthlySummaries' => $monthlySummaries,
        'selectedYear' => $selectedYear,
        'availableYears' => $availableYears,
        'categories' => $categories,
        'yearlySummary' => $yearlySummary,
        'months' => [
          '01' => 'January',
          '02' => 'February',
          '03' => 'March',
          '04' => 'April',
          '05' => 'May',
          '06' => 'June',
          '07' => 'July',
          '08' => 'August',
          '09' => 'September',
          '10' => 'October',
          '11' => 'November',
          '12' => 'December'
        ]
      ]);
    }

    // For list view, get the detailed data
    // Get filtered expenses
    $expenses = $this->expenseService->getFilteredExpenses([
      'user_id' => $user->id,
      'month' => $selectedMonth,
      'year' => $selectedYear,
      'category' => $selectedCategory,
      'per_page' => 15
    ]);

    // Get summary data for the selected month
    $summary = $this->expenseService->getMonthlySummary(
      $user,
      $selectedYear,
      $selectedMonth
    );

    // Get category breakdown for the selected month
    $categoryBreakdown = $this->expenseService->getCategoryBreakdown(
      $user,
      "{$selectedYear}-{$selectedMonth}-01",
      now()->endOfMonth()->format('Y-m-d')
    );

    // Get payment method breakdown for the selected month
    $paymentMethodBreakdown = $this->expenseService->getPaymentMethodBreakdown(
      $user,
      "{$selectedYear}-{$selectedMonth}-01",
      now()->endOfMonth()->format('Y-m-d')
    );

    return view('expenses.index', [
      'viewMode' => 'list',
      'expenses' => $expenses,
      'summary' => $summary,
      'availableYears' => $availableYears,
      'categories' => $categories,
      'selectedYear' => $selectedYear,
      'selectedMonth' => $selectedMonth,
      'selectedCategory' => $selectedCategory,
      'categoryBreakdown' => $categoryBreakdown,
      'paymentMethodBreakdown' => $paymentMethodBreakdown,
      'monthlySummaries' => $monthlySummaries,
      'yearlySummary' => $yearlySummary,
      'previousYearSummary' => $previousYearSummary,
      'months' => [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
      ]
    ]);
  }

  /**
   * Show the form for creating a new expense.
   */
  public function create()
  {
      $categories = $this->expenseService->getExpenseCategories();
      $paymentMethods = $this->expenseService->getPaymentMethods();
      $recurringIntervals = $this->expenseService->getRecurringIntervals();
      $budgets = $this->expenseService->getUserBudgets(Auth::user());

      return view('expenses.create', [
          'expense' => new Expense(),
          'categories' => $categories,
          'paymentMethods' => $paymentMethods,
          'recurringIntervals' => $recurringIntervals,
          'budgets' => $budgets
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
        Rule::in(array_keys($this->expenseService->getRecurringIntervals())),
      ],
      'payment_method' => 'required|string|max:255',
      'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
    ]);

    try {
      $expense = $this->expenseService->createExpense(
        Auth::user(),
        $validated,
        $request->file('receipt')
      );

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

    $expense = $this->expenseService->getExpenseWithRelations($expense);

    return view('expenses.show', compact('expense'));
  }

  /**
   * Show the form for editing the specified expense.
   */
  public function edit(Expense $expense)
  {
    $this->authorize('update', $expense);

    $expense = $this->expenseService->getExpenseWithRelations($expense);

    return view('expenses.edit', [
      'expense' => $expense,
      'categories' => $this->expenseService->getExpenseCategories(),
      'recurringIntervals' => $this->expenseService->getRecurringIntervals(),
      'paymentMethods' => $this->expenseService->getPaymentMethods(),
      'budgets' => $this->expenseService->getUserBudgets(Auth::user()),
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
        Rule::in(array_keys($this->expenseService->getRecurringIntervals())),
      ],
      'payment_method' => 'required|string|max:255',
      'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
    ]);

    try {
      $this->expenseService->updateExpense(
        $expense,
        $validated,
        $request->file('receipt')
      );

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
      $this->expenseService->deleteExpense($expense);

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
   * Get monthly expenses summary
   */
  public function getMonthlySummary(int $year = null, int $month = null)
  {
    return $this->expenseService->getMonthlySummary(
      Auth::user(),
      $year,
      $month
    );
  }

  /**
   * Get expenses by date range
   */
  public function getExpensesByDateRange(Request $request)
  {
    $validated = $request->validate([
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    return $this->expenseService->filterByDateRange(
      Auth::user(),
      $validated['start_date'],
      $validated['end_date']
    );
  }
}
