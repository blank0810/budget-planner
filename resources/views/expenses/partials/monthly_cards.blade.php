@inject('str', 'Illuminate\\Support\\Str')

<div x-data="{
        showModal: false,
        activeMonth: null,
        init() {
            // Add keyboard event listener for Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.showModal) {
                    this.closeModal();
                }
            });
        },
        openModal(monthData) {
            this.activeMonth = typeof monthData === 'string' ? JSON.parse(monthData) : monthData;
            this.showModal = true;
            document.body.classList.add('overflow-hidden');
            document.documentElement.style.overflow = 'hidden';
        },
        closeModal() {
            this.showModal = false;
            this.activeMonth = null;
            document.body.classList.remove('overflow-hidden');
            document.documentElement.style.overflow = '';
            this.$nextTick(() => {
                // Any additional cleanup if needed
            });
        }
    }" @open-modal.window="openModal($event.detail)">

    <!-- Year Filter -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('expenses.monthly') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                        <select id="year" name="year"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                            @foreach($availableYears as $availableYear)
                                <option value="{{ $availableYear }}" {{ $selectedYear == $availableYear ? 'selected' : '' }}>
                                    {{ $availableYear }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Apply Filters
                        </button>
                        <a href="{{ route('expenses.monthly') }}"
                            class="ml-2 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start auto-rows-auto">
        @foreach($monthlySummaries as $index => $monthly)
            <div x-data="{ show: false }" x-init="setTimeout(() => show = true, {{ $index * 100 }})" x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="group bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 transform cursor-pointer"
                @click="$dispatch('open-modal', {{ json_encode($monthly) }})">
                <div class="bg-gradient-to-r from-red-500 to-orange-600 p-6 text-white flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">{{ $monthly['month_name'] }} {{ $monthly['year'] }}</h3>
                            <p class="text-red-100 text-sm mt-0.5">
                                {{ $monthly['transaction_count'] }}
                                {{ Str::plural('transaction', $monthly['transaction_count']) }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="flex flex-col items-end">
                            <p class="text-3xl font-bold">{{ number_format($monthly['total_amount'], 2) }}
                                {{ config('app.currency', '$') }}
                            </p>
                            <p class="text-red-100 text-sm mt-1">
                                @if($monthly['transaction_count'] > 0)
                                    Avg: {{ number_format($monthly['total_amount'] / $monthly['transaction_count'], 2) }}
                                    {{ config('app.currency', '$') }}
                                @else
                                    No transactions
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Modal for Month Details -->
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300"
            x-cloak @click.self="closeModal()" x-trap.noscroll.inert="showModal"
            x-on:keydown.escape.window="closeModal()">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col transform transition-all"
                @click.stop>
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white"
                            x-text="activeMonth ? activeMonth.month_name + ' ' + activeMonth.year : ''"></h3>
                        <button @click.stop="closeModal()"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 rounded-full p-1 transition-colors duration-150"
                            aria-label="Close modal">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="flex-1 overflow-hidden flex flex-col">
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">

                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Total Expenses -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Spent</p>
                                        <p class="mt-1 text-2xl font-bold text-red-600"
                                            x-text="'{{ config('app.currency', '$') }}' + (activeMonth ? Number(activeMonth.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')">
                                        </p>
                                    </div>
                                    <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20">
                                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                                    x-text="activeMonth ? (activeMonth.transaction_count + ' ' + (activeMonth.transaction_count === 1 ? 'transaction' : 'transactions')) : '0 transactions'">
                                </p>
                            </div>

                            <!-- Average Transaction -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg. Transaction
                                        </p>
                                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"
                                            x-text="'{{ config('app.currency', '$') }}' + (activeMonth && activeMonth.transaction_count > 0 ? Number(activeMonth.total_amount / activeMonth.transaction_count).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')">
                                        </p>
                                    </div>
                                    <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span
                                        x-text="activeMonth ? activeMonth.transaction_count + ' transactions' : '0 transactions'"></span>
                                </p>
                            </div>

                            <!-- Busiest Day -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Busiest Day</p>
                                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"
                                            x-text="activeMonth && activeMonth.busiest_day ? new Date(activeMonth.busiest_day.date).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }) : 'N/A'">
                                        </p>
                                    </div>
                                    <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20">
                                        <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                                    x-text="activeMonth && activeMonth.busiest_day ? activeMonth.busiest_day.transaction_count + ' transactions' : 'No data'">
                                </p>
                            </div>
                        </div>

                        <!-- Category Breakdown -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Spending by Category
                                </h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Category Chart -->
                                <div class="flex items-center justify-center h-64 relative">
                                    <div
                                        class="w-48 h-48 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                        <template x-if="activeMonth?.category_breakdown?.length">
                                            <div x-data="{
                                            chart: null,
                                            
                                            getActiveMonth() {
                                                let parent = this.$el.parentElement;
                                                while (parent) {
                                                    if (parent.__x && parent.__x.$data && parent.__x.$data.activeMonth !== undefined) {
                                                        return parent.__x.$data.activeMonth;
                                                    }
                                                    parent = parent.parentElement;
                                                }
                                                return null;
                                            },
                                            
                                            init() {
                                                let lastData = null;
                                                
                                                // Single render function that handles both initial and subsequent renders
                                                const checkAndRender = () => {
                                                    const activeMonth = this.getActiveMonth();
                                                    if (!activeMonth?.category_breakdown?.length) return;
                                                    
                                                    const currentData = JSON.stringify(activeMonth.category_breakdown);
                                                    if (currentData !== lastData) {
                                                        lastData = currentData;
                                                        this.renderChart(activeMonth.category_breakdown);
                                                    }
                                                };
                                                
                                                // Initial check
                                                checkAndRender();
                                                
                                                // Poll for changes (reduced frequency to 1000ms)
                                                this.pollingInterval = setInterval(checkAndRender, 1000);
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
                                                    const chartData = {
                                                        labels: categories.map(c => c.category || 'Uncategorized'),
                                                        datasets: [{
                                                            data: categories.map(c => parseFloat(c.total_amount) || 0),
                                                            backgroundColor: [
                                                                '#EF4444', '#F59E0B', '#10B981', '#3B82F6', 
                                                                '#8B5CF6', '#EC4899', '#F97316', '#06B6D4'
                                                            ].slice(0, categories.length),
                                                            borderWidth: 0,
                                                        }]
                                                    };
                                                    
                                                    console.log('Chart data prepared:', chartData);
                                                    
                                                    this.chart = new Chart(ctx, {
                                                        type: 'doughnut',
                                                        data: chartData,
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
                                                                            return `${label}: {{ config('app.currency', '$') }}${value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} (${percentage}%)`;
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
                                                // Clear the polling interval
                                                if (this.pollingInterval) {
                                                    clearInterval(this.pollingInterval);
                                                }
                                                
                                                // Clean up chart instance
                                                if (this.chart) {
                                                    console.log('Destroying chart instance');
                                                    this.chart.destroy();
                                                    this.chart = null;
                                                }
                                            }
                                        }" x-init="init()" class="relative w-full h-full">
                                                <canvas x-ref="chartCanvas" class="w-full h-full"></canvas>
                                                <div x-show="!chart"
                                                    class="absolute inset-0 flex items-center justify-center">
                                                    <p class="text-sm text-gray-500">Loading chart...</p>
                                                </div>
                                            </div>
                                        </template>
                                        <p x-show="!activeMonth || !activeMonth.category_breakdown || activeMonth.category_breakdown.length === 0"
                                            class="text-sm text-gray-500 dark:text-gray-400 text-center">
                                            No category data available
                                        </p>
                                    </div>
                                </div>

                                <!-- Category List -->
                                <div class="space-y-3">
                                    <template
                                        x-if="activeMonth && activeMonth.category_breakdown && activeMonth.category_breakdown.length > 0">
                                        <div class="space-y-3">
                                            <template x-for="(category, index) in activeMonth.category_breakdown"
                                                :key="index">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-3 h-3 rounded-full"
                                                            :style="'background-color: ' + ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#F97316', '#06B6D4'][index % 8]">
                                                        </div>
                                                        <span
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                                            x-text="category.category || 'Uncategorized'">
                                                        </span>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white"
                                                            x-text="'{{ config('app.currency', '$') }}' + Number(category.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                                        </div>
                                                        <div class="text-xs text-gray-500"
                                                            x-text="Math.round((category.total_amount / activeMonth.total_amount) * 100) + '% of total'">
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <p x-show="!activeMonth || !activeMonth.category_breakdown || activeMonth.category_breakdown.length === 0"
                                        class="text-sm text-gray-500 dark:text-gray-400">
                                        No category data available for this month.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Methods</h4>
                            <div class="space-y-4">
                                <template
                                    x-if="activeMonth && activeMonth.payment_methods && activeMonth.payment_methods.length > 0">
                                    <div class="space-y-4">
                                        <template x-for="(method, index) in activeMonth.payment_methods" :key="index">
                                            <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                                <div class="flex justify-between items-center text-sm">
                                                    <span
                                                        class="font-medium text-gray-700 dark:text-gray-200 capitalize"
                                                        x-text="method.payment_method ? method.payment_method.replace(/_/g, ' ') : 'Unknown'">
                                                    </span>
                                                    <span class="text-gray-900 dark:text-white font-medium"
                                                        x-text="'{{ config('app.currency', '$') }}' + (method.total_amount ? Number(method.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')">
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mt-2">
                                                    <div class="bg-blue-600 h-2 rounded-full"
                                                        :style="'width: ' + (activeMonth && activeMonth.total_amount > 0 ? (method.total_amount / activeMonth.total_amount * 100) : 0) + '%'">
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                    <span
                                                        x-text="(method.transaction_count || 0) + ' ' + ((method.transaction_count === 1) ? 'transaction' : 'transactions')"></span>
                                                    <span
                                                        x-text="(activeMonth && activeMonth.total_amount > 0) ? (Math.round((method.total_amount / activeMonth.total_amount) * 100) + '% of total') : '0% of total'"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template
                                    x-if="!activeMonth || !activeMonth.payment_methods || activeMonth.payment_methods.length === 0">
                                    <div class="text-center py-4">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M3 10h18M7 3v2m10-2v2M7 19v2m10-2v2M5 10v6a2 2 0 002 2h10a2 2 0 002-2v-6H5z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            No payment method data available for this month.
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Recent Transactions -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-[350px]">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Transactions</h4>
                            </div>
                            <div class="flex-1 overflow-y-auto">
                                <template x-if="activeMonth && activeMonth.expenses && activeMonth.expenses.length > 0">
                                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <template x-for="(expense, index) in activeMonth.expenses.slice(0, 10)"
                                            :key="expense.id">
                                            <div
                                                class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20">
                                                            <svg class="h-5 w-5 text-red-600" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                            </svg>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                                                x-text="expense.description || 'Expense'"></p>
                                                            <div class="flex items-center mt-1 space-x-2 text-xs">
                                                                <span
                                                                    class="text-gray-500 dark:text-gray-400 whitespace-nowrap"
                                                                    x-text="new Date(expense.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })">
                                                                </span>
                                                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                                                <span
                                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium whitespace-nowrap overflow-hidden overflow-ellipsis max-w-[120px]"
                                                                    :class="expense.category?.color_class || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                                                    :title="expense.category?.name || 'Uncategorized'"
                                                                    x-text="expense.category?.name || 'Uncategorized'">
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-right ml-2">
                                                        <p class="text-sm font-semibold text-red-600 whitespace-nowrap"
                                                            x-text="'{{ config('app.currency', '$') }}' + Number(expense.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 capitalize whitespace-nowrap"
                                                            x-text="expense.payment_method?.replace(/_/g, ' ') || ''">
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template
                                    x-if="activeMonth && (!activeMonth.expenses || activeMonth.expenses.length === 0)">
                                    <div class="h-full flex items-center justify-center p-8 text-center">
                                        <div>
                                            <div
                                                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <h3 class="mt-3 text-sm font-medium text-gray-900 dark:text-white">No
                                                transactions</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                No expenses recorded for this month.
                                            </p>
                                            <div class="mt-4">
                                                <a href="{{ route('expenses.create') }}"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Add Expense
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex justify-end">
                            <button @click="closeModal()" type="button"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 150ms ease-in-out;
        }

        .fade-enter-from,
        .fade-leave-to {
            opacity: 0;
        }
    </style>