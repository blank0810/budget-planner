<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Income Details') }}
            </h2>
            <a href="{{ route('incomes.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Incomes
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
                                    {{ $income->source }}
                                </h3>
                                @if($income->is_recurring)
                                    <span class="ml-3 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($income->recurring_interval) }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                <span>Added on {{ $income->created_at->format('M d, Y') }}</span>
                                <span class="mx-2">•</span>
                                <span>Updated {{ $income->updated_at->diffForHumans() }}</span>
                            </p>
                        </div>
                        <div class="mt-4 flex space-x-3 md:mt-0">
                            <a href="{{ route('incomes.edit', $income) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('incomes.destroy', $income) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this income entry?')">
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
                                    <dd class="mt-1 text-2xl font-semibold text-green-600">+{{ config('app.currency') }}{{ number_format($income->amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $income->date->format('F j, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="mt-1">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $income->category ?: 'Uncategorized' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Source</dt>
                                    <dd class="mt-1 text-sm text-gray-900 capitalize">
                                        {{ $income->source }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Notes Card -->
                    @if($income->notes)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                            <div class="px-6 py-5 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Notes</h3>
                            </div>
                            <div class="px-6 py-5">
                                <p class="text-gray-700 whitespace-pre-line">{{ $income->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Budget Card -->
                    {{-- @if($income->budget)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Budget</h3>
                        </div>
                        <div class="px-6 py-5">
                            @php
                                $budget = $income->budget;
                                $totalEarned = $budget->getTotalEarned();
                                $remaining = $budget->amount - $totalEarned;
                                $percentageUsed = $budget->amount > 0 ? ($totalEarned / $budget->amount) * 100 : 0;
                                $isExceeded = $totalEarned > $budget->amount;
                            @endphp
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <span class="px-2.5 py-0.5 inline-flex items-center text-sm font-medium rounded-full {{ $isExceeded ? 'bg-green-100 text-green-800' : 'bg-green-100 text-green-800' }}">
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
                                        <span class="font-medium {{ $isExceeded ? 'text-green-600' : 'text-gray-700' }}">
                                            {{ number_format($percentageUsed, 0) }}% used
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        @php
                                            $progressWidth = min(100, max(0, $percentageUsed));
                                            $progressColor = $isExceeded ? 'bg-green-500' : 'bg-green-500';
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
                                        <div class="text-xs text-gray-500 font-medium">Earned</div>
                                        <div class="font-semibold {{ $isExceeded ? 'text-green-600' : '' }}">
                                            {{ config('app.currency') }}{{ number_format($totalEarned, 2) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 font-medium">Remaining</div>
                                        <div class="font-semibold {{ $remaining < 0 ? 'text-green-600' : 'text-green-600' }}">
                                            {{ config('app.currency') }}{{ number_format($remaining, 2) }}
                                        </div>
                                    </div>
                                </div>
                                
                                @if($isExceeded)
                                    <div class="mt-3 p-3 bg-green-50 rounded-lg text-sm text-green-700 flex items-start">
                                        <svg class="h-5 w-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span>This income has exceeded the budget target</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                            <div class="px-6 py-5 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Budget</h3>
                            </div>
                            <div class="px-6 py-5">
                                <div class="text-center py-4">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No budget assigned</h3>
                                    <p class="mt-1 text-sm text-gray-500">This income isn't associated with any budget.</p>
                                </div>
                            </div>
                        </div>
                    @endif --}}

                    <!-- Additional Information Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @if($income->is_recurring)
                                <div class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-gray-900">Recurring Income</h4>
                                            <p class="text-sm text-gray-500">
                                                {{ ucfirst($income->recurring_interval) }} 
                                                @if($income->next_recurring_date)
                                                    • Next on {{ $income->next_recurring_date->format('M j, Y') }}
                                                @endif
                                            </p>
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
                <a href="{{ route('incomes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Incomes
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
