<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Income Management') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">Track and manage your income streams</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="incomeIndex()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Actions Bar -->
            <div class="mb-8 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl shadow-lg p-6 border border-emerald-100">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Your Income History</h3>
                            <p class="text-sm text-gray-600">View and manage your income entries</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-3 w-full lg:w-auto">
                        <!-- View Mode Toggle -->
                        <div class="inline-flex rounded-xl shadow-md overflow-hidden border border-gray-200" role="group">
                            @php
                                $listQuery = array_merge(request()->query(), ['view' => 'list']);
                                $monthlyQuery = array_merge(request()->query(), ['view' => 'monthly']);
                            @endphp
                            <a href="{{ route('incomes.index', $listQuery) }}" 
                               class="group px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ $viewMode === 'list' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                    <span class="hidden sm:inline">List View</span>
                                </div>
                            </a>
                            <a href="{{ route('incomes.index', $monthlyQuery) }}" 
                               class="group px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ $viewMode === 'monthly' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Monthly</span>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Date Filters -->
                        <form method="GET" class="flex flex-wrap gap-2">
                            @if($viewMode === 'list')
                                <!-- Month Filter (only for list view) -->
                                <div class="relative">
                                    <select name="month" onchange="this.form.submit()"
                                        class="appearance-none bg-white border border-gray-300 rounded-lg pl-4 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">All Months</option>
                                        @foreach($months as $key => $name)
                                            <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Year Filter -->
                            <div class="relative">
                                <select name="year" onchange="this.form.submit()"
                                    class="appearance-none bg-white border border-gray-300 rounded-lg pl-4 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    @forelse($availableYears as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @empty
                                        <option value="{{ now()->year }}">{{ now()->year }}</option>
                                    @endforelse
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Hidden fields to preserve view mode -->
                            @if(request()->has('view'))
                                <input type="hidden" name="view" value="{{ $viewMode }}">
                            @endif
                        </form>
                        
                        <!-- Add New Income Button -->
                        <a href="{{ route('incomes.create') }}" 
                           class="group inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Add Income</span>
                        </a>
                    </div>
                </div>
            </div>
                    
            <!-- Loading State -->
            <div x-show="isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="flex flex-col items-center justify-center py-16">
                <div class="relative">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-200"></div>
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-emerald-600 absolute top-0 left-0"></div>
                </div>
                <p class="mt-4 text-gray-600 font-medium">Loading your income data...</p>
            </div>

            @if(($viewMode === 'list' && $incomes->isEmpty()) || ($viewMode === 'monthly' && $monthlyIncomes->isEmpty()))
                <!-- Empty State -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-xl overflow-hidden border border-gray-100"
                     x-data="{ show: false }"
                     x-init="setTimeout(() => show = true, 100)"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 mb-6">
                            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Income Entries Found</h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">
                            @if($viewMode === 'monthly')
                                No income data available for {{ $selectedYear }}. Start tracking your income to see insights.
                            @else
                                @if($selectedMonth)
                                    No income entries found for {{ $months[$selectedMonth] ?? 'selected month' }} {{ $selectedYear }}. Try a different month or year.
                                @else
                                    No income entries found for {{ $selectedYear }}. Add your first income entry to get started.
                                @endif
                            @endif
                        </p>
                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ route('incomes.create') }}" 
                               class="group inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Add Your First Income</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                @if($viewMode === 'list')
                    <!-- Modern List View -->
                    <div class="space-y-3" x-data="{ hoveredRow: null }">
                        @foreach($incomes as $index => $income)
                            <div x-data="{ show: false }" 
                                 x-init="setTimeout(() => show = true, {{ $index * 50 }})"
                                 x-show="show"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-x-4"
                                 x-transition:enter-end="opacity-100 translate-x-0"
                                 @mouseenter="hoveredRow = {{ $index }}"
                                 @mouseleave="hoveredRow = null"
                                 class="group bg-white rounded-2xl shadow-md hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                                
                                <div class="p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                        <!-- Left Section: Date & Source -->
                                        <div class="flex items-start gap-4 flex-1">
                                            <div class="flex-shrink-0">
                                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center">
                                                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $income->source }}</h3>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-sm text-gray-500">{{ $income->date->format('M d, Y') }}</span>
                                                    @if($income->is_recurring)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                            {{ ucfirst($income->recurring_interval) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Middle Section: Category & Amount -->
                                        <div class="flex items-center gap-6">
                                            <div class="text-center">
                                                <p class="text-xs text-gray-500 mb-1">Category</p>
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    {{ $income->category }}
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500 mb-1">Amount</p>
                                                <p class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                                    ${{ number_format($income->amount, 2) }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Right Section: Actions -->
                                        <div class="flex items-center gap-2 lg:opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <a href="{{ route('incomes.show', $income) }}" 
                                               class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors duration-200"
                                               title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('incomes.edit', $income) }}" 
                                               class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors duration-200"
                                               title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('incomes.destroy', $income) }}" method="POST" class="inline"
                                                  x-data="{ confirmDelete: false }"
                                                  @submit.prevent="if(confirmDelete || confirm('Are you sure you want to delete this income entry?')) $el.submit()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors duration-200"
                                                        title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(method_exists($incomes, 'links'))
                        <div class="mt-8">
                            {{ $incomes->links() }}
                        </div>
                    @endif
                @else
                    <!-- Monthly Card View -->
                    @include('incomes.partials.monthly_cards', ['monthlyIncomes' => $monthlyIncomes])
                @endif
            @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function incomeIndex() {
            return {
                isLoading: false
            };
        }
    </script>
    @endpush
</x-app-layout>
