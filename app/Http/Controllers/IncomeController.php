<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    /**
     * Display a listing of the incomes.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $incomes = $user->incomes()
            ->latest('date')
            ->paginate(10);

        return view('incomes.index', compact('incomes'));
    }

    /**
     * Show the form for creating a new income.
     */
    public function create()
    {
        return view('incomes.create', [
            'income' => new Income(),
            'categories' => $this->getIncomeCategories(),
            'recurringIntervals' => $this->getRecurringIntervals(),
        ]);
    }

    /**
     * Store a newly created income in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:10000000',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_interval' => [
                Rule::requiredIf(fn() => $request->boolean('is_recurring')),
                'nullable',
                Rule::in(array_keys($this->getRecurringIntervals())),
            ],
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $income = $user->incomes()->create($validated);

            return redirect()
                ->route('incomes.index')
                ->with('success', 'Income added successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating income: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to add income. Please try again.');
        }
    }

    /**
     * Display the specified income.
     */
    public function show(Income $income)
    {
        $this->authorize('view', $income);

        return view('incomes.show', compact('income'));
    }

    /**
     * Show the form for editing the specified income.
     */
    public function edit(Income $income)
    {
        $this->authorize('update', $income);

        return view('incomes.edit', [
            'income' => $income,
            'categories' => $this->getIncomeCategories(),
            'recurringIntervals' => $this->getRecurringIntervals(),
        ]);
    }

    /**
     * Update the specified income in storage.
     */
    public function update(Request $request, Income $income)
    {
        $this->authorize('update', $income);

        $validated = $request->validate([
            'source' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:10000000',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_interval' => [
                Rule::requiredIf(fn() => $request->boolean('is_recurring')),
                'nullable',
                Rule::in(array_keys($this->getRecurringIntervals())),
            ],
        ]);

        try {
            $income->update($validated);

            return redirect()
                ->route('incomes.index')
                ->with('success', 'Income updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating income: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to update income. Please try again.');
        }
    }

    /**
     * Remove the specified income from storage.
     */
    public function destroy(Income $income)
    {
        $this->authorize('delete', $income);

        try {
            $income->delete();

            return redirect()
                ->route('incomes.index')
                ->with('success', 'Income deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting income: ' . $e->getMessage());

            return back()
                ->with('error', 'Failed to delete income. Please try again.');
        }
    }

    /**
     * Get the list of income categories.
     */
    protected function getIncomeCategories(): array
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
}
