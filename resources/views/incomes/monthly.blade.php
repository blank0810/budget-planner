@extends('layouts.app')

@section('title', 'Monthly Income Summary')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .summary-card {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transition-all duration-200 hover:shadow-lg;
        }
        .chart-container {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 h-96 transition-all duration-200;
        }
        .monthly-card {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300;
        }
        .monthly-card-header {
            @apply bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-white cursor-pointer;
        }
        .trend-up { @apply text-green-500 dark:text-green-400; }
        .trend-down { @apply text-red-500 dark:text-red-400; }
        .loading {
            @apply animate-pulse bg-gray-200 dark:bg-gray-700 rounded;
            min-height: 2rem;
        }
        [x-cloak] { display: none !important; }
        .empty-state {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center border border-dashed border-gray-200 dark:border-gray-700;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $selectedYear }} Income Overview</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Track and analyze your monthly income for {{ $selectedYear }}</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full md:w-auto">
            <!-- Year Selector -->
            <form method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <select name="year" onchange="this.form.submit()"
                        class="appearance-none bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:text-white">
                        @foreach($availableYears as $yearOption)
                            <option value="{{ $yearOption }}" {{ $selectedYear == $yearOption ? 'selected' : '' }}>
                                {{ $yearOption }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <input type="hidden" name="view" value="monthly">
            </form>
            
            <!-- Switch to List View Button -->
            <a href="{{ route('incomes.index', ['view' => 'list', 'year' => $selectedYear]) }}" 
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                View List
            </a>
        </div>
    </div>

    <!-- Year Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="summary-card">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Income ({{ $selectedYear }})</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($monthlyData->sum('total_amount'), 2) }}</p>
            <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                <span>{{ $monthlyData->where('has_transactions', true)->count() }} {{ Str::plural('month', $monthlyData->where('has_transactions', true)->count()) }} with income</span>
            </div>
        </div>
        <div class="summary-card">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Monthly Average</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($monthlyData->avg('total_amount'), 2) }}</p>
            <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                <span>Based on active months</span>
            </div>
        </div>
        <div class="summary-card">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Transactions</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($monthlyData->sum('transaction_count')) }}</p>
            <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                <span>Across all categories</span>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Breakdown</h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $monthlyData->where('has_transactions', true)->count() }} {{ Str::plural('month', $monthlyData->where('has_transactions', true)->count()) }} with income
            </div>
        </div>
        @include('incomes.partials.monthly_cards', [
            'monthlyIncomes' => $monthlyData->sortBy('month_number')->values()->all()
        ])
    </div>
        @if($monthlyData->isEmpty())
        <div class="empty-state">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No income data for {{ $selectedYear }}</h3>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Start adding income to see your monthly breakdown.</p>
            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('incomes.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Income
                </a>
                @if($availableYears->count() > 1)
                    <a href="{{ route('incomes.index', ['view' => 'monthly', 'year' => $availableYears->first() == $selectedYear ? $availableYears->last() : $availableYears->first()]) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        View {{ $availableYears->first() == $selectedYear ? $availableYears->last() : $availableYears->first() }} Data
                    </a>
                @endif
            </div>
        </div>
    @else
        <!-- Yearly Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Summary -->
            <div class="chart-container">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Monthly Summary ({{ $selectedYear }})</h3>
                <div class="h-80">
                    <canvas id="monthlySummaryChart"></canvas>
                </div>
            </div>

            <!-- Monthly Comparison -->
            <div class="chart-container">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Monthly Comparison ({{ $selectedYear }})</h3>
                <div class="h-80">
                    <canvas id="monthlyComparisonChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    @if($monthlyData->isNotEmpty())
    <!-- Year Summary -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-8 transition-all duration-200">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ $selectedYear }} Summary</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Annual income and transaction overview</p>
        </div>
        <div class="px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200 dark:sm:divide-gray-700">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Income</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                        ${{ number_format($monthlyData->sum('total_amount'), 2) }}
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transactions</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                        {{ number_format($monthlyData->sum('transaction_count')) }}
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Average Monthly Income</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                        ${{ number_format($monthlyData->where('has_transactions', true)->avg('total_amount'), 2) }}
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Months with Income</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                        {{ $monthlyData->where('has_transactions', true)->count() }} of 12
                    </dd>
                </div>
            </dl>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize charts if we have data
        @if($monthlyData->isNotEmpty())
            // Monthly Summary Chart (Bar Chart)
            const monthlySummaryCtx = document.getElementById('monthlySummaryChart').getContext('2d');
            new Chart(monthlySummaryCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyData->pluck('month_name')) !!},
                    datasets: [{
                        label: 'Income',
                        data: {!! json_encode($monthlyData->pluck('total_amount')) !!},
                        backgroundColor: '#10b981',
                        borderColor: '#059669',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Income: $' + context.raw.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    }
                }
            });

            // Monthly Comparison Chart (Line Chart)
            const monthlyComparisonCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
            new Chart(monthlyComparisonCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyData->pluck('month_name')) !!},
                    datasets: [{
                        label: 'Income',
                        data: {!! json_encode($monthlyData->pluck('total_amount')) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#3b82f6',
                        pointHoverBorderColor: '#fff',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Income: $' + context.raw.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    }
                }
            });
        @endif
    });
</script>
@endpush
@endsection
