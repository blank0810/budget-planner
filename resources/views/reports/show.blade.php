@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Report Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $reportData['title'] }}</h1>
                <p class="text-gray-600">{{ $reportData['period'] }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <a href="{{ route('reports.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Back to Reports
                </a>
                <a href="{{ route('reports.generate', [
                    'report_type' => request('report_type'),
                    'year' => request('year'),
                    'month' => request('month'),
                    'export_format' => 'pdf'
                ]) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Export as PDF
                </a>
                <a href="{{ route('reports.generate', [
                    'report_type' => request('report_type'),
                    'year' => request('year'),
                    'month' => request('month'),
                    'export_format' => 'excel'
                ]) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Export as Excel
                </a>
            </div>
        </div>
        
        <!-- Report Content -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            @if(view()->exists('reports.partials.' . $reportType))
            @include('reports.partials.' . $reportType, ['reportData' => $reportData])
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            The report view for "{{ $reportType }}" is not yet available. Please select another report type.
                        </p>
                    </div>
                </div>
            </div>
        @endif
        </div>
        
        <!-- Report Filters -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-medium text-gray-800 mb-4">Generate Another Report</h2>
            <form action="{{ route('reports.generate') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Report Type -->
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Report Type
                        </label>
                        <select name="report_type" id="report_type" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($reportTypes as $key => $label)
                                <option value="{{ $key }}" {{ $key === request('report_type') ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Year -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">
                            Year
                        </label>
                        <select name="year" id="year" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @php
                                $currentYear = date('Y');
                                $years = range($currentYear - 5, $currentYear + 1);
                            @endphp
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == request('year', $currentYear) ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Month -->
                    <div id="month-container">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">
                            Month (Optional)
                        </label>
                        <select name="month" id="month" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Months</option>
                            @foreach($months as $key => $month)
                                <option value="{{ $key }}" {{ $key == request('month') ? 'selected' : '' }}>
                                    {{ $month }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="pt-2">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any charts or interactive elements
        @if($reportType === 'expense_by_category')
            initExpenseByCategoryChart({!! json_encode($reportData) !!});
        @elseif($reportType === 'income_vs_expense')
            initIncomeVsExpenseChart({!! json_encode($reportData) !!});
        @elseif($reportType === 'budget_vs_actual')
            initBudgetVsActualChart({!! json_encode($reportData) !!});
        @endif
    });
    
    // Initialize expense by category chart
    function initExpenseByCategoryChart(data) {
        const ctx = document.getElementById('expense-by-category-chart');
        if (!ctx) return;
        
        const labels = data.expenses.map(item => item.category);
        const amounts = data.expenses.map(item => parseFloat(item.total));
        const colors = generateColors(labels.length);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: amounts,
                    backgroundColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Initialize income vs expense chart
    function initIncomeVsExpenseChart(data) {
        const ctx = document.getElementById('income-vs-expense-chart');
        if (!ctx) return;
        
        // Get all unique periods (months or years) from both income and expense data
        const periods = [...new Set([...Object.keys(data.incomes), ...Object.keys(data.expenses)])].sort();
        
        // Map the data to ensure consistent ordering and handle missing values
        const incomeData = periods.map(period => data.incomes[period] || 0);
        const expenseData = periods.map(period => data.expenses[period] || 0);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: periods,
                datasets: [
                    {
                        label: 'Income',
                        data: incomeData,
                        backgroundColor: 'rgba(16, 185, 129, 0.6)', // Green
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.7
                    },
                    {
                        label: 'Expenses',
                        data: expenseData,
                        backgroundColor: 'rgba(239, 68, 68, 0.6)', // Red
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-US', { 
                                        style: 'currency', 
                                        currency: 'USD' 
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: data.is_monthly ? 'Monthly Income vs Expenses' : 'Yearly Income vs Expenses',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: data.is_monthly ? 'Months' : 'Years'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount ($)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.raw.toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Initialize budget vs actual chart
    function initBudgetVsActualChart(data) {
        const ctx = document.getElementById('budget-vs-actual-chart');
        if (!ctx) return;
        
        // Sort categories by budgeted amount (descending) for better visualization
        const sortedBudgets = [...data.budgets].sort((a, b) => b.budgeted - a.budgeted);
        const categories = sortedBudgets.map(item => item.category);
        const budgetedData = sortedBudgets.map(item => item.budgeted);
        const spentData = sortedBudgets.map(item => item.spent);
        
        // Create the chart
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categories,
                datasets: [
                    {
                        label: 'Budgeted',
                        data: budgetedData,
                        backgroundColor: 'rgba(99, 102, 241, 0.6)', // Indigo
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.8
                    },
                    {
                        label: 'Spent',
                        data: spentData,
                        backgroundColor: function(context) {
                            const value = context.raw;
                            const budget = budgetedData[context.dataIndex];
                            return value > budget ? 'rgba(239, 68, 68, 0.8)' : 'rgba(16, 185, 129, 0.8)';
                        },
                        borderColor: function(context) {
                            const value = context.raw;
                            const budget = budgetedData[context.dataIndex];
                            return value > budget ? 'rgba(239, 68, 68, 1)' : 'rgba(16, 185, 129, 1)';
                        },
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-US', { 
                                        style: 'currency', 
                                        currency: 'USD' 
                                    }).format(context.parsed.y);
                                    
                                    // Add percentage for spent compared to budget
                                    if (context.dataset.label === 'Spent') {
                                        const budget = budgetedData[context.dataIndex];
                                        if (budget > 0) {
                                            const percentage = (context.parsed.y / budget) * 100;
                                            label += ` (${percentage.toFixed(1)}% of budget)`;
                                            
                                            if (context.parsed.y > budget) {
                                                label += ' - Over Budget!';
                                            }
                                        }
                                    }
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Budgeted vs Actual Spending by Category',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Categories'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount ($)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                // Add click event to drill down into category details
                onClick: function(e, elements) {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const category = categories[index];
                        // You can implement navigation to category detail view here
                        console.log('Clicked on category:', category);
                    }
                }
            }
        });
                        data: budgeted,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Spent',
    // Generate colors for charts with consistent hues
    function generateColors(count, saturation = 70, lightness = 60, alpha = 0.7) {
        const colors = [];
        const hueStep = 360 / count;
        
        for (let i = 0; i < count; i++) {
            const hue = Math.floor(i * hueStep) % 360;
            colors.push(`hsla(${hue}, ${saturation}%, ${lightness}%, ${alpha})`);
        }
        
        return colors;
    }
</script>
@endpush
