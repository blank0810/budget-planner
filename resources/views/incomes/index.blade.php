<x-app-layout>
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        {{ $selectedYear }} Yearly Summary
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                    <!-- Total Income Card -->
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Income</p>
                                <p class="mt-1 text-3xl font-bold text-gray-900">
                                    {{ number_format($yearlySummary['total_income'], 2) }} {{ config('app.currency') }}
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">
                                <span class="font-medium text-emerald-600">{{ $yearlySummary['transaction_count'] }}</span> transactions this year
                            </p>
                        </div>
                    </div>

                    <!-- Average Monthly Income Card -->
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Avg. Monthly</p>
                                <p class="mt-1 text-3xl font-bold text-gray-900">
                                    {{ number_format($yearlySummary['average_monthly'], 2) }} {{ config('app.currency') }}
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">
                                Based on {{ count($yearlySummary['monthly_breakdown']) }} months
                            </p>
                        </div>
                    </div>

                    <!-- Highest Month Card -->
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Highest Month</p>
                                <p class="mt-1 text-3xl font-bold text-gray-900">
                                    {{ $yearlySummary['highest_month']['month'] }}
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h3a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h3m0 0V5a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">
                                <span class="font-medium text-amber-600">{{ number_format($yearlySummary['highest_month']['amount'], 2) }} {{ config('app.currency') }}</span> in {{ $yearlySummary['highest_month']['month'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Income Management') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">Track and manage your income streams</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="incomeIndex()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Actions Bar -->
            <div
                class="mb-8 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl shadow-lg p-6 border border-emerald-100">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Your Income History</h3>
                            <p class="text-sm text-gray-600">View and manage your income entries</p>
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
                            <a href="{{ route('incomes.index', $listQuery) }}"
                                class="group px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ $viewMode === 'list' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                    <span class="hidden sm:inline">List View</span>
                                </div>
                            </a>
                            <a href="{{ route('incomes.index', $monthlyQuery) }}"
                                class="group px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ $viewMode === 'monthly' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span class="hidden sm:inline">Monthly</span>
                                </div>
                            </a>
                        </div>

                        <!-- Date Filters -->
                        {{-- <form method="GET" class="flex flex-wrap gap-2">
                            @if($viewMode === 'list')
                                <!-- Month Filter (only for list view) -->
                                <div class="relative">
                                    <select name="month" onchange="this.form.submit()"
                                        class="appearance-none bg-white border border-gray-300 rounded-lg pl-4 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">All Months</option>
                                        @foreach($months as $key => $name)
                                            <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>{{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                            @endif

                            <!-- Year Filter -->
                            <div class="relative">
                                <select name="year" onchange="this.form.submit()"
                                    class="appearance-none bg-white border border-gray-300 rounded-lg pl-4 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    @forelse($availableYears as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}
                                        </option>
                                    @empty
                                        <option value="{{ now()->year }}">{{ now()->year }}</option>
                                    @endforelse
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Hidden fields to preserve view mode -->
                            @if(request()->has('view'))
                                <input type="hidden" name="view" value="{{ $viewMode }}">
                            @endif
                        </form> --}}

                        <!-- Add New Income Button -->
                        <a href="{{ route('incomes.create') }}"
                            class="group inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Add Income</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div x-show="isLoading" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="flex flex-col items-center justify-center py-16">
                <div class="relative">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-200"></div>
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-emerald-600 absolute top-0 left-0"></div>
                </div>
                <p class="mt-4 text-gray-600 font-medium">Loading your income data...</p>
            </div>

            @if($viewMode === 'list')
                <!-- List View -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <!-- Filters -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <form method="GET" action="{{ route('incomes.index') }}" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                                            <select id="month" name="month"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm rounded-md">
                                                <option value="">All Months</option>
                                                @foreach($months as $key => $name)
                                                    <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>{{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                                            <select id="year" name="year"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm rounded-md">
                                                @foreach($availableYears as $year)
                                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                            <select id="category" name="category"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm rounded-md">
                                                <option value="">All Categories</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category }}" {{ $selectedCategory == $category ? 'selected' : '' }}>
                                                        {{ $category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex items-end">
                                            <button type="submit"
                                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                                Apply Filters
                                            </button>
                                            <a href="{{ route('incomes.index') }}"
                                                class="ml-2 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                                Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @if($incomes->isEmpty())
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No income entries</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    @if($selectedMonth)
                                        No income entries found for {{ $months[$selectedMonth] ?? 'selected month' }} {{ $selectedYear }}. Try a
                                        different month or year.
                                    @else
                                        No income entries found for {{ $selectedYear }}. Add your first income entry to get started.
                                    @endif
                                </p>
                                <div class="mt-6">
                                    <a href="{{ route('incomes.create') }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        New Income
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                                                                Source
                                                            </th>
                                                            <th scope="col"
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Category
                                                            </th>
                                                            <th scope="col"
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Amount
                                                            </th>
                                                            <th scope="col" class="relative px-6 py-3">
                                                                <span class="sr-only">Actions</span>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @foreach($incomes as $income)
                                                            <tr class="hover:bg-gray-50">
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    {{ $income->date->format('M d, Y') }}
                                                                    @if($income->is_recurring)
                                                                        <span
                                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                            {{ ucfirst($income->recurring_interval) }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-6 py-4">
                                                                    <div class="text-sm font-medium text-gray-900">{{ $income->source }}</div>
                                                                    @if($income->description)
                                                                        <div class="text-sm text-gray-500">{{ $income->description }}</div>
                                                                    @endif
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <span
                                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                        {{ $income->category }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600">
                                                                    {{ number_format($income->amount, 2) }} {{ config('app.currency') }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    <span
                                                                        class="capitalize">{{ str_replace('_', ' ', $income->payment_method) }}</span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                                    <div class="flex items-center justify-end space-x-2">
                                                                        <a href="{{ route('incomes.show', $income) }}"
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
                                                                        <a href="{{ route('incomes.edit', $income) }}"
                                                                            class="text-yellow-600 hover:text-yellow-900">
                                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                                stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                            </svg>
                                                                        </a>
                                                                        <form action="{{ route('incomes.destroy', $income) }}" method="POST"
                                                                            onsubmit="return confirm('Are you sure you want to delete this income record?');">
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

                                @if(method_exists($incomes, 'links'))
                                    <div class="mt-4">
                                        {{ $incomes->links() }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Monthly Card View -->
                @include('incomes.partials.monthly_cards', ['monthlyIncomes' => $monthlyIncomes])
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function incomeIndex() {
                return {
                    isLoading: false
                };
            }
        </script>
    @endpush
</x-app-layout>