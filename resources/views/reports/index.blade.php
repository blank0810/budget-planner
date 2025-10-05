@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Financial Reports</h1>
            
            <form action="{{ route('reports.generate') }}" method="GET" class="space-y-6">
                @csrf
                
                <!-- Report Type -->
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Report Type
                    </label>
                    <select name="report_type" id="report_type" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                            onchange="updateReportForm()">
                        @foreach($reportTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Year -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">
                            Year
                        </label>
                        <select name="year" id="year" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Month (initially visible) -->
                    <div id="month-container">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">
                            Month
                        </label>
                        <select name="month" id="month" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Months</option>
                            @foreach($months as $key => $month)
                                <option value="{{ $key }}" {{ $key == $selectedMonth ? 'selected' : '' }}>
                                    {{ $month }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Export Options -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-4">Export as:</span>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="export_none" name="export_format" type="radio" value="" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked>
                                <label for="export_none" class="ml-2 block text-sm text-gray-700">
                                    View in Browser
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="export_pdf" name="export_format" type="radio" value="pdf" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <label for="export_pdf" class="ml-2 block text-sm text-gray-700">
                                    PDF
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="export_excel" name="export_format" type="radio" value="excel" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <label for="export_excel" class="ml-2 block text-sm text-gray-700">
                                    Excel
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="export_csv" name="export_format" type="radio" value="csv" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <label for="export_csv" class="ml-2 block text-sm text-gray-700">
                                    CSV
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="pt-4 flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Report Examples -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Monthly Summary</h3>
                <p class="text-gray-600 text-sm mb-4">
                    Get a quick overview of your income, expenses, and savings for a specific month.
                </p>
                <div class="h-32 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                    Preview
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Expenses by Category</h3>
                <p class="text-gray-600 text-sm mb-4">
                    See how your spending is distributed across different categories.
                </p>
                <div class="h-32 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                    Preview
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Income vs Expenses</h3>
                <p class="text-gray-600 text-sm mb-4">
                    Compare your income and expenses over time to track your financial health.
                </p>
                <div class="h-32 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                    Preview
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Budget vs Actual</h3>
                <p class="text-gray-600 text-sm mb-4">
                    Compare your budgeted amounts with your actual spending by category.
                </p>
                <div class="h-32 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                    Preview
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateReportForm() {
        const reportType = document.getElementById('report_type').value;
        const monthContainer = document.getElementById('month-container');
        
        // Show/hide month selector based on report type
        if (reportType === 'income_vs_expense') {
            monthContainer.style.display = 'block';
        } else if (reportType === 'monthly_summary') {
            monthContainer.style.display = 'block';
        } else if (reportType === 'expense_by_category') {
            monthContainer.style.display = 'block';
        } else if (reportType === 'budget_vs_actual') {
            monthContainer.style.display = 'block';
        } else {
            monthContainer.style.display = 'none';
        }
    }
    
    // Initialize the form
    document.addEventListener('DOMContentLoaded', function() {
        updateReportForm();
    });
</script>
@endpush
