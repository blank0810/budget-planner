@inject('str', 'Illuminate\\Support\\Str')

<div x-data="{
        showModal: false,
        activeMonth: null,
        sources: [],
        getCategories() {
            if (!this.activeMonth?.incomes?.length) return [];
            
            // Group incomes by category
            const categories = {};
            this.activeMonth.incomes.forEach(income => {
                const category = income.category || 'Uncategorized';
                if (!categories[category]) {
                    categories[category] = 0;
                }
                categories[category] += parseFloat(income.amount) || 0;
            });
            
            // Convert to array of {category, total_amount} objects
            return Object.entries(categories).map(([category, total_amount]) => ({
                category,
                total_amount: parseFloat(total_amount.toFixed(2))
            }));
        },
        getSources() {
            if (!this.activeMonth?.incomes?.length) return [];
            
            // Group incomes by source
            const sources = {};
            this.activeMonth.incomes.forEach(income => {
                const sourceName = income.source || 'Other';
                if (!sources[sourceName]) {
                    sources[sourceName] = {
                        name: sourceName,
                        amount: 0,
                        count: 0
                    };
                }
                sources[sourceName].amount += parseFloat(income.amount) || 0;
                sources[sourceName].count++;
            });
            
            // Convert to array and sort by amount (descending)
            return Object.values(sources).sort((a, b) => b.amount - a.amount);
        },
        openModal(monthData) {
            this.activeMonth = typeof monthData === 'string' ? JSON.parse(monthData) : monthData;
            this.sources = this.getSources();
            this.showModal = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.showModal = false;
            this.activeMonth = null;
            this.sources = [];
            document.body.classList.remove('overflow-hidden');
        }
    }" @open-modal.window="openModal($event.detail)">

    <!-- Year Filter -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('incomes.index') }}" class="space-y-4">
                <input type="hidden" name="view" value="monthly">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                        <select id="year" name="year"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm rounded-md">
                            @foreach($availableYears as $availableYear)
                                <option value="{{ $availableYear }}" {{ $selectedYear == $availableYear ? 'selected' : '' }}>
                                    {{ $availableYear }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Apply Filters
                        </button>
                        <a href="{{ route('incomes.index', ['view' => 'monthly']) }}"
                            class="ml-2 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start auto-rows-auto">
        @foreach($monthlyIncomes as $index => $monthly)
            <div x-data="{ show: false }" x-init="setTimeout(() => show = true, {{ $index * 100 }})" x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="group bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 transform cursor-pointer"
                @click="$dispatch('open-modal', {{ json_encode($monthly) }})">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-white flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">{{ $monthly['month'] }}</h3>
                            <p class="text-emerald-100 text-sm mt-0.5">
                                {{ $monthly['transaction_count'] }}
                                {{ Str::plural('transaction', $monthly['transaction_count']) }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">${{ number_format($monthly['total_amount'], 2) }}</p>
                        <p class="text-emerald-100 text-sm mt-1">
                            Avg: ${{ number_format($monthly['total_amount'] / max(1, $monthly['transaction_count']), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- MODAL -->
    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition" x-cloak
        @click.self="closeModal()" @keydown.escape.window="closeModal()">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden"
            x-show="showModal" x-transition>
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold" x-text="activeMonth ? activeMonth.month : ''"></h2>
                    <p class="text-emerald-100 text-sm"
                        x-text="activeMonth ? activeMonth.transaction_count + ' transaction(s)' : ''"></p>
                </div>
                <button @click="closeModal()"
                    class="p-2 rounded-full focus:outline-none focus:ring-2 focus:ring-white/30 transition">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 max-h-[70vh] overflow-y-auto space-y-6">
                <!-- Debug Section -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Debug Info</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p class="text-xs font-mono whitespace-pre-wrap"
                                    x-text="JSON.stringify(activeMonth, null, 2)"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Total Income Card -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-4 border border-emerald-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Income</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900"
                                    x-text="'₱' + (activeMonth ? Number(activeMonth.total_amount).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')">
                                </p>
                            </div>
                            <div class="p-2 rounded-lg bg-emerald-100 text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-xs text-gray-500">
                                <span class="font-medium text-emerald-600"
                                    x-text="activeMonth ? activeMonth.transaction_count : '0'"></span>
                                {{ Str::plural('transaction', 2) }}
                            </p>
                        </div>
                    </div>

                    <!-- Average Income Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-4 border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Avg. Income</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900"
                                    x-text="'₱' + (activeMonth && activeMonth.transaction_count > 0 ? 
                                       (activeMonth.total_amount / activeMonth.transaction_count).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')">
                                </p>
                            </div>
                            <div class="p-2 rounded-lg bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-xs text-gray-500">
                                per transaction
                            </p>
                        </div>
                    </div>

                    <!-- Largest Income Card -->
                    <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-4 border border-amber-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Largest Income</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900"
                                    x-text="activeMonth && activeMonth.largest_income ? 
                                       '₱' + activeMonth.largest_income.amount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '₱0.00'">
                                </p>
                            </div>
                            <div class="p-2 rounded-lg bg-amber-100 text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-xs text-gray-500 truncate"
                                x-text="activeMonth && activeMonth.largest_income ? 
                                   activeMonth.largest_income.source + ' on ' + new Date(activeMonth.largest_income.date).toLocaleDateString('en-US', {month: 'short', day: 'numeric'}) : 'No transactions'">
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Income Breakdown Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Income Breakdown by Source
                        </h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Income Chart -->
                        <template x-if="activeMonth?.incomes?.length">
                            <div class="flex items-center justify-center h-64 relative">
                                <div class="w-48 h-48 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                    <div x-data="{
                                        chart: null,
                                        activeMonth: null,
                                        
                                        init() {
                                            let lastData = null;
                                            
                                            // Single render function that handles both initial and subsequent renders
                                            const checkAndRender = () => {
                                                this.activeMonth = this.getParentData('activeMonth');
                                                if (!this.activeMonth?.incomes?.length) return;
                                                
                                                const currentData = JSON.stringify(this.getCategoriesData());
                                                if (currentData !== lastData) {
                                                    lastData = currentData;
                                                    this.renderChart(this.getCategoriesData());
                                                }
                                            };
                                            
                                            // Initial check
                                            checkAndRender();
                                            
                                            // Poll for changes every second
                                            this.pollingInterval = setInterval(checkAndRender, 1000);
                                            
                                            // Clean up on destroy
                                            this.$el.addEventListener('alpine:initialized', () => {
                                                this.$el.addEventListener('alpine:destroy', () => {
                                                    if (this.pollingInterval) {
                                                        clearInterval(this.pollingInterval);
                                                    }
                                                });
                                            });
                                        },
                                        
                                        getParentData(key) {
                                            let parent = this.$el.closest('[x-data]');
                                            while (parent) {
                                                if (parent.__x && parent.__x.$data && parent.__x.$data[key] !== undefined) {
                                                    return parent.__x.$data[key];
                                                }
                                                parent = parent.parentElement;
                                            }
                                            return null;
                                        },
                                        
                                        getCategoriesData() {
                                            if (!this.activeMonth?.incomes?.length) return [];
                                            
                                            // Group incomes by category
                                            const categories = {};
                                            this.activeMonth.incomes.forEach(income => {
                                                const category = income.category || 'Uncategorized';
                                                if (!categories[category]) categories[category] = 0;
                                                categories[category] += parseFloat(income.amount) || 0;
                                            });
                                            
                                            return Object.entries(categories).map(([category, total]) => ({
                                                category,
                                                total_amount: parseFloat(total.toFixed(2))
                                            }));
                                        },
                                        
                                        renderChart(categories) {
                                            const canvas = this.$refs.chartCanvas;
                                            if (!canvas) {
                                                console.log('Canvas element not found');
                                                return;
                                            }
                                            
                                            const ctx = canvas.getContext('2d');
                                            if (!ctx) {
                                                console.log('Could not get 2D context');
                                                return;
                                            }
                                            
                                            console.log('Rendering chart with categories:', categories);
                                            
                                            if (this.chart) {
                                                this.chart.destroy();
                                            }
                                            
                                            try {
                                                const colorPalette = [
                                                    '#10B981', '#0D9488', '#059669', '#047857',
                                                    '#0F766E', '#115E59', '#064E3B', '#134E4A'
                                                ];
                                                
                                                this.chart = new Chart(ctx, {
                                                    type: 'doughnut',
                                                    data: {
                                                        labels: categories.map(c => c.category || 'Uncategorized'),
                                                        datasets: [{
                                                            data: categories.map(c => c.total_amount),
                                                            backgroundColor: categories.map((_, i) => colorPalette[i % colorPalette.length]),
                                                            borderWidth: 0,
                                                            hoverOffset: 10
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        cutout: '70%',
                                                        animation: {
                                                            animateScale: true,
                                                            animateRotate: true
                                                        },
                                                        plugins: {
                                                            legend: { display: false },
                                                            tooltip: {
                                                                callbacks: {
                                                                    label: (context) => {
                                                                        const label = context.label || '';
                                                                        const value = context.parsed || 0;
                                                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                                        const percentage = Math.round((value / total) * 100);
                                                                        return `${label}: ₱${value.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} (${percentage}%)`;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                                
                                                console.log('Chart rendered successfully');
                                            } catch (error) {
                                                console.error('Error rendering chart:', error);
                                            }
                                        },
                                        
                                        destroy() {
                                            if (this.pollingInterval) {
                                                clearInterval(this.pollingInterval);
                                            }
                                            
                                            if (this.chart) {
                                                try { 
                                                    this.chart.destroy(); 
                                                } catch (e) {
                                                    console.error('Error destroying chart:', e);
                                                }
                                                this.chart = null;
                                            }
                                        }
                                    }" x-init="init()" class="relative w-full h-full">
                                        <canvas x-ref="chartCanvas" class="w-full h-full"></canvas>
                                        <div x-show="!activeMonth?.incomes?.length" 
                                             class="absolute inset-0 flex items-center justify-center">
                                            <p class="text-sm text-gray-500">No income data available</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <!-- Show message when no data -->
                        <div x-show="!activeMonth?.incomes?.length" class="col-span-2 flex items-center justify-center h-64">
                            <p class="text-sm text-gray-500">No income data available for this month</p>
                        </div>
                        <!-- Category List -->
                            <div class="space-y-3">
                                <template x-for="(item, index) in getCategories()" :key="index">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 rounded-full"
                                                :style="'background-color: ' + ['#10B981', '#0D9488', '#059669', '#047857', '#0F766E', '#115E59', '#064E3B', '#134E4A'][index % 8]">
                                            </div>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                                x-text="item.category || 'Uncategorized'"></span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white"
                                                x-text="'₱' + (parseFloat(item.total_amount) || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                            </div>
                                            <div class="text-xs text-gray-500"
                                                x-text="Math.round((parseFloat(item.total_amount) / activeMonth.total_amount) * 100) + '% of total'">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <p x-show="!activeMonth?.incomes?.length" class="text-sm text-gray-500 dark:text-gray-400">
                            No income categories to display
                        </p>
                    </div>
                </div>

                <!-- Income Sources Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Income Sources</h4>
                    </div>
                    <div class="space-y-3">
                        <template x-if="sources && sources.length > 0">
                            <template x-for="(source, index) in sources" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="source.name"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="source.count + ' ' + (source.count === 1 ? 'transaction' : 'transactions')"></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="'₱' + source.amount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits:2})"></div>
                                        <div class="text-xs text-gray-500" x-text="Math.round((source.amount / activeMonth.total_amount) * 100) + '% of total'"></div>
                                    </div>
                                </div>
                            </template>
                        </template>
                        <p x-show="!sources || sources.length === 0" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                            No income sources data available
                        </p>
                    </div>
                </div>

                <!-- Transactions Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-[350px]">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Transactions</h4>
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <template x-if="activeMonth && activeMonth.incomes && activeMonth.incomes.length > 0">
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="(income, index) in activeMonth.incomes.slice(0, 10)" :key="income.id">
                                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                                        x-text="income.notes || income.source || 'Income'">
                                                    </p>
                                                    <div class="flex items-center mt-1 space-x-2 text-xs">
                                                        <span class="text-gray-500 dark:text-gray-400 whitespace-nowrap"
                                                            x-text="new Date(income.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })">
                                                        </span>
                                                        <span class="text-gray-300 dark:text-gray-600">•</span>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium whitespace-nowrap overflow-hidden overflow-ellipsis max-w-[120px] bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300"
                                                            x-text="income.category || 'Uncategorized'"
                                                            :title="income.category || 'Uncategorized'">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right ml-2">
                                                <p class="text-sm font-semibold text-emerald-600 whitespace-nowrap"
                                                    x-text="'₱' + Number(income.amount).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 capitalize whitespace-nowrap"
                                                    x-text="(income.source || '').toLowerCase()">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!activeMonth?.incomes?.length">
                            <div class="h-full flex items-center justify-center p-8 text-center">
                                <div>
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No transactions</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        There are no income transactions for this period.
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <template x-if="activeMonth && (!activeMonth.incomes || activeMonth.incomes.length === 0)">
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        No transactions for this month.
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    .overflow-hidden {
        overflow: hidden;
    }
</style>