@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Copy Budgets</h2>
            
            <form action="{{ route('budgets.copy') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Source Month -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-700 mb-3">Copy From</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="from_month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                <select name="from_month" id="from_month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach($months as $key => $month)
                                        <option value="{{ $key }}" {{ $fromMonth == $key ? 'selected' : '' }}>{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="from_year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                <input type="number" name="from_year" id="from_year" min="2020" max="2030" 
                                       value="{{ $fromYear }}" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Target Month -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-700 mb-3">Copy To</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="to_month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                <select name="to_month" id="to_month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach($months as $key => $month)
                                        <option value="{{ $key }}" {{ $toMonth == $key ? 'selected' : '' }}>{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="to_year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                <input type="number" name="to_year" id="to_year" min="2020" max="2030" 
                                       value="{{ $toYear }}" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Budget Adjustment -->
                <div class="mb-6">
                    <label for="adjustment_percentage" class="block text-sm font-medium text-gray-700 mb-1">
                        Adjust Budget Amounts (%)
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" name="adjustment_percentage" id="adjustment_percentage" 
                               min="0.1" max="1000" step="0.1" value="100"
                               class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Adjust budget amounts by a percentage (e.g., 105 for a 5% increase, 95 for a 5% decrease)
                    </p>
                </div>
                
                <!-- Budget Categories -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-medium text-gray-700">Select Budgets to Copy</h3>
                        <div class="flex items-center">
                            <button type="button" id="select-all" class="text-sm text-indigo-600 hover:text-indigo-800 mr-3">Select All</button>
                            <button type="button" id="deselect-all" class="text-sm text-indigo-600 hover:text-indigo-800">Deselect All</button>
                        </div>
                    </div>
                    
                    @if($budgets->isEmpty())
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        No budgets found for the selected period. Please select a different period.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                            <ul class="divide-y divide-gray-200">
                                @foreach($budgets as $budget)
                                    <li class="px-4 py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <input id="category-{{ $budget->id }}" name="categories[]" type="checkbox" 
                                                       value="{{ $budget->category }}" 
                                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" checked>
                                                <label for="category-{{ $budget->id }}" class="ml-3 block text-gray-700">
                                                    <span class="font-medium">{{ $budget->category }}</span>
                                                    <span class="text-sm text-gray-500 ml-2">${{ number_format($budget->amount, 2) }}</span>
                                                </label>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $budget->month_name }} {{ $budget->year }}
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('budgets.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $budgets->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $budgets->isEmpty() ? 'disabled' : '' }}>
                        Copy Selected Budgets
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all/none functionality
        document.getElementById('select-all').addEventListener('click', function() {
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        });
        
        document.getElementById('deselect-all').addEventListener('click', function() {
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        });
        
        // Update form action when month/year changes
        const updateFormAction = () => {
            const fromMonth = document.getElementById('from_month').value;
            const fromYear = document.getElementById('from_year').value;
            const toMonth = document.getElementById('to_month').value;
            const toYear = document.getElementById('to_year').value;
            
            const url = new URL(window.location.href);
            url.searchParams.set('from_month', fromMonth);
            url.searchParams.set('from_year', fromYear);
            url.searchParams.set('to_month', toMonth);
            url.searchParams.set('to_year', toYear);
            
            // Reload the page with new parameters
            window.location.href = url.toString();
        };
        
        // Add event listeners for month/year selectors
        document.getElementById('from_month').addEventListener('change', updateFormAction);
        document.getElementById('from_year').addEventListener('change', updateFormAction);
        document.getElementById('to_month').addEventListener('change', updateFormAction);
        document.getElementById('to_year').addEventListener('change', updateFormAction);
    });
</script>
@endpush
@endsection
