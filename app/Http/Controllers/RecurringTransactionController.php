<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecurringTransactionController extends Controller
{
    /**
     * Display a listing of all recurring transactions.
     */
    public function index()
    {
        $recurringExpenses = Expense::where('is_recurring', true)
            ->with('category')
            ->orderBy('next_recurring_date')
            ->get()
            ->map(function ($expense) {
                $expense->type = 'expense';
                return $expense;
            });

        $recurringIncomes = Income::where('is_recurring', true)
            ->with('category')
            ->orderBy('next_recurring_date')
            ->get()
            ->map(function ($income) {
                $income->type = 'income';
                return $income;
            });

        $transactions = $recurringExpenses->merge($recurringIncomes)
            ->sortBy('next_recurring_date');

        return view('recurring.index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Skip the next occurrence of a recurring transaction.
     */
    public function skipNextOccurrence(Request $request, string $type, int $id)
    {
        $model = $this->getModel($type);
        $transaction = $model->findOrFail($id);

        $this->authorize('update', $transaction);

        // Calculate the next occurrence after skipping one
        $transaction->next_recurring_date = $this->calculateNextOccurrence(
            $transaction->next_recurring_date,
            $transaction->recurring_interval,
            2 // Skip next one, so calculate 2 intervals ahead
        );

        $transaction->save();

        return back()->with('success', 'Next occurrence skipped successfully.');
    }

    /**
     * End the recurrence of a transaction.
     */
    public function endRecurrence(Request $request, string $type, int $id)
    {
        $model = $this->getModel($type);
        $transaction = $model->findOrFail($id);

        $this->authorize('update', $transaction);

        $transaction->update([
            'is_recurring' => false,
            'next_recurring_date' => null,
            'recurring_interval' => null,
        ]);

        return back()->with('success', 'Recurrence ended successfully.');
    }

    /**
     * Pause a recurring transaction.
     */
    public function pause(Request $request, string $type, int $id)
    {
        $model = $this->getModel($type);
        $transaction = $model->findOrFail($id);

        $this->authorize('update', $transaction);

        $transaction->update([
            'is_paused' => true,
        ]);

        return back()->with('success', 'Recurring transaction paused.');
    }

    /**
     * Resume a paused recurring transaction.
     */
    public function resume(Request $request, string $type, int $id)
    {
        $model = $this->getModel($type);
        $transaction = $model->findOrFail($id);

        $this->authorize('update', $transaction);

        $transaction->update([
            'is_paused' => false,
        ]);

        return back()->with('success', 'Recurring transaction resumed.');
    }

    /**
     * Get the appropriate model based on type.
     */
    protected function getModel(string $type)
    {
        return $type === 'income' ? new Income : new Expense;
    }

    /**
     * Calculate the next occurrence date.
     */
    protected function calculateNextOccurrence($date, $interval, $count = 1)
    {
        $date = Carbon::parse($date);
        
        return match ($interval) {
            'daily' => $date->copy()->addDays($count),
            'weekly' => $date->copy()->addWeeks($count),
            'monthly' => $date->copy()->addMonths($count),
            'yearly' => $date->copy()->addYears($count),
            default => $date,
        };
    }
}
