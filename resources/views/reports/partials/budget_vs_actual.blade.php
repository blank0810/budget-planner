<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">{{ $reportData['title'] }}</h2>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Budgeted -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-indigo-100 text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500">Total Budgeted</p>
                    <p class="text-lg font-semibold text-gray-800">${{ number_format($reportData['total_budgeted'], 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Spent -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 {{ $reportData['total_spent'] > $reportData['total_budgeted'] ? 'border-red-500' : 'border-green-500' }}">
            <div class="flex items-center">
                <div class="p-2 rounded-full {{ $reportData['total_spent'] > $reportData['total_budgeted'] ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500">Total Spent</p>
                    <p class="text-lg font-semibold {{ $reportData['total_spent'] > $reportData['total_budgeted'] ? 'text-red-600' : 'text-green-600' }}">
                        ${{ number_format($reportData['total_spent'], 2) }}
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Remaining -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 {{ $reportData['total_remaining'] < 0 ? 'border-red-500' : 'border-blue-500' }}">
            <div class="flex items-center">
                <div class="p-2 rounded-full {{ $reportData['total_remaining'] < 0 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500">Remaining</p>
                    <p class="text-lg font-semibold {{ $reportData['total_remaining'] < 0 ? 'text-red-600' : 'text-blue-600' }}">
                        ${{ number_format(abs($reportData['total_remaining']), 2) }}
                        @if($reportData['total_remaining'] < 0)
                            <span class="text-xs">(Over Budget)</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Utilization -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 {{ $reportData['total_utilization'] > 100 ? 'border-red-500' : 'border-green-500' }}">
            <div class="flex items-center">
                <div class="p-2 rounded-full {{ $reportData['total_utilization'] > 100 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-500">Budget Utilization</p>
                    <p class="text-lg font-semibold {{ $reportData['total_utilization'] > 100 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($reportData['total_utilization'], 1) }}%
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Budget vs Actual Spending</h3>
        <div class="h-96">
            <canvas id="budget-vs-actual-chart"></canvas>
        </div>
    </div>
    
    <!-- Budget Categories -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-800">Budget Categories</h3>
                <div class="relative">
                    <input type="text" id="category-search" placeholder="Search categories..." class="pl-8 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Budgeted</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Spent</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Utilization</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="category-table-body">
                    @foreach($reportData['budgets'] as $budget)
                        <tr class="category-row hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-700 font-medium">{{ strtoupper(substr($budget['category'], 0, 2)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $budget['category'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                ${{ number_format($budget['budgeted'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $budget['spent'] > $budget['budgeted'] ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                ${{ number_format($budget['spent'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $budget['remaining'] < 0 ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                ${{ number_format(abs($budget['remaining']), 2) }}
                                @if($budget['remaining'] < 0)
                                    <span class="text-red-500 text-xs">(Over)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end">
                                    <div class="w-24 mr-2">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            @php
                                                $utilization = min(100, ($budget['spent'] / max($budget['budgeted'], 1)) * 100);
                                                $bgColor = $utilization > 100 ? 'bg-red-500' : 'bg-green-500';
                                            @endphp
                                            <div class="h-2.5 rounded-full {{ $bgColor }}" style="width: {{ min(100, $utilization) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium {{ $utilization > 100 ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ number_format($utilization, 0) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                            ${{ number_format($reportData['total_budgeted'], 2) }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium {{ $reportData['total_spent'] > $reportData['total_budgeted'] ? 'text-red-600' : 'text-gray-900' }}">
                            ${{ number_format($reportData['total_spent'], 2) }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium {{ $reportData['total_remaining'] < 0 ? 'text-red-600' : 'text-gray-900' }}">
                            ${{ number_format(abs($reportData['total_remaining']), 2) }}
                            @if($reportData['total_remaining'] < 0)
                                <span class="text-red-500">(Over)</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium {{ $reportData['total_utilization'] > 100 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ number_format($reportData['total_utilization'], 1) }}%
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Categories Over Budget -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Categories Over Budget</h3>
            
            @php
                $overBudget = array_filter($reportData['budgets'], function($budget) {
                    return $budget['spent'] > $budget['budgeted'];
                });
            @endphp
            
            @if(count($overBudget) > 0)
                <div class="space-y-4">
                    @foreach($overBudget as $budget)
                        @php
                            $overAmount = $budget['spent'] - $budget['budgeted'];
                            $utilization = ($budget['spent'] / $budget['budgeted']) * 100;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $budget['category'] }}</span>
                                <span class="font-medium text-red-600">${{ number_format($overAmount, 2) }} over</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Budget: ${{ number_format($budget['budgeted'], 2) }}</span>
                                <span>Spent: ${{ number_format($budget['spent'], 2) }} ({{ number_format($utilization, 0) }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">All categories are within budget!</p>
                </div>
            @endif
        </div>
        
        <!-- Categories With Most Remaining -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Categories With Most Remaining</h3>
            
            @php
                $withRemaining = array_filter($reportData['budgets'], function($budget) {
                    return $budget['remaining'] > 0;
                });
                
                // Sort by remaining amount (descending) and take top 5
                usort($withRemaining, function($a, $b) {
                    return $b['remaining'] <=> $a['remaining'];
                });
                $topRemaining = array_slice($withRemaining, 0, 5);
            @endphp
            
            @if(count($topRemaining) > 0)
                <div class="space-y-4">
                    @foreach($topRemaining as $budget)
                        @php
                            $utilization = ($budget['spent'] / $budget['budgeted']) * 100;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $budget['category'] }}</span>
                                <span class="font-medium text-green-600">${{ number_format($budget['remaining'], 2) }} left</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $utilization }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Budget: ${{ number_format($budget['budgeted'], 2) }}</span>
                                <span>Spent: ${{ number_format($budget['spent'], 2) }} ({{ number_format($utilization, 0) }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">No categories with remaining budget found.</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Budget Tips -->
    <div class="bg-indigo-50 rounded-lg p-6 border border-indigo-100">
        <h3 class="text-lg font-medium text-indigo-800 mb-3">Budgeting Tips</h3>
        <ul class="space-y-2 text-sm text-indigo-700">
            @if($reportData['total_utilization'] > 100)
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>You've exceeded your total budget by <strong>${{ number_format(abs($reportData['total_remaining']), 2) }}</strong>. Consider reviewing your spending in over-budget categories.</span>
                </li>
            @else
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Great job! You're within your total budget for this period.</span>
                </li>
            @endif
            
            @if(count($overBudget) > 0)
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>You have <strong>{{ count($overBudget) }} categor{{ count($overBudget) === 1 ? 'y' : 'ies' }} over budget</strong>. Consider adjusting your spending or reallocating funds from categories with remaining budget.</span>
                </li>
            @endif
            
            @if(count($topRemaining) > 0)
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>You have unused funds in <strong>{{ count($topRemaining) }} categor{{ count($topRemaining) === 1 ? 'y' : 'ies' }}</strong>. Consider reallocating these funds to other categories or adding them to your savings.</span>
                </li>
            @endif
            
            <li class="flex items-start">
                <svg class="h-5 w-5 text-indigo-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Regularly reviewing your budget helps you stay on track with your financial goals.</span>
            </li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
    // Category search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('category-search');
        const categoryRows = document.querySelectorAll('.category-row');
        
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                
                categoryRows.forEach(row => {
                    const categoryName = row.querySelector('td:first-child .text-sm').textContent.toLowerCase();
                    if (categoryName.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush
