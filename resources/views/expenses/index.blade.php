<x-app-layout>
    <!-- Yearly Summary Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-red-600 to-orange-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        {{ $selectedYear }} Yearly Summary
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                    <!-- Total Expenses Card -->
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Expenses</p>
                                <p class="mt-1 text-3xl font-bold text-gray-900">
                                    {{ number_format($yearlySummary['total_amount'], 2) }}
                                    {{ config('app.currency') }}
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">
                                <span class="font-medium text-red-600">{{ $yearlySummary['transaction_count'] }}</span>
                                transactions this year
                            </p>
                        </div>
                    </div>

                    <!-- Average Monthly Expenses Card -->
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Avg. Amount</p>
                                <p class="mt-1 text-3xl font-bold text-gray-900">
                                    {{ number_format($yearlySummary['average_monthly'], 2) }}
                                    {{ config('app.currency') }}
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">
                                <span
                                    class="font-medium text-blue-600">{{ $yearlySummary['monthly_average_transactions'] }}</span>
                                avg transactions/month
                            </p>
                        </div>
                    </div>

                    <!-- Yearly Comparison Card -->
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Yearly Comparison</p>
                                @php
$comparison = 0;
$hasPreviousYearData = !empty($previousYearSummary['total_expenses']) && $previousYearSummary['total_expenses'] > 0;
$hasCurrentYearData = !empty($yearlySummary['total_expenses']);
$showComparison = $hasPreviousYearData && $hasCurrentYearData;

if ($showComparison) {
    $comparison = (($yearlySummary['total_expenses'] - $previousYearSummary['total_expenses']) / $previousYearSummary['total_expenses']) * 100;
}

$isIncrease = $comparison >= 0;
                                @endphp
                                <div class="mt-1">
                                    @if($showComparison)
                                        <p class="text-3xl font-bold text-gray-900">
                                            {{ number_format(abs($comparison), 1) }}%
                                        </p>
                                        <p
                                            class="mt-2 flex items-center text-sm font-semibold {{ $isIncrease ? 'text-green-600' : 'text-red-600' }}">
                                            @if($isIncrease)
                                                <svg class="h-5 w-5 text-green-500 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                                </svg>
                                                <span>from {{ $previousYearSummary['year'] ?? ($selectedYear - 1) }}</span>
                                            @else
                                                <svg class="h-5 w-5 text-red-500 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                                </svg>
                                                <span>from {{ $previousYearSummary['year'] ?? ($selectedYear - 1) }}</span>
                                            @endif
                                        </p>
                                    @else
                                        <p class="text-3xl font-bold text-gray-900">N/A</p>
                                        <p class="mt-2 text-sm text-gray-500">
                                            @if(!$hasCurrentYearData)
                                                No data for {{ $selectedYear }}
                                            @else
                                                No data for {{ $selectedYear - 1 }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="p-3 rounded-full {{ $isIncrease ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                @if($isIncrease)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">
                                vs. {{ $selectedYear - 1 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Expense Entries') }}
            </h2>
            <a href="{{ route('expenses.create') }}"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Expense
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        init() {
            // Initialize any Alpine.js functionality here
        },
        exportExpenses() {
            // Add export functionality if needed
            console.log('Exporting expenses...');
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Actions Bar -->
            <div class="mb-8 bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl shadow-lg p-6 border border-red-100">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-br from-red-500 to-orange-600 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Expense Management</h2>
                            <p class="text-sm text-gray-600">Track and manage your expenses</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 w-full lg:w-auto">
                        <!-- View Mode Toggle -->
                        <div class="inline-flex rounded-xl shadow-md overflow-hidden border border-gray-200"
                            role="group">
                            @php
$listQuery = array_merge(request()->query(), ['view' => 'list']);
$monthlyQuery = array_merge(request()->query(), ['view' => 'monthly']);
                            @endphp
                            <a href="{{ route('expenses.index', $listQuery) }}"
                                class="px-4 py-2 text-sm font-medium {{ $viewMode === 'list' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border-r border-gray-200">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    <span>List View</span>
                                </div>
                            </a>
                            <a href="{{ route('expenses.index', $monthlyQuery) }}"
                                class="px-4 py-2 text-sm font-medium {{ $viewMode === 'monthly' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>Monthly View</span>
                                </div>
                            </a>
                        </div>

                        <!-- Add Expense Button -->
                        <a href="{{ route('expenses.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Expense
                        </a>
                    </div>
                </div>
            </div>

            @if($viewMode === 'monthly')
                <!-- Monthly Cards View -->
                @include('expenses.partials.monthly_cards', ['monthlyExpenses' => $monthlySummaries])
            @else
                <!-- List View -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <!-- Filters -->
                        {{-- <form method="GET" action="{{ route('expenses.index') }}" class="space-y-4">
                            <input type="hidden" name="view" value="list">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                                    <select id="month" name="month" onchange="this.form.submit()"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                                        @foreach($months as $key => $name)
                                        <option value="{{ $key }}" {{ $selectedMonth==$key ? 'selected' : '' }}>{{ $name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                                    <select id="year" name="year" onchange="this.form.submit()"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                                        @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $selectedYear==$year ? 'selected' : '' }}>{{ $year }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                    <select id="category" name="category" onchange="this.form.submit()"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ $selectedCategory==$category ? 'selected' : ''
                                            }}>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form> --}}
                        <!-- Filters -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <form method="GET" action="{{ route('expenses.index') }}" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                                            <select id="month" name="month"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                                                @foreach($months as $key => $name)
                                                    <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>
                                                        {{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                                            <select id="year" name="year"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                                                @foreach($availableYears as $year)
                                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="category"
                                                class="block text-sm font-medium text-gray-700">Category</label>
                                            <select id="category" name="category"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                                                <option value="">All Categories</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category }}" {{ $selectedCategory == $category ? 'selected' : '' }}>{{ $category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex items-end">
                                            <button type="submit"
                                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Apply Filters
                                            </button>
                                            <a href="{{ route('expenses.index') }}"
                                                class="ml-2 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">

                                @if($expenses->isEmpty())
                                    <div class="text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No expense entries yet</h3>
                                        <p class="mt-1 text-sm text-gray-500">Get started by adding your first expense entry.
                                        </p>
                                        <div class="mt-6">
                                            <a href="{{ route('expenses.create') }}"
                                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                New Expense
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-col">
                                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    Date
                                                                </th>
                                                                <th scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    Description
                                                                </th>
                                                                <th scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    Category
                                                                </th>
                                                                <th scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    Budget
                                                                </th>
                                                                <th scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    Amount
                                                                </th>
                                                                <th scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    Payment Method
                                                                </th>
                                                                <th scope="col"
                                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    Status
                                                                </th>
                                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    Actions
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @foreach($expenses as $expense)
                                                                <tr>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        {{ $expense->date->format('M d, Y') }}
                                                                    </td>
                                                                    <td class="px-6 py-4">
                                                                        <div class="text-sm font-medium text-gray-900">
                                                                            {{ $expense->description }}
                                                                            @if($expense->is_recurring)
                                                                                <span
                                                                                    class="ml-2 text-xs text-gray-500">({{ ucfirst($expense->recurring_interval) }})</span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        {{ $expense->category ?: '<span class="text-gray-400">—</span>' }}
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        @if($expense->budget)
                                                                            <div class="flex items-center">
                                                                                <span
                                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $expense->isBudgetExceeded() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}"
                                                                                    title="{{ $expense->budget->budget_name }} ({{ $expense->budget->month }}/{{ $expense->budget->year }}): ${{ number_format($expense->budget->getTotalSpent(), 2) }} / ${{ number_format($expense->budget->amount, 2) }}">
                                                                                    {{ $expense->budget->budget_name }}
                                                                                    @if($expense->isBudgetExceeded())
                                                                                        <svg class="ml-1 h-3 w-3 inline-block"
                                                                                            fill="currentColor" viewBox="0 0 20 20"
                                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                                            <path fill-rule="evenodd"
                                                                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                                                clip-rule="evenodd" />
                                                                                        </svg>
                                                                                    @endif
                                                                                </span>
                                                                            </div>
                                                                        @else
                                                                            <span class="text-gray-400">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        <span class="font-medium text-red-600">-{{ config('app.currency') }}{{ number_format($expense->amount, 2) }}</span>
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        <span class="capitalize">{{ str_replace('_', ' ', $expense->payment_method) }}</span>
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                                        @if($expense->is_recurring)
                                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                                Recurring
                                                                            </span>
                                                                        @else
                                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                                                One-time
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                                        <div class="flex items-center justify-end space-x-2">
                                                                            <a href="{{ route('expenses.show', $expense) }}"
                                                                                class="text-blue-600 hover:text-blue-900">
                                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                                    stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                                </svg>
                                                                            </a>
                                                                            <a href="{{ route('expenses.edit', $expense) }}"
                                                                                class="text-yellow-600 hover:text-yellow-900">
                                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                                    stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                                </svg>
                                                                            </a>
                                                                            <form action="{{ route('expenses.destroy', $expense) }}" 
                                                                                method="POST" 
                                                                                onsubmit="return confirm('Are you sure you want to delete this expense entry?');"
                                                                                class="inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                                        stroke="currentColor">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                                    </svg>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        {{ $expenses->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
            @endif
                </div>
            </div>

</x-app-layout>