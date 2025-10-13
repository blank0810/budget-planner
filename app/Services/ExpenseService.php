<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ExpenseService
{
  /**
   * Get available years with expenses
   *
   * @param User $user
   * @return array
   */
  public function getAvailableYears(User $user)
  {
    return Cache::remember("user_{$user->id}_available_expense_years", now()->addDay(), function () use ($user) {
      return Expense::where('user_id', $user->id)
        ->selectRaw('YEAR(date) as year')
        ->groupBy('year')
        ->orderBy('year', 'desc')
        ->pluck('year')
        ->toArray();
    });
  }

  /**
   * Get filtered expenses with pagination
   *
   * @param array $filters
   * @return \Illuminate\Pagination\LengthAwarePaginator
   */
  public function getFilteredExpenses(array $filters = [])
  {
    $query = Expense::with(['budget'])
      ->where('user_id', $filters['user_id']);

    if (!empty($filters['category'])) {
      $query->where('category', $filters['category']);
    }

    if (!empty($filters['month'])) {
      $query->whereMonth('date', $filters['month']);
    }

    if (!empty($filters['year'])) {
      $query->whereYear('date', $filters['year']);
    }

    return $query->latest('date')
      ->paginate($filters['per_page'] ?? 15);
  }

  /**
   * Get monthly summary of expenses
   *
   * @param User $user
   * @param int|null $year
   * @param int|null $month
   * @param bool $includeComparison
   * @return array
   */
  public function getMonthlySummary(User $user, ?int $year = null, ?int $month = null, bool $includeComparison = true): array
  {
    $year = $year ?? now()->year;
    $month = $month ?? now()->month;
    $cacheKey = "user_{$user->id}_expense_summary_{$year}_{$month}";

    return Cache::remember($cacheKey, now()->addHour(), function () use ($user, $year, $month, $includeComparison) {
      // Base summary query
      $result = DB::table('expenses')
        ->select([
          DB::raw('COALESCE(SUM(amount), 0) as total_amount'),
          DB::raw('COUNT(*) as transaction_count'),
          DB::raw('COALESCE(AVG(amount), 0) as average_amount'),
        ])
        ->where('user_id', $user->id)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->first();

      // Get payment methods breakdown
      $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
      $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
      
      $paymentMethods = DB::table('expenses')
        ->select([
          'payment_method',
          DB::raw('SUM(amount) as total_amount'),
          DB::raw('COUNT(*) as transaction_count')
        ])
        ->where('user_id', $user->id)
        ->whereBetween('date', [$startDate, $endDate])
        ->whereNotNull('payment_method')
        ->groupBy('payment_method')
        ->orderBy('total_amount', 'desc')
        ->get()
        ->map(function ($item) {
          return [
            'payment_method' => $item->payment_method,
            'total_amount' => (float)$item->total_amount,
            'transaction_count' => (int)$item->transaction_count,
            'percentage' => 0 // Will be calculated below
          ];
        })
        ->toArray();

      // Calculate percentages
      $totalAmount = (float)($result->total_amount ?? 0);
      if ($totalAmount > 0) {
        foreach ($paymentMethods as &$method) {
          $method['percentage'] = round(($method['total_amount'] / $totalAmount) * 100, 1);
        }
      }

      $summary = [
        'total_amount' => $totalAmount,
        'transaction_count' => (int)($result->transaction_count ?? 0),
        'average_amount' => (float)($result->average_amount ?? 0),
        'payment_methods' => $paymentMethods,
        'month' => str_pad($month, 2, '0', STR_PAD_LEFT),
        'year' => $year,
        'month_name' => Carbon::createFromDate($year, $month, 1)->format('F')
      ];

      if ($includeComparison) {
        $prevMonth = Carbon::createFromDate($year, $month, 1)->subMonth();
        $prevResult = DB::table('expenses')
          ->select(DB::raw('COALESCE(SUM(amount), 0) as total_amount'))
          ->where('user_id', $user->id)
          ->whereYear('date', $prevMonth->year)
          ->whereMonth('date', $prevMonth->month)
          ->first();

        $prevTotal = (float)($prevResult->total_amount ?? 0);

        $summary['previous_period'] = [
          'total_amount' => $prevTotal,
          'change_percentage' => $prevTotal > 0
            ? (($summary['total_amount'] - $prevTotal) / $prevTotal) * 100
            : 0,
        ];
        $summary['previous_period']['is_positive_change'] = $summary['previous_period']['change_percentage'] <= 0;
      }

      return $summary;
    });
  }

  /**
   * Get payment method breakdown
   *
   * @param User $user
   * @param string $startDate
   * @param string $endDate
   * @return Collection
   */
  public function getPaymentMethodBreakdown(User $user, string $startDate, string $endDate): Collection
  {
    $cacheKey = "user_{$user->id}_payment_methods_{$startDate}_{$endDate}";

    return Cache::remember($cacheKey, now()->addHour(), function () use ($user, $startDate, $endDate) {
      $total = $user->expenses()
        ->whereBetween('date', [$startDate, $endDate])
        ->sum('amount');

      return $user->expenses()
        ->select([
          'payment_method',
          DB::raw('SUM(amount) as total_amount'),
          DB::raw('COUNT(*) as transaction_count')
        ])
        ->whereBetween('date', [$startDate, $endDate])
        ->groupBy('payment_method')
        ->orderBy('total_amount', 'desc')
        ->get()
        ->map(function ($item) use ($total) {
          $item->percentage = $total > 0 ? ($item->total_amount / $total) * 100 : 0;
          return $item;
        });
    });
  }

  /**
   * Get yearly summary of expenses
   *
   * @param User $user
   * @param int $year
   * @return array
   */
  public function getYearlySummary(User $user, int $year): array
  {
    $cacheKey = "user_{$user->id}_expense_yearly_summary_{$year}";

    return Cache::remember($cacheKey, now()->addDay(), function () use ($user, $year) {
      // Initialize default values
      $result = (object) [
        'transaction_count' => 0,
        'total_amount' => 0,
        'average_amount' => 0
      ];

      // Get total transactions and amount for the year
      $queryResult = Expense::where('user_id', $user->id)
        ->whereYear('date', $year)
        ->selectRaw('COUNT(*) as transaction_count')
        ->selectRaw('COALESCE(SUM(amount), 0) as total_amount')
        ->selectRaw('COALESCE(AVG(amount), 0) as average_amount')
        ->first();

      if ($queryResult) {
        $result = $queryResult;
      }

      // Get monthly average transactions
      $monthlyAverages = Expense::where('user_id', $user->id)
        ->whereYear('date', $year)
        ->selectRaw('MONTH(date) as month')
        ->selectRaw('COUNT(*) as count')
        ->groupBy('month')
        ->get();

      $monthlyTransactionCounts = $monthlyAverages->pluck('count', 'month')->toArray();
      $monthlyAverageTransactions = count($monthlyTransactionCounts) > 0
        ? round(array_sum($monthlyTransactionCounts) / count($monthlyTransactionCounts), 1)
        : 0;

      $totalAmount = (float) $result->total_amount;
      $transactionCount = (int) $result->transaction_count;

      return [
        'transaction_count' => $transactionCount,
        'total_amount' => $totalAmount, // Keep for backward compatibility
        'average_monthly' => $totalAmount / 12,
        'monthly_average_transactions' => $monthlyAverageTransactions,
        'year' => $year
      ];
    });
  }

  /**
   * Get monthly summaries of expenses
   *
   * @param User $user
   * @param int|null $year Optional: filter by year
   * @return Collection
   */
  public function getMonthlySummaries(User $user, ?int $year = null): Collection
  {
    $year = $year ?? now()->year;
    $cacheKey = "user_{$user->id}_expense_monthly_summaries_{$year}";

    return Cache::remember($cacheKey, now()->addDay(), function () use ($user, $year) {
      // Get all expenses for the year, grouped by month
      $expensesByMonth = $user->expenses()
        ->whereYear('date', $year)
        ->orderBy('date', 'desc')
        ->get()
        ->groupBy(function ($expense) {
          return (int) $expense->date->format('n'); // Group by month number (1-12)
        });

      // Get payment methods for each month
      $paymentMethodsByMonth = $user->expenses()
        ->select([
          DB::raw('MONTH(date) as month'),
          'payment_method',
          DB::raw('SUM(amount) as total_amount'),
          DB::raw('COUNT(*) as transaction_count')
        ])
        ->whereYear('date', $year)
        ->whereNotNull('payment_method')
        ->groupBy(DB::raw('MONTH(date)'), 'payment_method')
        ->orderBy(DB::raw('MONTH(date)'))
        ->orderBy('total_amount', 'desc')
        ->get()
        ->groupBy('month');
        
      // Get categories for each month
      $categoriesByMonth = $user->expenses()
        ->select([
          DB::raw('MONTH(date) as month'),
          'category',
          DB::raw('SUM(amount) as total_amount'),
          DB::raw('COUNT(*) as transaction_count')
        ])
        ->whereYear('date', $year)
        ->whereNotNull('category')
        ->groupBy(DB::raw('MONTH(date)'), 'category')
        ->orderBy(DB::raw('MONTH(date)'))
        ->orderBy('total_amount', 'desc')
        ->get()
        ->groupBy('month');
        
      // Get busiest day for each month
      $busiestDays = $user->expenses()
        ->select([
          DB::raw('MONTH(date) as month'),
          DB::raw('DAY(date) as day'),
          DB::raw('COUNT(*) as transaction_count'),
          DB::raw('SUM(amount) as total_amount'),
          DB::raw('DATE(date) as date')
        ])
        ->whereYear('date', $year)
        ->groupBy(DB::raw('MONTH(date)'), DB::raw('DAY(date)'), DB::raw('DATE(date)'))
        ->orderBy(DB::raw('MONTH(date)'))
        ->orderBy('transaction_count', 'desc')
        ->get()
        ->groupBy('month')
        ->map(function ($days) {
          return $days->first();
        });

      // Initialize collection for all 12 months
      $monthlyData = collect();

      for ($month = 1; $month <= 12; $month++) {
        $date = Carbon::createFromDate($year, $month, 1);
        $monthExpenses = $expensesByMonth->get($month, collect());
        $monthPaymentMethods = $paymentMethodsByMonth->get($month, collect());
        
        // Calculate payment method and category percentages
        $totalAmount = $monthExpenses->sum('amount');
        
        // Process payment methods
        $paymentMethods = $monthPaymentMethods->map(function ($method) use ($totalAmount) {
          $method->percentage = $totalAmount > 0 ? round(($method->total_amount / $totalAmount) * 100, 1) : 0;
          return [
            'payment_method' => $method->payment_method,
            'total_amount' => (float)$method->total_amount,
            'transaction_count' => (int)$method->transaction_count,
            'percentage' => $method->percentage
          ];
        })->values()->toArray();

        // Process categories
        $monthCategories = $categoriesByMonth->get($month, collect());
        $categoryBreakdown = $monthCategories->map(function ($category) use ($totalAmount) {
          $category->percentage = $totalAmount > 0 ? round(($category->total_amount / $totalAmount) * 100, 1) : 0;
          return [
            'category' => $category->category,
            'total_amount' => (float)$category->total_amount,
            'transaction_count' => (int)$category->transaction_count,
            'percentage' => $category->percentage
          ];
        })->values()->toArray();

        // Get busiest day for this month
        $busiestDay = $busiestDays->get($month);
        $busiestDayData = null;
        
        if ($busiestDay) {
            $busiestDayDate = Carbon::parse($busiestDay->date);
            $busiestDayData = [
                'date' => $busiestDayDate->format('Y-m-d'),
                'day_name' => $busiestDayDate->format('l'),
                'day_number' => $busiestDayDate->format('j'),
                'transaction_count' => $busiestDay->transaction_count,
                'total_amount' => (float)$busiestDay->total_amount
            ];
        }

        $monthlyData->push((object) [
          'year' => $year,
          'month' => $month,
          'month_number' => str_pad($month, 2, '0', STR_PAD_LEFT),
          'month_name' => $date->format('F'),
          'transaction_count' => $monthExpenses->count(),
          'total_amount' => $totalAmount,
          'payment_methods' => $paymentMethods,
          'category_breakdown' => $categoryBreakdown,
          'busiest_day' => $busiestDayData,
          'date' => $date->format('Y-m-01'),
          'expenses' => $monthExpenses
        ]);
      }

      return $monthlyData->sortBy('month');
    });
  }

  /**
   * Get expenses for a specific month
   *
   * @param User $user
   * @param int|null $year
   * @param int|null $month
   * @return Collection
   */
  public function getMonthlyExpenses(User $user, ?int $year = null, ?int $month = null)
  {
    $year = $year ?? now()->year;
    $month = $month ?? now()->month;

    return $user->expenses()
      ->with('budget')
      ->whereYear('date', $year)
      ->whereMonth('date', $month)
      ->orderBy('date', 'desc')
      ->get();
  }

  /*
     * Filter expenses by date range
     */
  public function filterByDateRange(User $user, string $startDate, string $endDate)
  {
    return $user->expenses()
      ->with('budget')
      ->whereBetween('date', [$startDate, $endDate])
      ->orderBy('date', 'desc')
      ->get();
  }

  /**
   * Get the list of valid expense categories
   */
  public function getExpenseCategories(): array
  {
    return [
      'Housing' => 'Housing',
      'Utilities' => 'Utilities',
      'Food' => 'Food',
      'Transportation' => 'Transportation',
      'Healthcare' => 'Healthcare',
      'Entertainment' => 'Entertainment',
      'Shopping' => 'Shopping',
      'Education' => 'Education',
      'Personal Care' => 'Personal Care',
      'Gifts & Donations' => 'Gifts & Donations',
      'Travel' => 'Travel',
      'Insurance' => 'Insurance',
      'Other' => 'Other',
    ];
  }

  /**
   * Get the list of available recurring intervals
   */
  public function getRecurringIntervals(): array
  {
    return [
      'daily' => 'Daily',
      'weekly' => 'Weekly',
      'monthly' => 'Monthly',
      'yearly' => 'Yearly',
    ];
  }

  /**
   * Get the list of available payment methods
   */
  public function getPaymentMethods(): array
  {
    return [
      'cash' => 'Cash',
      'credit_card' => 'Credit Card',
      'debit_card' => 'Debit Card',
      'bank_transfer' => 'Bank Transfer',
      'digital_wallet' => 'Digital Wallet',
      'other' => 'Other',
    ];
  }

  /**
   * Get category breakdown for expenses
   *
   * @param User $user
   * @param string $startDate Format: Y-m-d
   * @param string $endDate Format: Y-m-d
   * @return Collection
   */
  public function getCategoryBreakdown(User $user, string $startDate, string $endDate): Collection
  {
    $cacheKey = "user_{$user->id}_expense_categories_{$startDate}_{$endDate}";

    return Cache::remember($cacheKey, now()->addHour(), function () use ($user, $startDate, $endDate) {
      $total = $user->expenses()
        ->whereBetween('date', [$startDate, $endDate])
        ->sum('amount');

      return $user->expenses()
        ->select([
          'category',
          DB::raw('SUM(amount) as total'),
          DB::raw('COUNT(*) as count')
        ])
        ->whereBetween('date', [$startDate, $endDate])
        ->groupBy('category')
        ->orderBy('total', 'desc')
        ->get()
        ->map(function ($item) use ($total) {
          $item->percentage = $total > 0 ? ($item->total / $total) * 100 : 0;
          return $item;
        });
    });
  }

  /**
   * Get user's budgets
   */
  public function getUserBudgets(User $user)
  {
    return $user->budgets()
      ->orderBy('category')
      ->orderBy('budget_name')
      ->get();
  }

  /**
   * Create a new expense
   */
  public function createExpense(User $user, array $data, $receiptFile = null)
  {
    if ($receiptFile) {
      $data['receipt_path'] = $receiptFile->store('receipts', 'public');
    }

    return $user->expenses()->create($data);
  }

  /**
   * Get expense with relations
   */
  public function getExpenseWithRelations(Expense $expense)
  {
    return $expense->load('budget');
  }

  /**
   * Update an existing expense
   */
  public function updateExpense(Expense $expense, array $data, $receiptFile = null)
  {
    if ($receiptFile) {
      // Delete old receipt if exists
      if ($expense->receipt_path) {
        Storage::disk('public')->delete($expense->receipt_path);
      }
      $data['receipt_path'] = $receiptFile->store('receipts', 'public');
    }

    $expense->update($data);
    return $expense;
  }

  /**
   * Delete an expense
   */
  public function deleteExpense(Expense $expense)
  {
    // Delete receipt file if exists
    if ($expense->receipt_path) {
      Storage::disk('public')->delete($expense->receipt_path);
    }

    $expense->delete();
  }

  /**
   * Get recurring expense summary
   * Cached for 6 hours
   * Cache key: user_{user_id}_recurring_expense_summary
   */
  public function getRecurringExpenseSummary(User $user): array
  {
    $cacheKey = "user_{$user->id}_recurring_expense_summary";

    return Cache::remember($cacheKey, now()->addHours(6), function () use ($user) {
      $recurringExpenses = $user->expenses()
        ->where('is_recurring', true)
        ->where('is_paused', false)
        ->get();

      $monthlyTotal = 0;
      $annualTotal = 0;

      foreach ($recurringExpenses as $expense) {
        switch ($expense->recurring_interval) {
          case 'monthly':
            $monthlyTotal += $expense->amount;
            $annualTotal += $expense->amount * 12;
            break;
          case 'yearly':
            $monthlyTotal += $expense->amount / 12;
            $annualTotal += $expense->amount;
            break;
          case 'weekly':
            $monthlyTotal += $expense->amount * 4.33; // Average weeks in a month
            $annualTotal += $expense->amount * 52;
            break;
          case 'daily':
            $monthlyTotal += $expense->amount * 30.44; // Average days in a month
            $annualTotal += $expense->amount * 365;
            break;
        }
      }

      return [
        'count' => $recurringExpenses->count(),
        'monthly_total' => $monthlyTotal,
        'annual_total' => $annualTotal,
        'expenses' => $recurringExpenses,
      ];
    });
  }

  /**
   * Process recurring expenses
   * This should be called by a scheduled task
   */
  public function processRecurringExpenses(User $user = null)
  {
    $query = Expense::query()
      ->where('is_recurring', true)
      ->where('is_paused', false);

    if ($user) {
      $query->where('user_id', $user->id);
    }

    $recurringExpenses = $query->get();
    $createdExpenses = [];

    foreach ($recurringExpenses as $expense) {
      $shouldCreate = false;
      $now = now();
      $lastOccurrence = $expense->last_recurred_at ? Carbon::parse($expense->last_recurred_at) : null;

      switch ($expense->recurring_interval) {
        case 'daily':
          $shouldCreate = !$lastOccurrence || $lastOccurrence->addDay()->isPast();
          break;
        case 'weekly':
          $shouldCreate = !$lastOccurrence || $lastOccurrence->addWeek()->isPast();
          break;
        case 'monthly':
          $shouldCreate = !$lastOccurrence || $lastOccurrence->addMonth()->isPast();
          break;
        case 'yearly':
          $shouldCreate = !$lastOccurrence || $lastOccurrence->addYear()->isPast();
          break;
      }

      if ($shouldCreate) {
        $newExpense = $expense->replicate();
        $newExpense->is_recurring = false;
        $newExpense->recurring_interval = null;
        $newExpense->last_recurred_at = null;
        $newExpense->created_at = $now;
        $newExpense->updated_at = $now;
        $newExpense->date = $now->toDateString();
        $newExpense->save();

        $expense->last_recurred_at = $now;
        $expense->save();

        $createdExpenses[] = $newExpense;
      }
    }

    return $createdExpenses;
  }

  /**
   * Get upcoming recurring expenses
   */
  public function getUpcomingRecurringExpenses(User $user, int $days = 30)
  {
    $now = now();
    $endDate = $now->copy()->addDays($days);
    $upcomingExpenses = [];

    $recurringExpenses = $user->expenses()
      ->where('is_recurring', true)
      ->where('is_paused', false)
      ->get();

    foreach ($recurringExpenses as $expense) {
      $lastDate = $expense->last_recurred_at ? Carbon::parse($expense->last_recurred_at) : $now;
      $nextDate = $this->calculateNextOccurrence($lastDate, $expense->recurring_interval);

      while ($nextDate <= $endDate) {
        $upcomingExpenses[] = [
          'expense' => $expense,
          'next_occurrence' => $nextDate->format('Y-m-d'),
          'amount' => $expense->amount,
          'description' => $expense->description,
          'category' => $expense->category,
        ];

        $nextDate = $this->calculateNextOccurrence($nextDate, $expense->recurring_interval);
      }
    }

    // Sort by next occurrence date
    usort($upcomingExpenses, function ($a, $b) {
      return strtotime($a['next_occurrence']) - strtotime($b['next_occurrence']);
    });

    return $upcomingExpenses;
  }

  /**
   * Calculate next occurrence date based on interval
   */
  protected function calculateNextOccurrence(Carbon $date, string $interval): Carbon
  {
    return match ($interval) {
      'daily' => $date->copy()->addDay(),
      'weekly' => $date->copy()->addWeek(),
      'monthly' => $date->copy()->addMonth(),
      'yearly' => $date->copy()->addYear(),
      default => $date->copy()->addMonth(),
    };
  }
}
