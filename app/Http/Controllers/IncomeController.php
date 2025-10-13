<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Services\IncomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class IncomeController extends Controller
{
    protected IncomeService $incomeService;

    public function __construct(IncomeService $incomeService)
    {
        $this->incomeService = $incomeService;
    }

    /**
     * Display a listing of the incomes.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get view mode from request, default to 'list' if not set or invalid
        $validViewModes = ['list', 'monthly'];
        $viewMode = in_array($request->get('view'), $validViewModes)
            ? $request->get('view')
            : 'list';

        // Get filter values with defaults
        $year = (int)$request->input('year', now()->year);
        $month = $request->has('month') ? (int)$request->input('month') : null;

        // Get available years for dropdown
        $availableYears = $this->incomeService->getAvailableYears($user);

        // Check if the selected year is valid, if not, use the most recent year
        $availableYearsArray = $availableYears->toArray();
        if (!in_array($year, $availableYearsArray) && $availableYears->isNotEmpty()) {
            $year = $availableYears->max();
        }

        // Get yearly summary data
        $yearlySummary = $this->incomeService->getYearlySummary($user, $year);

        $data = [
            'viewMode' => $viewMode,
            'incomes' => collect([]),
            'monthlyIncomes' => collect([]),
            'selectedYear' => $year,
            'selectedMonth' => $month,
            'selectedCategory' => $request->input('category'),
            'availableYears' => $availableYears,
            'yearlySummary' => $yearlySummary,
            'categories' => $this->incomeService->getIncomeCategories(),
            'months' => [
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
                12 => 'December'
            ],
        ];

        if ($viewMode === 'monthly') {
            // Get all monthly incomes for the selected year
            $monthlyIncomes = $this->incomeService->getMonthlyIncomes($user, $year);
            $data['monthlyIncomes'] = $monthlyIncomes;

            $monthlyData = collect();
            for ($month = 1; $month <= 12; $month++) {
                $summary = $this->incomeService->getMonthlySummary($user, $year, $month, false);
                $date = Carbon::createFromDate($year, $month, 1);
                $monthlyData->push([
                    'month' => $date->format('F Y'),
                    'month_name' => $date->format('F'),
                    'month_number' => $month,
                    'total_amount' => $summary['total_amount'],
                    'transaction_count' => $monthlyIncomes->get($month, collect())->count(),
                    'incomes' => $monthlyIncomes->get($month, collect()),
                    'largest_income' => $summary['largest_income'] ?? null,
                ]);
            }
            $data['monthlyData'] = $monthlyData;
            $data['monthlyIncomes'] = $monthlyData; // Keep for backward compatibility
            $data['incomesByMonth'] = $monthlyIncomes;
            // dd($data);
        } else {
            // Get paginated list of incomes with filters
            $query = $user->incomes()
                ->whereYear('date', $year);

            if ($month) {
                $query->whereMonth('date', $month);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->input('category'));
            }

            $incomes = $query->orderBy('date', 'desc')
                ->paginate(15)
                ->withQueryString();

            $data['incomes'] = $incomes;
        }

        return view('incomes.index', $data);
    }

    /**
     * Show the form for creating a new income.
     */
    public function create()
    {
        return view('incomes.create', [
            'income' => new Income(),
            'categories' => $this->incomeService->getIncomeCategories(),
            'recurringIntervals' => $this->incomeService->getRecurringIntervals(),
        ]);
    }

    /**
     * Store a newly created income in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateIncomeRequest($request);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->incomes()->create($validated);
            $this->incomeService->clearCache(Auth::user());

            return redirect()
                ->route('incomes.index')
                ->with('success', 'Income added successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating income: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to add income. Please try again.');
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
            'categories' => $this->incomeService->getIncomeCategories(),
            'recurringIntervals' => $this->incomeService->getRecurringIntervals(),
        ]);
    }

    /**
     * Update the specified income in storage.
     */
    public function update(Request $request, Income $income)
    {
        $this->authorize('update', $income);
        $validated = $this->validateIncomeRequest($request);

        try {
            $income->update($validated);
            $this->incomeService->clearCache(Auth::user());

            return redirect()
                ->route('incomes.index')
                ->with('success', 'Income updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating income: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update income. Please try again.');
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
            $this->incomeService->clearCache(Auth::user());

            return redirect()
                ->route('incomes.index')
                ->with('success', 'Income deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting income: ' . $e->getMessage());
        }
    }

    /**
     * Display monthly income summary and insights.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function monthlyView(Request $request)
    {
        $year = (int)$request->input('year', now()->year);
        $user = Auth::user();

        // Get monthly data using the existing getMonthlySummary method
        $monthlyData = collect();
        for ($month = 1; $month <= 12; $month++) {
            $summary = $this->incomeService->getMonthlySummary($user, $year, $month, false);
            $date = Carbon::createFromDate($year, $month, 1);
            $monthlyData->push([
                'year' => $year,
                'month' => $date->format('F Y'),
                'month_name' => $date->format('F'),
                'month_number' => $month,
                'total_amount' => $summary['total_amount'],
                'transaction_count' => $summary['transaction_count'],
                'has_transactions' => $summary['transaction_count'] > 0
            ]);
        }

        $availableYears = $this->incomeService->getAvailableYears($user);

        // If the requested year has no data, try to find the most recent year with data
        if ($monthlyData->isEmpty() && $availableYears->isNotEmpty()) {
            $year = $availableYears->first();
            return $this->monthlyView(new Request(['year' => $year]));
        }

        return view('incomes.monthly', [
            'monthlyData' => $monthlyData,
            'availableYears' => $availableYears,
            'selectedYear' => $year
        ]);
    }

    /**
     * Validate the income request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function validateIncomeRequest(Request $request)
    {
        return $request->validate([
            // 'description' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:10000000',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_interval' => [
                Rule::requiredIf(fn() => $request->boolean('is_recurring')),
                'nullable',
                Rule::in(array_keys($this->incomeService->getRecurringIntervals())),
            ],
        ]);
    }
}
