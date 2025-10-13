<?php

namespace App\Services;

use App\Models\Income;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeService
{
  /**
   * Get monthly income summary for a user
   */
  /**
   * Get monthly income summary for a user
   * Cached for 1 hour
   * Cache key: user_{user_id}_income_summary_{year}_{month}
   */
  public function getMonthlySummary(User $user, int $year = null, int $month = null, bool $includeComparison = true): array
  {
    $year = $year ?? now()->year;
    $month = $month ?? now()->month;

    $cacheKey = "user_{$user->id}_income_summary_{$year}_{$month}";

    // Clear cache for testing
    Cache::forget($cacheKey);
    
    return Cache::remember($cacheKey, now()->addHour(), function () use ($user, $year, $month, $includeComparison) {
      // Get summary data
      $result = DB::table('incomes')
        ->select([
          DB::raw('COALESCE(SUM(amount), 0) as total_amount'),
          DB::raw('COUNT(*) as transaction_count'),
          DB::raw('COALESCE(AVG(amount), 0) as average_amount'),
        ])
        ->where('user_id', $user->id)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->first();

      // Get largest income for the month
      $largestIncome = DB::table('incomes')
        ->select(['id', 'amount', 'source', 'date'])
        ->where('user_id', $user->id)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->orderBy('amount', 'desc')
        ->first();

      // Debug output
      Log::info('Largest income query result:', [
        'year' => $year,
        'month' => $month,
        'result' => $largestIncome,
        'has_data' => !is_null($largestIncome)
      ]);

      $summary = [
        'total_amount' => (float)($result->total_amount ?? 0),
        'transaction_count' => (int)($result->transaction_count ?? 0),
        'average_amount' => (float)($result->average_amount ?? 0),
      ];

      // Only add largest_income if we have a result
      if ($largestIncome) {
        $summary['largest_income'] = [
          'id' => $largestIncome->id,
          'amount' => (float)$largestIncome->amount,
          'source' => $largestIncome->source,
          'date' => $largestIncome->date
        ];
      }

      if ($includeComparison) {
        // Get previous month data without recursion
        $prevMonth = Carbon::createFromDate($year, $month, 1)->subMonth();
        $prevResult = DB::table('incomes')
          ->select(DB::raw('COALESCE(SUM(amount), 0) as total_amount'))
          ->where('user_id', $user->id)
          ->whereYear('date', $prevMonth->year)
          ->whereMonth('date', $prevMonth->month)
          ->first();

        $prevTotal = (float)($prevResult->total_amount ?? 0);

        $summary['change_percentage'] = $prevTotal > 0
          ? (($summary['total_amount'] - $prevTotal) / $prevTotal) * 100
          : 0;

        $summary['is_positive_change'] = $summary['change_percentage'] >= 0;
      }

      // Debug the final summary
      Log::info('Monthly summary:', $summary);

      return $summary;
    });
  }

  /**
   * Get income breakdown by category for a specific period
   */
  /**
   * Get income breakdown by category for a specific period
   * Cached for 1 hour
   * Cache key: user_{user_id}_category_breakdown_{year}_{month}
   */
  public function getCategoryBreakdown(User $user, int $year = null, int $month = null): Collection
  {
    $year = $year ?? now()->year;
    $month = $month ?? now()->month;

    $cacheKey = "user_{$user->id}_category_breakdown_{$year}_{$month}";

    return Cache::remember($cacheKey, now()->addHour(), function () use ($user, $year, $month) {
      $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
      $endDate = $startDate->copy()->endOfMonth();

      return $user->incomes()
        ->select([
          'category',
          DB::raw('SUM(amount) as total_amount'),
          DB::raw('COUNT(*) as transaction_count')
        ])
        ->whereBetween('date', [$startDate, $endDate])
        ->groupBy('category')
        ->orderBy('total_amount', 'desc')
        ->get();
    });
  }

  /**
   * Get income trends over a period
   */
  /**
   * Get income trends over a period
   * Cached for 6 hours
   * Cache key: user_{user_id}_income_trends_{start_ym}_{end_ym}
   */
  public function getIncomeTrends(User $user, int $months = 6): array
  {
    $endDate = now()->endOfMonth();
    $startDate = now()->subMonths($months - 1)->startOfMonth();

    $cacheKey = "user_{$user->id}_income_trends_{$startDate->format('Ym')}_{$endDate->format('Ym')}";

    return Cache::remember($cacheKey, now()->addHours(6), function () use ($user, $startDate, $endDate, $months) {
      $results = $user->incomes()
        ->select([
          DB::raw('YEAR(date) as year'),
          DB::raw('MONTH(date) as month'),
          DB::raw('SUM(amount) as total_amount'),
          DB::raw('COUNT(*) as transaction_count')
        ])
        ->whereBetween('date', [$startDate, $endDate])
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

      // Fill in missing months with zero values
      $trends = [];
      $current = $startDate->copy();

      while ($current <= $endDate) {
        $key = $current->format('Y-m');
        $monthData = $results->firstWhere('month', $current->month);

        $trends[] = [
          'month' => $current->format('M Y'),
          'year' => $current->year,
          'month_number' => $current->month,
          'total_amount' => $monthData->total_amount ?? 0,
          'transaction_count' => $monthData->transaction_count ?? 0,
        ];

        $current->addMonth();
      }

      return $trends;
    });
  }

  /**
   * Get recurring income summary
   */
  /**
   * Get recurring income summary
   * Cached for 6 hours
   * Cache key: user_{user_id}_recurring_income_summary
   */
  public function getRecurringIncomeSummary(User $user): array
  {
    $cacheKey = "user_{$user->id}_recurring_income_summary";

    return Cache::remember($cacheKey, now()->addHours(6), function () use ($user) {
      $recurringIncomes = $user->incomes()
        ->where('is_recurring', true)
        ->where('is_paused', false)
        ->get();

      $monthlyTotal = 0;
      $annualTotal = 0;

      foreach ($recurringIncomes as $income) {
        switch ($income->recurring_interval) {
          case 'monthly':
            $monthlyTotal += $income->amount;
            $annualTotal += $income->amount * 12;
            break;
          case 'yearly':
            $monthlyTotal += $income->amount / 12;
            $annualTotal += $income->amount;
            break;
          case 'weekly':
            $monthlyTotal += $income->amount * 4.33; // Average weeks in a month
            $annualTotal += $income->amount * 52;
            break;
          case 'daily':
            $monthlyTotal += $income->amount * 30.44; // Average days in a month
            $annualTotal += $income->amount * 365;
            break;
        }
      }

      return [
        'count' => $recurringIncomes->count(),
        'monthly_total' => $monthlyTotal,
        'annual_total' => $annualTotal,
        'incomes' => $recurringIncomes,
      ];
    });
  }

  /**
   * Get all available income categories
   */
  public function getIncomeCategories(): array
  {
    return [
      'Salary',
      'Freelance',
      'Investments',
      'Gifts',
      'Rental',
      'Business',
      'Other',
    ];
  }

  /**
   * Get recurring intervals
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
   * Clear all cached income data for a user
   */

  /**
   * Get all incomes for a specific year, grouped by month
   * 
   * @param User $user The user to get incomes for
   * @param int $year The year to get incomes for
   * @return \Illuminate\Support\Collection Collection of incomes grouped by month (1-12)
   */
  public function getMonthlyIncomes(User $user, int $year): \Illuminate\Support\Collection
  {
    $cacheKey = "user_{$user->id}_monthly_incomes_{$year}";

    return Cache::remember($cacheKey, now()->addHour(), function () use ($user, $year) {
      return $user->incomes()
        ->whereYear('date', $year)
        ->orderBy('date', 'desc')
        ->get()
        ->groupBy(function ($income) {
          return (int)$income->date->format('m');
        });
    });
  }

  /**
   * Get all available years with income data
   * 
   * @param User $user The user to get years for
   * @return \Illuminate\Support\Collection
   */
  public function getAvailableYears(User $user): \Illuminate\Support\Collection
  {
    $cacheKey = "user_{$user->id}_available_income_years";

    return Cache::remember($cacheKey, now()->addHours(24), function () use ($user) {
      return $user->incomes()
        ->select(DB::raw('YEAR(date) as year'))
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');
    });
  }

  /**
   * Get yearly income summary
   * 
   * @param User $user The user to get the summary for
   * @param int $year The year to get the summary for
   * @return array Array containing yearly summary data
   */
  public function getYearlySummary(User $user, int $year = null): array
  {
    $year = $year ?? now()->year;
    $cacheKey = "user_{$user->id}_yearly_summary_{$year}";

    return Cache::remember($cacheKey, now()->addHours(6), function () use ($user, $year) {
      // Get monthly summaries for the year
      $monthlySummaries = [];
      $totalIncome = 0;
      $transactionCount = 0;
      $highestMonth = ['amount' => 0, 'month' => ''];

      for ($month = 1; $month <= 12; $month++) {
        $summary = $this->getMonthlySummary($user, $year, $month, false);
        $monthlySummaries[] = [
          'month' => Carbon::createFromDate($year, $month, 1)->format('F'),
          'amount' => $summary['total_amount']
        ];

        $totalIncome += $summary['total_amount'];
        $transactionCount += $summary['transaction_count'];

        if ($summary['total_amount'] > $highestMonth['amount']) {
          $highestMonth = [
            'amount' => $summary['total_amount'],
            'month' => Carbon::createFromDate($year, $month, 1)->format('F Y')
          ];
        }
      }

      return [
        'total_income' => $totalIncome,
        'average_monthly' => $totalIncome / 12,
        'transaction_count' => $transactionCount,
        'highest_month' => $highestMonth,
        'monthly_breakdown' => $monthlySummaries,
        'year' => $year
      ];
    });
  }

  /**
   * Clear all cached income data for a user
   * If year and month are provided, only clears cache for that specific period
   */
  public function clearCache(User $user, int $year = null, int $month = null): void
  {
    if ($year && $month) {
      // Clear specific month cache
      $keys = [
        "user_{$user->id}_income_summary_{$year}_{$month}",
        "user_{$user->id}_category_breakdown_{$year}_{$month}",
      ];

      // Also clear trends and other caches since they might be affected
      $startDate = now()->subMonths(5)->startOfMonth();
      $endDate = now()->endOfMonth();
      $keys = array_merge($keys, [
        "user_{$user->id}_income_trends_{$startDate->format('Ym')}_{$endDate->format('Ym')}",
        "user_{$user->id}_recurring_income_summary",
        "user_{$user->id}_available_income_years"
      ]);

      foreach ($keys as $key) {
        Cache::forget($key);
      }
    } else {
      // Clear all cached data for the user using tags if supported
      if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
        Cache::tags(["user_{$user->id}"])->flush();
      } else {
        // Fallback to clearing all cache if tags are not supported
        Cache::flush();
      }
    }
  }

  // Add these methods to IncomeService.php

  /**
   * Get filtered and sorted transactions
   */
  public function getFilteredTransactions(User $user, array $filters = [], array $sort = []): \Illuminate\Pagination\LengthAwarePaginator
  {
    $query = $user->incomes()->with('category');

    // Apply filters
    if (!empty($filters['year'])) {
      $query->whereYear('date', $filters['year']);
    }

    if (!empty($filters['month'])) {
      $query->whereMonth('date', $filters['month']);
    }

    if (!empty($filters['category'])) {
      $query->where('category_id', $filters['category']);
    }

    // Apply sorting
    $sortField = $sort['field'] ?? 'date';
    $sortDirection = $sort['direction'] ?? 'desc';
    $query->orderBy($sortField, $sortDirection);

    return $query->paginate(15);
  }

  /**
   * Export transactions to CSV
   */
  public function exportToCsv(User $user, array $filters = []): \Illuminate\Support\Collection
  {
    $query = $user->incomes();

    // Apply the same filters as getFilteredTransactions
    if (!empty($filters['year'])) {
      $query->whereYear('date', $filters['year']);
    }

    if (!empty($filters['month'])) {
      $query->whereMonth('date', $filters['month']);
    }

    return $query->get();
  }
}
