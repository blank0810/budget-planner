<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Expense Details') }}
            </h2>
            <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Expenses
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <h3 class="text-2xl font-bold text-gray-900">
                                    {{ $expense->description }}
                                </h3>
                                @if($expense->is_recurring)
                                    <span class="ml-3 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($expense->recurring_interval) }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                <span>Added on {{ $expense->created_at->format('M d, Y') }}</span>
                                <span class="mx-2">•</span>
                                <span>Updated {{ $expense->updated_at->diffForHumans() }}</span>
                            </p>
                        </div>
                        <div class="mt-4 flex space-x-3 md:mt-0">
                            <a href="{{ route('expenses.edit', $expense) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense entry?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Amount & Category Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Transaction Details</h3>
                        </div>
                        <div class="px-6 py-5">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-red-600">-{{ config('app.currency') }}{{ number_format($expense->amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $expense->date->format('F j, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="mt-1">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $expense->category ?: 'Uncategorized' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                    <dd class="mt-1 text-sm text-gray-900 capitalize">
                                        {{ str_replace('_', ' ', $expense->payment_method) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Notes Card -->
                    @if($expense->notes)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                            <div class="px-6 py-5 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Notes</h3>
                            </div>
                            <div class="px-6 py-5">
                                <p class="text-gray-700 whitespace-pre-line">{{ $expense->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Budget Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Budget</h3>
                        </div>
                        <div class="px-6 py-5">
                            @if($expense->budget)
                                @php
                                    $budget = $expense->budget;
                                    $totalSpent = $budget->getTotalSpent();
                                    $remaining = $budget->amount - $totalSpent;
                                    $percentageUsed = $budget->amount > 0 ? ($totalSpent / $budget->amount) * 100 : 0;
                                    $isExceeded = $expense->isBudgetExceeded();
                                @endphp
                                
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <span class="px-2.5 py-1 inline-flex items-center text-sm font-medium rounded-full {{ $isExceeded ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $budget->budget_name }}
                                            @if($isExceeded)
                                                <svg class="ml-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </span>
                                        <span class="ml-2 text-sm text-gray-500">
                                            {{ $budget->month }}/{{ $budget->year }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="font-medium">Progress</span>
                                            <span class="font-medium {{ $isExceeded ? 'text-red-600' : 'text-gray-700' }}">
                                                {{ number_format($percentageUsed, 0) }}% used
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            @php
                                                $progressWidth = min(100, max(0, $percentageUsed));
                                                $progressColor = $isExceeded ? 'bg-red-500' : 'bg-blue-500';
                                            @endphp
                                            <div class="h-2.5 rounded-full {{ $progressColor }}" style="width: {{ $progressWidth }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-3 gap-4 text-center text-sm mt-4">
                                        <div>
                                            <div class="text-xs text-gray-500 font-medium">Budgeted</div>
                                            <div class="font-semibold">{{ config('app.currency') }}{{ number_format($budget->amount, 2) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 font-medium">Spent</div>
                                            <div class="font-semibold {{ $isExceeded ? 'text-red-600' : '' }}">
                                                {{ config('app.currency') }}{{ number_format($totalSpent, 2) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 font-medium">Remaining</div>
                                            <div class="font-semibold {{ $remaining < 0 ? 'text-red-600' : 'text-green-600' }}">
                                                {{ config('app.currency') }}{{ number_format($remaining, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($isExceeded)
                                        <div class="mt-3 p-3 bg-red-50 rounded-lg text-sm text-red-700 flex items-start">
                                            <svg class="h-5 w-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span>This expense is part of an exceeded budget</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No budget assigned</h3>
                                    <p class="mt-1 text-sm text-gray-500">This expense isn't associated with any budget.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Receipt & Recurring Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @if($expense->receipt_path)
                                <div class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-gray-900">Receipt</h4>
                                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-500">
                                                View receipt
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($expense->is_recurring && $expense->next_recurring_date)
                                <div class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-gray-900">Next Occurrence</h4>
                                            <p class="text-sm text-gray-500">{{ $expense->next_recurring_date->format('F j, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Navigation -->
            <div class="mt-8 flex justify-end">
                <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Expenses
                </a>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
