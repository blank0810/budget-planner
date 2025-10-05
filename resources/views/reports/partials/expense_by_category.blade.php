<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">{{ $reportData['title'] }}</h2>
    
    <!-- Summary Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-8 border-l-4 border-purple-500">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Expenses</p>
                <p class="text-3xl font-bold text-gray-800">${{ number_format($reportData['total_expenses'], 2) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $reportData['period'] }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                    {{ count($reportData['expenses']) }} Categories
                </span>
            </div>
        </div>
    </div>
    
    <!-- Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Expense Distribution</h3>
        <div class="h-80">
            <canvas id="expense-by-category-chart"></canvas>
        </div>
    </div>
    
    <!-- Category Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Expense Details by Category</h3>
        </div>
        
        @if(count($reportData['expenses']) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reportData['expenses'] as $expense)
                            @php
                                $budget = $budgets->firstWhere('category', $expense->category);
                                $remaining = $budget ? $budget->amount - $expense->total : null;
                                $isOverBudget = $budget && $expense->total > $budget->amount;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $expense->category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                    ${{ number_format($expense->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    {{ number_format($expense->percentage, 1) }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $isOverBudget ? 'text-red-600' : 'text-gray-500' }}">
                                    @if($budget)
                                        ${{ number_format($budget->amount, 2) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $isOverBudget ? 'text-red-600' : 'text-gray-500' }}">
                                    @if($budget)
                                        ${{ number_format($remaining, 2) }}
                                        @if($isOverBudget)
                                            <span class="text-red-500 ml-1">(Over)</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                                ${{ number_format($reportData['total_expenses'], 2) }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500">100%</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500">-</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500">-</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No expenses found</h3>
                <p class="mt-1 text-sm text-gray-500">There are no expenses for the selected period.</p>
            </div>
        @endif
    </div>
    
    <!-- Category Breakdown -->
    @if(count($reportData['expenses']) > 0)
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Category Breakdown</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($reportData['expenses'] as $expense)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-medium text-gray-900">{{ $expense->category }}</h4>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">
                                {{ number_format($expense->percentage, 1) }}%
                            </span>
                        </div>
                        <p class="text-2xl font-bold text-gray-800 mb-2">${{ number_format($expense->total, 2) }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $expense->percentage }}%"></div>
                        </div>
                        
                        @php
                            $budget = $budgets->firstWhere('category', $expense->category);
                            $remaining = $budget ? $budget->amount - $expense->total : null;
                            $isOverBudget = $budget && $expense->total > $budget->amount;
                        @endphp
                        
                        @if($budget)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-500">Budget:</span>
                                    <span class="font-medium">${{ number_format($budget->amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Remaining:</span>
                                    <span class="font-medium {{ $isOverBudget ? 'text-red-600' : 'text-green-600' }}">
                                        ${{ number_format($remaining, 2) }}
                                        @if($isOverBudget)
                                            <span class="text-red-500">(Over Budget)</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
