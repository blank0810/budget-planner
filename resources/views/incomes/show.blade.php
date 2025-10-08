<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Income Details') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">View complete income information</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Card -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
                
                <!-- Header Section with Gradient -->
                <div class="relative bg-gradient-to-r from-emerald-500 to-teal-600 p-8 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                    
                    <div class="relative flex justify-between items-start">
                        <div class="flex items-start gap-4">
                            <div class="p-4 bg-white/20 backdrop-blur-sm rounded-2xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">{{ $income->source }}</h3>
                                <p class="mt-1 text-emerald-100 text-sm">
                                    Added on {{ $income->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="{{ route('incomes.edit', $income) }}" 
                               class="group p-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl transition-all duration-200 transform hover:scale-110"
                               title="Edit Income">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('incomes.destroy', $income) }}" method="POST" 
                                  x-data="{ confirmDelete: false }"
                                  @submit.prevent="if(confirmDelete || confirm('Are you sure you want to delete this income entry?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="group p-3 bg-white/20 hover:bg-red-500/80 backdrop-blur-sm rounded-xl transition-all duration-200 transform hover:scale-110"
                                        title="Delete Income">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Amount Card -->
                        <div class="group p-6 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-emerald-100 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-emerald-500 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Amount</h4>
                            </div>
                            <p class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                ${{ number_format($income->amount, 2) }}
                            </p>
                        </div>

                        <!-- Category Card -->
                        <div class="group p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-blue-500 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Category</h4>
                            </div>
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-base font-semibold bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700">
                                {{ $income->category }}
                            </span>
                        </div>

                        <!-- Date Card -->
                        <div class="group p-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border border-purple-100 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-purple-500 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Date</h4>
                            </div>
                            <p class="text-lg font-semibold text-gray-900">{{ $income->date->format('F j, Y') }}</p>
                        </div>

                        @if($income->is_recurring)
                            <!-- Recurring Card -->
                            <div class="group p-6 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-100 hover:shadow-lg transition-all duration-300">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-2 bg-amber-500 rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Recurring</h4>
                                </div>
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-base font-semibold bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700">
                                    {{ ucfirst($income->recurring_interval) }}
                                </span>
                                @if($income->next_recurring_date)
                                    <p class="mt-3 text-sm text-gray-600">
                                        <span class="font-medium">Next occurrence:</span> {{ $income->next_recurring_date->format('F j, Y') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($income->notes)
                        <!-- Notes Section -->
                        <div class="mt-6 p-6 bg-gradient-to-br from-gray-50 to-slate-50 rounded-2xl border border-gray-200">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2 bg-gray-500 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Notes</h4>
                            </div>
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $income->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <a href="{{ route('incomes.index') }}" 
                       class="group inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 shadow-md hover:shadow-lg transition-all duration-200">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>Back to Incomes</span>
                    </a>
                    
                    <a href="{{ route('incomes.edit', $income) }}" 
                       class="group inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit Income</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
