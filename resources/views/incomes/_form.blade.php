@props(['income', 'categories', 'recurringIntervals'])

<div class="space-y-8" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
    <!-- Form Header -->
    <div x-show="show" x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl shadow-lg p-6 border border-emerald-100">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">
                    {{ $income->exists ? 'Update Income Entry' : 'Add New Income' }}
                </h3>
                <p class="text-sm text-gray-600 mt-0.5">
                    {{ $income->exists ? 'Modify your income details below' : 'Track your income by filling out the form below' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Form Validation Errors -->
    @if($errors->any())
        <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 200)" x-show="show"
            x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="rounded-2xl bg-gradient-to-r from-red-50 to-pink-50 p-6 shadow-lg border border-red-200">
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="p-2 bg-red-100 rounded-xl">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-red-900 mb-1">
                        {{ $errors->count() === 1 ? 'There is 1 error' : "There are {$errors->count()} errors" }} with your
                        submission
                    </h3>
                    <div class="text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Fields -->
    <div x-show="show" x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-6">
                <!-- Source -->
                <div class="sm:col-span-3 group" x-data="{ focused: false }">
                    <label for="source" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        Income Source
                    </label>
                    <input type="text" name="source" id="source" value="{{ old('source', $income->source) }}"
                        @focus="focused = true" @blur="focused = false"
                        class="block w-full rounded-xl border-gray-300 shadow-md focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 sm:text-sm"
                        placeholder="e.g., Salary, Freelance, Investment">
                    @error('source')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="sm:col-span-3 group">
                    <label for="category"
                        class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                        Category
                    </label>
                    <select id="category" name="category"
                        class="block w-full rounded-xl border-gray-300 shadow-md focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 sm:text-sm">
                        <option value="" disabled {{ old('category', $income->category) ? '' : 'selected' }}>
                            Select a category
                        </option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('category', $income->category) == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Amount -->
                <div class="sm:col-span-3" x-data="amountField('{{ $income->amount ?? '' }}')">
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Amount
                    </label>
                    <div class="relative rounded-xl shadow-md"
                        :class="{'ring-2 ring-emerald-500 shadow-lg': isFormatting}">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-base font-medium">$</span>
                        </div>
                        <input type="text" name="amount_display" id="amount_display" x-model="displayValue"
                            x-ref="input" x-on:input="onInput($event)" x-on:blur="onBlur()"
                            x-on:keydown="handleKeyDown($event)"
                            :class="{'border-emerald-500 ring-2 ring-emerald-200': isFormatting, 'border-gray-300': !isFormatting}"
                            class="block w-full pl-10 pr-4 py-3 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 sm:text-sm font-medium"
                            placeholder="0.00" inputmode="decimal">
                        <input type="hidden" name="amount" id="amount" x-model="rawValue">
                    </div>
                    @error('amount')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Date -->
                <div class="sm:col-span-3">
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Date
                    </label>
                    <input type="date" name="date" id="date"
                        value="{{ old('date', $income->date ? $income->date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                        class="block w-full rounded-xl border-gray-300 shadow-md focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 sm:text-sm">
                    @error('date')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Recurring Transaction Fields -->
                <div id="recurring_fields"
                    class="sm:col-span-6 {{ old('is_recurring', $income->is_recurring) ? '' : 'hidden' }}">
                    <!-- Recurring interval and other fields will go here -->
                </div>

                <!-- Notes -->
                <div class="sm:col-span-6">
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                            </path>
                        </svg>
                        Notes
                        <span class="text-xs font-normal text-gray-500">(Optional)</span>
                    </label>
                    <textarea id="notes" name="notes" rows="4"
                        class="block w-full rounded-xl border-gray-300 shadow-md focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 sm:text-sm resize-none"
                        placeholder="Add any additional details about this income...">{{ old('notes', $income->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('incomes.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 font-medium shadow-md hover:shadow-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    <span>Cancel</span>
                </a>
                <button type="submit"
                    class="group inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ $income->exists ? 'Update Income' : 'Create Income' }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Amount field AlpineJS component
        function amountField(initialAmount = null) {
            return {
                rawValue: initialAmount !== null ? parseFloat(initialAmount).toFixed(2) : '',
                displayValue: '',
                isFormatting: false,

                init() {
                    if (this.rawValue) {
                        this.displayValue = this.addThousandSeparators(this.rawValue);
                    }
                },

                onInput(event) {
                    if (this.isFormatting) return;

                    const cursorPosition = event.target.selectionStart;
                    const inputValue = event.target.value;
                    let raw = inputValue.replace(/[^\d.]/g, '');

                    const decimalSplit = raw.split('.');
                    if (decimalSplit.length > 2) {
                        raw = decimalSplit[0] + '.' + decimalSplit.slice(1).join('');
                    }
                    if (decimalSplit.length > 1) {
                        raw = decimalSplit[0] + '.' + decimalSplit[1].substring(0, 2);
                    }

                    this.rawValue = raw || '';
                    this.displayValue = this.rawValue ? this.addThousandSeparators(this.rawValue) : '';

                    this.$nextTick(() => {
                        const beforeCursor = inputValue.substring(0, cursorPosition);
                        const digitsBeforeCursor = beforeCursor.replace(/[^\d]/g, '').length;

                        let digitCount = 0;
                        let newCursorPos = 0;

                        for (let i = 0; i < this.displayValue.length; i++) {
                            if (/\d/.test(this.displayValue[i])) {
                                digitCount++;
                                if (digitCount > digitsBeforeCursor) {
                                    newCursorPos = i;
                                    break;
                                }
                            }
                            if (digitCount === digitsBeforeCursor) {
                                newCursorPos = i + 1;
                            }
                        }

                        if (digitCount === digitsBeforeCursor) {
                            newCursorPos = this.displayValue.length;
                        }

                        event.target.setSelectionRange(newCursorPos, newCursorPos);
                    });

                    this.highlightField();
                },

                addThousandSeparators(value) {
                    if (!value) return '';
                    const parts = value.toString().split('.');
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    return parts[1] !== undefined ? parts[0] + '.' + parts[1] : parts[0];
                },

                onBlur() {
                    if (this.rawValue) {
                        const num = parseFloat(this.rawValue);
                        if (!isNaN(num)) {
                            this.rawValue = num.toFixed(2);
                            this.displayValue = this.addThousandSeparators(this.rawValue);
                        }
                    } else {
                        this.rawValue = '';
                        this.displayValue = '';
                    }
                },

                handleKeyDown(event) {
                    if ([46, 8, 9, 27, 13, 110, 190].includes(event.keyCode) ||
                        (event.keyCode === 65 && (event.ctrlKey || event.metaKey)) ||
                        (event.keyCode === 67 && (event.ctrlKey || event.metaKey)) ||
                        (event.keyCode === 88 && (event.ctrlKey || event.metaKey)) ||
                        (event.keyCode >= 35 && event.keyCode <= 39)) {
                        return;
                    }
                    if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) &&
                        (event.keyCode < 96 || event.keyCode > 105) &&
                        event.keyCode !== 190 &&
                        event.keyCode !== 110) {
                        event.preventDefault();
                    }
                },

                highlightField() {
                    this.isFormatting = true;
                    setTimeout(() => {
                        this.isFormatting = false;
                    }, 150);
                }
            };
        }
    </script>
@endpush