<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">{{ $reportData['title'] }}</h2>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Income -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Income</p>
                    <p class="text-2xl font-semibold text-gray-800">
                        ${{ number_format(array_sum($reportData['incomes']), 2) }}
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Total Expenses -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Expenses</p>
                    <p class="text-2xl font-semibold text-gray-800">
                        ${{ number_format(array_sum($reportData['expenses']), 2) }}
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Net Savings -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Net Savings</p>
                    @php
                        $totalIncome = array_sum($reportData['incomes']);
                        $totalExpense = array_sum($reportData['expenses']);
                        $netSavings = $totalIncome - $totalExpense;
                        $savingsRate = $totalIncome > 0 ? ($netSavings / $totalIncome) * 100 : 0;
                    @endphp
                    <p class="text-2xl font-semibold {{ $netSavings >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $netSavings >= 0 ? '+' : '' }}${{ number_format($netSavings, 2) }}
                    </p>
                    <p class="text-sm {{ $netSavings >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($savingsRate, 1) }}% of income
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-800">Income vs Expenses</h3>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                    <span class="text-sm text-gray-600">Income</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-orange-500 mr-2"></div>
                    <span class="text-sm text-gray-600">Expenses</span>
                </div>
            </div>
        </div>
        <div class="h-80">
            <canvas id="income-vs-expense-chart"></canvas>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Detailed Breakdown</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $reportData['is_monthly'] ? 'Month' : 'Year' }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Income</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Expenses</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Savings</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Savings Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $periods = $reportData['is_monthly'] 
                            ? array_keys($reportData['incomes'])
                            : array_unique(array_merge(array_keys($reportData['incomes']), array_keys($reportData['expenses'])));
                        rsort($periods);
                    @endphp
                    
                    @foreach($periods as $period)
                        @php
                            $income = $reportData['incomes'][$period] ?? 0;
                            $expense = $reportData['expenses'][$period] ?? 0;
                            $savings = $income - $expense;
                            $savingsRate = $income > 0 ? ($savings / $income) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $period }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                ${{ number_format($income, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                ${{ number_format($expense, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium {{ $savings >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $savings >= 0 ? '+' : '' }}${{ number_format($savings, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $savings >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($savingsRate, 1) }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                            ${{ number_format(array_sum($reportData['incomes']), 2) }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                            ${{ number_format(array_sum($reportData['expenses']), 2) }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium {{ $netSavings >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $netSavings >= 0 ? '+' : '' }}${{ number_format($netSavings, 2) }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium {{ $netSavings >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($savingsRate, 1) }}%
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- Insights -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Top Income Sources -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Top Income Sources</h3>
            
            @if(count($topIncomeSources ?? []) > 0)
                <div class="space-y-4">
                    @foreach($topIncomeSources as $source)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $source->source }}</span>
                                <span class="font-medium text-gray-900">${{ number_format($source->total, 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($source->total / max($totalIncome, 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No income data available.</p>
            @endif
        </div>
        
        <!-- Top Expense Categories -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Top Expense Categories</h3>
            
            @if(count($topExpenseCategories ?? []) > 0)
                <div class="space-y-4">
                    @foreach($topExpenseCategories as $category)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $category->category }}</span>
                                <span class="font-medium text-gray-900">${{ number_format($category->total, 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ ($category->total / max($totalExpense, 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No expense data available.</p>
            @endif
        </div>
    </div>
</div>
