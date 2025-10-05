<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Get monthly totals
        $monthlyIncome = Income::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $monthlyExpenses = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $balance = $monthlyIncome - $monthlyExpenses;

        // Get recent transactions (last 10)
        $recentIncomes = Income::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        $recentExpenses = Expense::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        // Merge and sort transactions
        $recentTransactions = $recentIncomes->concat($recentExpenses)
            ->sortByDesc('date')
            ->take(10);

        // Get expense by category for the current month
        $expensesByCategory = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        // Get current month's budgets with spending
        $budgets = Budget::where('user_id', $user->id)
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->where('is_active', true)
            ->with(['expenses' => function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }])
            ->get()
            ->map(function($budget) {
                $spent = $budget->expenses->sum('amount');
                $remaining = max(0, $budget->amount - $spent);
                $utilization = $budget->amount > 0 ? min(100, ($spent / $budget->amount) * 100) : 100;
                
                return (object) [
                    'category' => $budget->category,
                    'budgeted' => $budget->amount,
                    'spent' => $spent,
                    'remaining' => $remaining,
                    'utilization' => $utilization,
                    'status' => $utilization >= 100 ? 'over' : ($utilization >= 80 ? 'warning' : 'good')
                ];
            });

        // Calculate budget summary
        $totalBudgeted = $budgets->sum('budgeted');
        $totalSpent = $budgets->sum('spent');
        $totalRemaining = $budgets->sum('remaining');
        $totalUtilization = $totalBudgeted > 0 ? min(100, ($totalSpent / $totalBudgeted) * 100) : 0;

        return view('dashboard', [
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpenses' => $monthlyExpenses,
            'balance' => $balance,
            'recentTransactions' => $recentTransactions,
            'expensesByCategory' => $expensesByCategory,
            'budgets' => $budgets,
            'budgetSummary' => (object) [
                'total_budgeted' => $totalBudgeted,
                'total_spent' => $totalSpent,
                'total_remaining' => $totalRemaining,
                'utilization' => $totalUtilization,
                'status' => $totalUtilization >= 100 ? 'over' : ($totalUtilization >= 80 ? 'warning' : 'good')
            ]
        ]);
    }
}
