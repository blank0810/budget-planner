@inject('str', 'Illuminate\\Support\\Str')

<div x-data="{
        showModal: false,
        activeMonth: null,
        openModal(monthData) {
            this.activeMonth = typeof monthData === 'string' ? JSON.parse(monthData) : monthData;
            this.showModal = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.showModal = false;
            this.activeMonth = null;
            document.body.classList.remove('overflow-hidden');
        }
    }" @open-modal.window="openModal($event.detail)"
    class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start auto-rows-auto">
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
                    <h2 class="text-xl font-bold"
                        x-text="activeMonth ? activeMonth.month + ' ' + activeMonth.year : ''"></h2>
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

            <div class="p-6 max-h-[70vh] overflow-y-auto space-y-4">
                <template x-if="activeMonth && activeMonth.incomes && activeMonth.incomes.length > 0">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-2">
                            Transactions
                        </h4>
                        <template x-for="income in activeMonth.incomes" :key="income.id">
                            <div
                                class="flex justify-between items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-700 transition-colors duration-200">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white truncate"
                                            x-text="income.source"></p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="income.date"></span>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"
                                                x-text="income.category"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-lg font-bold text-emerald-600"
                                        x-text="'$' + Number(income.amount).toLocaleString()"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

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