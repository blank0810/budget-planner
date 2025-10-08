<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BudgetController extends Controller
{
    /**
     * Display a listing of the budgets.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $budgets = $user->budgets()
            ->latest('year')
            ->latest('month')
            ->paginate(10);

        return view('budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new budget.
     */
    public function create()
    {
        return view('budgets.create', [
            'budget' => new Budget(),
            'categories' => $this->getExpenseCategories(),
            'months' => $this->getMonths(),
            'years' => $this->getYears(),
        ]);
    }

    /**
     * Store a newly created budget in storage.
     */
    public function store(Request $request)
    {
        // Ensure month and year are integers before validation
        $request->merge([
            'month' => (int) $request->month,
            'year' => (int) $request->year,
        ]);

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'budget_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:10000000',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Check if budget with same name already exists for this category/month/year
            $existingBudget = $user->budgets()
                ->where('category', $validated['category'])
                ->where('budget_name', $validated['budget_name'])
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->first();

            if ($existingBudget) {
                return back()
                    ->withInput()
                    ->with('error', 'A budget with this name already exists for the selected category and period.');
            }

            $budget = $user->budgets()->create($validated);

            return redirect()
                ->route('budgets.index')
                ->with('success', 'Budget created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating budget: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to create budget. Please try again.');
        }
    }

    /**
     * Display the specified budget.
     */
    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);

        return view('budgets.show', compact('budget'));
    }

    /**
     * Show the form for editing the specified budget.
     */
    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);

        return view('budgets.edit', [
            'budget' => $budget,
            'categories' => $this->getExpenseCategories(),
            'months' => $this->getMonths(),
            'years' => $this->getYears(),
        ]);
    }

    /**
     * Update the specified budget in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        // Ensure month and year are integers before validation
        $request->merge([
            'month' => (int) $request->month,
            'year' => (int) $request->year,
        ]);

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'budget_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:10000000',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        try {
            // Check if another budget with same name exists for this category/month/year (excluding current budget)
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $existingBudget = $user->budgets()
                ->where('category', $validated['category'])
                ->where('budget_name', $validated['budget_name'])
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->where('id', '!=', $budget->id)
                ->first();

            if ($existingBudget) {
                return back()
                    ->withInput()
                    ->with('error', 'A budget with this name already exists for the selected category and period.');
            }

            $budget->update($validated);

            return redirect()
                ->route('budgets.index')
                ->with('success', 'Budget updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating budget: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to update budget. Please try again.');
        }
    }

    /**
     * Remove the specified budget from storage.
     */
    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);

        try {
            $budget->delete();

            return redirect()
                ->route('budgets.index')
                ->with('success', 'Budget deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting budget: ' . $e->getMessage());

            return back()
                ->with('error', 'Failed to delete budget. Please try again.');
        }
    }

    /**
     * Get the list of expense categories (same as ExpenseController).
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
     * Get the list of months.
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

    /**
     * Get the list of years (current year ± 5 years).
     */
    protected function getYears()
    {
        $currentYear = (int) date('Y');
        return range($currentYear - 1, $currentYear + 1);
    }

    /**
     * Show the form to copy budgets from a previous month.
     */
    public function showCopyForm()
    {
        $currentDate = now();
        $prevMonth = $currentDate->copy()->subMonth();

        $fromYear = (int) request()->input('from_year', $prevMonth->year);
        $fromMonth = (int) request()->input('from_month', $prevMonth->month);

        // Get budgets from the source month
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $budgets = $user->budgets()
            ->where('year', $fromYear)
            ->where('month', $fromMonth)
            ->orderBy('category')
            ->get();

        // Get target month (default to current month)
        $toYear = (int) request()->input('to_year', $currentDate->year);
        $toMonth = (int) request()->input('to_month', $currentDate->month);

        return view('budgets.copy', [
            'budgets' => $budgets,
            'fromYear' => $fromYear,
            'fromMonth' => $fromMonth,
            'toYear' => $toYear,
            'toMonth' => $toMonth,
            'months' => $this->getMonths(),
            'years' => $this->getYears(),
            'nextYear' => $currentDate->year + 1,
        ]);
    }

    /**
     * Copy budgets from one month to another.
     */
    public function copyBudgets(Request $request)
    {
        // Ensure all date fields are integers before validation
        $request->merge([
            'from_year' => (int) $request->from_year,
            'from_month' => (int) $request->from_month,
            'to_year' => (int) $request->to_year,
            'to_month' => (int) $request->to_month,
        ]);

        $validated = $request->validate([
            'from_year' => 'required|integer|min:2020|max:2030',
            'from_month' => 'required|integer|min:1|max:12',
            'to_year' => 'required|integer|min:2020|max:2030',
            'to_month' => 'required|integer|min:1|max:12',
            'categories' => 'sometimes|array',
            'categories.*' => 'string',
            'adjustment_percentage' => 'sometimes|numeric|min:0.1|max:1000',
        ]);

        $adjustmentFactor = isset($validated['adjustment_percentage'])
            ? (float) $validated['adjustment_percentage'] / 100
            : 1.0;

        $result = Budget::copyBudgets(
            $validated['from_year'],
            $validated['from_month'],
            $validated['to_year'],
            $validated['to_month'],
            Auth::id(),
            $validated['categories'] ?? [],
            $adjustmentFactor
        );

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        $redirect = redirect()
            ->route('budgets.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);

        if ($result['success'] && isset($result['copied_count']) && $result['copied_count'] > 0) {
            $redirect->with('copied_count', $result['copied_count']);
        }

        return $redirect;
    }
}
