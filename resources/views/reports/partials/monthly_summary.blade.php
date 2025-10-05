<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">{{ $reportData['title'] }}</h2>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Income -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Income</p>
                    <p class="text-2xl font-semibold text-gray-800">${{ number_format($reportData['incomes'], 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Expenses -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Expenses</p>
                    <p class="text-2xl font-semibold text-gray-800">${{ number_format($reportData['expenses'], 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Savings -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Savings</p>
                    <p class="text-2xl font-semibold text-gray-800">${{ number_format($reportData['savings'], 2) }}</p>
                    <p class="text-sm {{ $reportData['savings'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($reportData['savings_rate'], 1) }}% of income
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Categories -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Top Expense Categories</h3>
        
        @if(count($reportData['top_categories']) > 0)
            <div class="space-y-4">
                @foreach($reportData['top_categories'] as $category => $amount)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">{{ $category }}</span>
                            <span class="font-medium text-gray-900">${{ number_format($amount, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($amount / max($reportData['expenses'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No expense data available for this period.</p>
        @endif
    </div>
    
    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-800">Recent Transactions</h3>
            <a href="{{ route('transactions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
        </div>
        
        @if(count($recentTransactions ?? []) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentTransactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $transaction->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $transaction->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->amount >= 0 ? '+' : '' }}${{ number_format($transaction->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No recent transactions found.</p>
        @endif
    </div>
</div>
