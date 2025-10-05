<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Show the reports dashboard.
     */
    public function index()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        return view('reports.index', [
            'years' => $this->getAvailableYears(),
            'months' => $this->getMonths(),
            'selectedYear' => $currentYear,
            'selectedMonth' => $currentMonth,
            'reportTypes' => $this->getReportTypes(),
        ]);
    }

    /**
     * Generate the requested report.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:monthly_summary,expense_by_category,income_vs_expense,budget_vs_actual',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'month' => 'nullable|integer|min:1|max:12',
            'export_format' => 'nullable|in:pdf,csv,excel',
        ]);

        $reportData = $this->generateReportData(
            $validated['report_type'],
            $validated['year'],
            $validated['month'] ?? null
        );

        if (isset($validated['export_format'])) {
            return $this->exportReport($reportData, $validated['export_format']);
        }

        return view('reports.show', [
            'reportData' => $reportData,
            'reportType' => $validated['report_type'],
            'year' => $validated['year'],
            'month' => $validated['month'] ?? null,
            'reportTypes' => $this->getReportTypes(),
            'months' => $this->getMonths(),
        ]);
    }

    /**
     * Generate report data based on type and date range.
     */
    protected function generateReportData(string $type, int $year, ?int $month = null): array
    {
        $userId = Auth::id();
        $startDate = $month 
            ? Carbon::create($year, $month, 1)->startOfMonth()
            : Carbon::create($year, 1, 1)->startOfYear();
            
        $endDate = $month 
            ? $startDate->copy()->endOfMonth()
            : $startDate->copy()->endOfYear();

        $reportData = [
            'title' => $this->getReportTitle($type, $year, $month),
            'period' => $month 
                ? $startDate->format('F Y') 
                : $startDate->format('Y'),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        switch ($type) {
            case 'monthly_summary':
                return array_merge($reportData, $this->generateMonthlySummary($startDate, $endDate, $userId));
                
            case 'expense_by_category':
                return array_merge($reportData, $this->generateExpenseByCategory($startDate, $endDate, $userId));
                
            case 'income_vs_expense':
                return array_merge($reportData, $this->generateIncomeVsExpense($startDate, $endDate, $userId));
                
            case 'budget_vs_actual':
                return array_merge($reportData, $this->generateBudgetVsActual($startDate, $endDate, $userId));
                
            default:
                throw new \InvalidArgumentException("Invalid report type: {$type}");
        }
    }

    /**
     * Generate monthly summary report data.
     */
    protected function generateMonthlySummary($startDate, $endDate, $userId): array
    {
        $incomes = Income::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
            
        $expenses = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
            
        $savings = $incomes - $expenses;
        $savingsRate = $incomes > 0 ? ($savings / $incomes) * 100 : 0;
        
        // Get top 5 expense categories
        $topCategories = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'category')
            ->toArray();
            
        return [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'savings' => $savings,
            'savings_rate' => $savingsRate,
            'top_categories' => $topCategories,
        ];
    }

    /**
     * Generate expense by category report data.
     */
    protected function generateExpenseByCategory($startDate, $endDate, $userId): array
    {
        $expenses = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
            
        $totalExpenses = $expenses->sum('total');
        
        // Add percentage for each category
        $expenses->each(function ($item) use ($totalExpenses) {
            $item->percentage = $totalExpenses > 0 
                ? round(($item->total / $totalExpenses) * 100, 2) 
                : 0;
        });
        
        return [
            'expenses' => $expenses,
            'total_expenses' => $totalExpenses,
        ];
    }

    /**
     * Generate income vs expense report data.
     */
    protected function generateIncomeVsExpense($startDate, $endDate, $userId): array
    {
        $isMonthly = $startDate->diffInMonths($endDate) <= 12;
        
        if ($isMonthly) {
            // Group by month if within a year
            $incomes = Income::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function ($item) {
                    $date = Carbon::create($item->year, $item->month, 1);
                    return [$date->format('M Y') => $item->total];
                });
                
            $expenses = Expense::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function ($item) {
                    $date = Carbon::create($item->year, $item->month, 1);
                    return [$date->format('M Y') => $item->total];
                });
        } else {
            // Group by year if more than a year
            $incomes = Income::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('YEAR(date) as year, SUM(amount) as total')
                ->groupBy('year')
                ->orderBy('year')
                ->get()
                ->pluck('total', 'year');
                
            $expenses = Expense::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('YEAR(date) as year, SUM(amount) as total')
                ->groupBy('year')
                ->orderBy('year')
                ->get()
                ->pluck('total', 'year');
        }
        
        return [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'is_monthly' => $isMonthly,
        ];
    }

    /**
     * Generate budget vs actual report data.
     */
    protected function generateBudgetVsActual($startDate, $endDate, $userId): array
    {
        // Get budgets for the period
        $budgets = Budget::where('user_id', $userId)
            ->where(function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    // For monthly reports
                    if ($startDate->isSameMonth($endDate)) {
                        $q->where('year', $startDate->year)
                          ->where('month', $startDate->month);
                    } 
                    // For yearly reports
                    else if ($startDate->isSameYear($endDate)) {
                        $q->where('year', $startDate->year);
                    }
                    // For custom date ranges, get all budgets that overlap
                    else {
                        $q->where(function($q2) use ($startDate, $endDate) {
                            $q2->where('year', '>=', $startDate->year)
                               ->where('year', '<=', $endDate->year);
                        });
                    }
                });
            })
            ->with(['expenses' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])
            ->get();
            
        // Process budget data
        $budgetData = [];
        $totalBudgeted = 0;
        $totalSpent = 0;
        
        foreach ($budgets as $budget) {
            $spent = $budget->expenses->sum('amount');
            $remaining = max(0, $budget->amount - $spent);
            $utilization = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
            
            $budgetData[] = [
                'category' => $budget->category,
                'budgeted' => $budget->amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'utilization' => $utilization,
                'is_over_budget' => $spent > $budget->amount,
            ];
            
            $totalBudgeted += $budget->amount;
            $totalSpent += $spent;
        }
        
        $totalRemaining = max(0, $totalBudgeted - $totalSpent);
        $totalUtilization = $totalBudgeted > 0 ? ($totalSpent / $totalBudgeted) * 100 : 0;
        
        return [
            'budgets' => $budgetData,
            'total_budgeted' => $totalBudgeted,
            'total_spent' => $totalSpent,
            'total_remaining' => $totalRemaining,
            'total_utilization' => $totalUtilization,
            'is_overall_over_budget' => $totalSpent > $totalBudgeted,
        ];
    }

    /**
     * Export report in the specified format.
     */
    protected function exportReport(array $reportData, string $format)
    {
        // TODO: Implement export functionality for PDF, CSV, and Excel
        return back()->with('info', 'Export functionality coming soon!');
    }

    /**
     * Get the title for the report.
     */
    protected function getReportTitle(string $type, int $year, ?int $month = null): string
    {
        $titles = [
            'monthly_summary' => 'Monthly Summary',
            'expense_by_category' => 'Expenses by Category',
            'income_vs_expense' => 'Income vs Expenses',
            'budget_vs_actual' => 'Budget vs Actual',
        ];
        
        $title = $titles[$type] ?? 'Report';
        
        if ($month) {
            $date = Carbon::create($year, $month, 1);
            return "{$title} - {$date->format('F Y')}";
        }
        
        return "{$title} - {$year}";
    }

    /**
     * Get available report types.
     */
    protected function getReportTypes(): array
    {
        return [
            'monthly_summary' => 'Monthly Summary',
            'expense_by_category' => 'Expenses by Category',
            'income_vs_expense' => 'Income vs Expenses',
            'budget_vs_actual' => 'Budget vs Actual',
        ];
    }

    /**
     * Get available years with data.
     */
    protected function getAvailableYears(): array
    {
        $currentYear = date('Y');
        $years = [];
        
        // Get the earliest year from incomes and expenses
        $minYear = min(
            (int)Income::where('user_id', Auth::id())->min('date'),
            (int)Expense::where('user_id', Auth::id())->min('date'),
            $currentYear - 1 // Default to previous year if no data
        );
        
        // Generate array of years from minYear to current year + 1
        for ($year = $minYear; $year <= $currentYear + 1; $year++) {
            $years[$year] = $year;
        }
        
        return $years;
    }

    /**
     * Get months array for select dropdown.
     */
    protected function getMonths(): array
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }
}
