<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5">
            <!-- Page Heading -->
            <header class="mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ __('Dashboard') }}
                </h2>
            </header>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <!-- Monthly Income -->
                <div class="bg-white overflow-hidden shadow-sm rounded">
                    <div class="p-3 sm:p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Monthly Income
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            ${{ number_format($monthlyIncome, 2) }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Expenses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Monthly Expenses
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            ${{ number_format($monthlyExpenses, 2) }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Balance -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 {{ $balance >= 0 ? 'bg-green-500' : 'bg-red-500' }} rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Balance
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ${{ number_format($balance, 2) }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Summary -->
            <div class="bg-white overflow-hidden shadow-sm rounded mb-4">
                <div class="p-4">
                    <h3 class="font-medium text-gray-900 mb-3">Budget Summary</h3>
                    @if($budgets->isNotEmpty())
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Total Budgeted: ${{ number_format($budgetSummary->total_budgeted, 2) }}</span>
                            <span class="text-sm font-medium {{ $budgetSummary->status === 'over' ? 'text-red-600' : 'text-gray-700' }}">
                                Spent: ${{ number_format($budgetSummary->total_spent, 2) }} ({{ number_format($budgetSummary->utilization, 1) }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ 
                                        $budgetSummary->status === 'over' ? 'bg-red-600' : 
                                        ($budgetSummary->status === 'warning' ? 'bg-yellow-500' : 'bg-green-500') 
                                    }}" style="width: {{ min(100, $budgetSummary->utilization) }}%"></div>
                        </div>
                    </div>

                    <h4 class="text-md font-medium text-gray-900 mb-3">By Category</h4>
                    <div class="space-y-4">
                        @foreach($budgets as $budget)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium">{{ $budget->category }}</span>
                                <span class="font-medium {{ $budget->status === 'over' ? 'text-red-600' : 'text-gray-700' }}">
                                    ${{ number_format($budget->spent, 2) }} / ${{ number_format($budget->budgeted, 2) }}
                                    ({{ number_format($budget->utilization, 1) }}%)
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ 
                                                $budget->status === 'over' ? 'bg-red-600' : 
                                                ($budget->status === 'warning' ? 'bg-yellow-500' : 'bg-green-500') 
                                            }}" style="width: {{ min(100, $budget->utilization) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No active budgets</h3>
                        <p class="mt-1 text-sm text-gray-500">Start by creating a budget for the current month.</p>
                        <div class="mt-4">
                            <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Create Budget
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Recent Transactions -->
                <div class="bg-white overflow-hidden shadow-sm rounded">
                    <div class="p-4">
                        <h3 class="font-medium text-gray-900 mb-3">Recent Transactions</h3>
                        @if($recentTransactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->source ?? $transaction->description }}
                                            <div class="text-xs text-gray-500">{{ $transaction->category }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $transaction instanceof \App\Models\Income ? 'text-green-600' : 'text-red-600' }}">
                                            ${{ number_format($transaction->amount, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-gray-500 text-center py-4">No transactions yet. Add some income or expenses to get started.</p>
                        @endif
                    </div>
                </div>

                <!-- Expenses by Category -->
                <div class="bg-white overflow-hidden shadow-sm rounded">
                    <div class="p-4">
                        <h3 class="font-medium text-gray-900 mb-3">Expenses by Category</h3>
                        @php
                        $maxExpense = $expensesByCategory->isNotEmpty() ? $expensesByCategory->max('total') : 0;
                        @endphp
                        @if($expensesByCategory->count() > 0)
                        <div class="space-y-4">
                            @foreach($expensesByCategory as $category)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-700">{{ $category->category }}</span>
                                    <span class="text-gray-900">${{ number_format($category->total, 2) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    @php
                                    $percentage = $maxExpense > 0 ? ($category->total / $maxExpense) * 100 : 0;
                                    $percentageFormatted = number_format($percentage, 2);
                                    @endphp
                                    <div class="progress-bar" style="width: {{ $percentageFormatted }}%;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-center py-4">No expense data available for this month.</p>
                        @endif
                    </div>
                </div>
            </div>
</x-app-layout>