@props(['expense', 'categories', 'recurringIntervals', 'paymentMethods', 'budgets' => collect()])

@push('styles')
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        .animate-slide-in { animation: slideIn 0.3s ease-out forwards; }
        .form-section { animation-delay: 0.1s; }
        .form-field { animation-delay: calc(var(--delay, 0) * 0.1s); }
        .floating-input:focus-within label,
        .floating-input input:not(:placeholder-shown) + label,
        .floating-input select:not([value=""]) + label {
            @apply transform -translate-y-6 scale-75 text-red-500;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Amount field AlpineJS component
        function amountField(initialAmount = null) {
            return {
                rawValue: initialAmount !== null ? parseFloat(initialAmount).toFixed(2) : '',
                displayValue: '',
                isFormatting: false,
                cursorPosition: 0,
                init() {
                    // Format the initial value if it exists
                    if (this.rawValue) {
                        this.displayValue = this.addThousandSeparators(this.rawValue);
                    }
                },
                onInput(event) {
                    if (this.isFormatting) return;

                    const cursorPosition = event.target.selectionStart;
                    const inputValue = event.target.value;
                    let raw = inputValue.replace(/[^\d.]/g, '');

                    // Handle decimal places
                    const decimalSplit = raw.split('.');
                    if (decimalSplit.length > 2) {
                        raw = decimalSplit[0] + '.' + decimalSplit.slice(1).join('');
                    }
                    if (decimalSplit.length > 1) {
                        raw = decimalSplit[0] + '.' + decimalSplit[1].substring(0, 2);
                    }

                    // Store the raw value (empty string if no input)
                    this.rawValue = raw || '';

                    // Format with thousand separators if there's a value
                    this.displayValue = this.rawValue ? this.addThousandSeparators(this.rawValue) : '';

                    // Adjust cursor position
                    this.$nextTick(() => {
                        // Get the raw value before the cursor
                        const beforeCursor = inputValue.substring(0, cursorPosition);
                        const rawBeforeCursor = beforeCursor.replace(/[^\d]/g, '');
                        const formattedBeforeCursor = this.addThousandSeparators(rawBeforeCursor);

                        // Calculate new cursor position
                        const newCursorPos = formattedBeforeCursor.length + (this.displayValue.length - this.rawValue.length);
                        // Set the cursor position
                        event.target.setSelectionRange(newCursorPos, newCursorPos);
                    });

                    this.highlightField();
                },
                addThousandSeparators(value) {
                    if (!value) return '';
                    const parts = value.toString().split('.');
                    // Format the integer part with thousand separators
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    // Rejoin with decimal part if it exists
                    return parts[1] !== undefined ? parts[0] + '.' + parts[1] : parts[0];
                },
                calculateNewCursorPosition(oldPos, oldValue, newValue) {
                    if (oldPos >= oldValue.length) return newValue.length;
                    const nonDigits = (oldValue.substring(0, oldPos).match(/[^\d]/g) || []).length;
                    const digitsBefore = oldPos - nonDigits;
                    let count = 0;
                    for (let i = 0; i < newValue.length; i++) {
                        if (/\d/.test(newValue[i])) {
                            count++;
                            if (count > digitsBefore) return i;
                        }
                    }
                    return newValue.length;
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
                    // Allow: backspace, delete, tab, escape, enter, decimal point, numbers, keypad numbers
                    if ([46, 8, 9, 27, 13, 110, 190].includes(event.keyCode) ||
                        // Allow: Ctrl+A, Ctrl+C, Ctrl+X
                        (event.keyCode === 65 && (event.ctrlKey || event.metaKey)) ||
                        (event.keyCode === 67 && (event.ctrlKey || event.metaKey)) ||
                        (event.keyCode === 88 && (event.ctrlKey || event.metaKey)) ||
                        // Allow: home, end, left, right
                        (event.keyCode >= 35 && event.keyCode <= 39)) {
                        return;
                    }
                    // Ensure that it is a number and stop the keypress if not
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
                },

                init() {
                    // Format initial value if it exists
                    if (this.rawValue) {
                        this.format({
                            target: {
                                value: this.rawValue
                            }
                        });
                    }
                }
            }
        }
    </script>
@endpush

<!-- Form Container with Gradient Header -->
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden animate-fade-in-up">
    <!-- Gradient Header -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
        <h2 class="text-xl font-semibold text-white">
            {{ $expense->exists ? 'Edit Expense' : 'Add New Expense' }}
        </h2>
        <p class="text-red-100 text-sm mt-1">Track your spending with ease</p>
    </div>

    <div class="p-6">
        <form class="space-y-8 divide-y divide-gray-200">
            <!-- Expense Details Section -->
            <div class="space-y-6 form-section">
                <div class="flex items-center space-x-2 text-gray-700">
                    <x-heroicon-o-document-text class="h-5 w-5 text-red-500" />
                    <h3 class="text-lg font-medium">Expense Details</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Description -->
                    <div class="form-field relative z-0" style="--delay: 1">
                        <div class="relative z-0">
                            <input type="text" name="description" id="description" value="{{ old('description', $expense->description) }}" 
                                class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-500 peer" 
                                placeholder=" " required />
                            <label for="description" class="absolute left-0 -top-1 text-gray-500 text-sm duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <x-heroicon-o-document-text class="absolute right-0 top-4 h-5 w-5 text-gray-400" />
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="form-field relative z-0" style="--delay: 2">
                        <div class="relative z-0 floating-input">
                            <input type="text" name="category" id="category" 
                                   class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-500 peer"
                                   value="{{ old('category', $expense->category) }}" 
                                   placeholder=" " 
                                   list="category-suggestions"
                                   autocomplete="off"
                                   required>
                            <label for="category" class="absolute text-gray-500 duration-300 transform -translate-y-6 scale-75 top-4 z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Category (e.g., Food, Transportation, Bills) <span class="text-red-500">*</span>
                            </label>
                            <div class="absolute right-0 top-4 text-gray-400">
                                <x-heroicon-o-tag class="h-5 w-5" />
                            </div>
                            {{-- <datalist id="category-suggestions">
                                @if(isset($categorySuggestions) && is_array($categorySuggestions))
                                    @foreach($categorySuggestions as $suggestion)
                                        <option value="{{ $suggestion }}">
                                    @endforeach
                                @else
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}">
                                    @endforeach
                                @endif
                            </datalist> --}}
                        </div>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Budget -->
                    <div class="form-field relative z-0" style="--delay: 3">
                        <div class="relative z-0">
                            <select id="budget_id" name="budget_id" 
                                    class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-500 peer">
                                <option value="">-- No Budget --</option>
                                @php
                                    $currentCategory = old('category', $expense->category);
                                    $filteredBudgets = $budgets->filter(function ($budget) use ($currentCategory) {
                                        return $currentCategory ? $budget->category === $currentCategory : true;
                                    });
                                @endphp
                                @foreach($filteredBudgets->groupBy('category') as $category => $categoryBudgets)
                                    <optgroup label="{{ $category }}">
                                        @foreach($categoryBudgets as $budget)
                                            <option value="{{ $budget->id }}" {{ old('budget_id', $expense->budget_id) == $budget->id ? 'selected' : '' }}>
                                                {{ $budget->budget_name }} ({{ $budget->month }}/{{ $budget->year }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <label for="budget_id" class="absolute left-0 -top-1 text-gray-500 text-sm duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Budget (Optional)
                            </label>
                        </div>
                        @error('budget_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Track this expense against a budget</p>
                    </div>

                    <!-- Amount -->
                    <div class="form-field relative z-0" style="--delay: 4"
                         x-data="amountField({{ $expense->amount ?? 'null' }})"
                         x-init="displayValue = addThousandSeparators(rawValue);">
                        <div class="relative z-0">
                            <input type="text" 
                                   x-model="displayValue"
                                   @input="onInput($event)"
                                   @blur="onBlur()"
                                   @keydown="handleKeyDown($event)"
                                   id="amount" 
                                   name="amount" 
                                   class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-500 peer text-xl font-semibold" 
                                   placeholder=" " 
                                   required />
                            <label for="amount" class="absolute left-0 -top-1 text-gray-500 text-sm duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <span class="absolute right-0 top-4 text-gray-500 font-medium">$</span>
                            <x-heroicon-o-currency-dollar class="absolute right-6 top-4 h-5 w-5 text-gray-400" />
                        </div>
                        <input type="hidden" name="amount" :value="rawValue">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method -->
                    <div class="form-field relative z-0" style="--delay: 5">
                        <div class="relative z-0">
                            <select id="payment_method" name="payment_method" 
                                    class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-500 peer" required>
                                <option value="" disabled {{ old('payment_method', $expense->payment_method) ? '' : 'selected' }}></option>
                                @foreach($paymentMethods as $value => $label)
                                    <option value="{{ $value }}" {{ old('payment_method', $expense->payment_method) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <label for="payment_method" class="absolute left-0 -top-1 text-gray-500 text-sm duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <x-heroicon-o-credit-card class="absolute right-0 top-4 h-5 w-5 text-gray-400" />
                        </div>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="form-field relative z-0" style="--delay: 6">
                        <div class="relative z-0">
                            <input type="date" name="date" id="date" 
                                   value="{{ old('date', $expense->date ? $expense->date->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                                   class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-500 peer" 
                                   required />
                            <label for="date" class="absolute left-0 -top-1 text-gray-500 text-sm duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <x-heroicon-o-calendar class="absolute right-0 top-4 h-5 w-5 text-gray-400" />
                        </div>
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Receipt Upload -->
                    <div class="form-field relative z-0 md:col-span-2" style="--delay: 7">
                        <div class="relative z-0">
                            <div class="flex items-center justify-center w-full">
                                <label for="receipt" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <x-heroicon-o-arrow-up-tray class="w-8 h-8 mb-2 text-gray-500" />
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG, PDF (MAX. 2MB)</p>
                                    </div>
                                    <input id="receipt" name="receipt" type="file" class="hidden" accept="image/*,.pdf" />
                                </label>
                            </div>
                            @if($expense->receipt_path)
                                <div class="mt-2 flex items-center text-sm text-green-600">
                                    <x-heroicon-o-document-check class="h-4 w-4 mr-1" />
                                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="underline hover:text-green-700">View current receipt</a>
                                </div>
                            @endif
                            @error('receipt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Recurring Toggle -->
                    <div class="form-field md:col-span-2" style="--delay: 8">
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center h-5">
                                <input id="is_recurring" name="is_recurring" type="checkbox" 
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" 
                                       {{ old('is_recurring', $expense->is_recurring) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_recurring" class="font-medium text-gray-700 flex items-center">
                                    <x-heroicon-o-arrow-path class="h-4 w-4 mr-1 text-red-500" />
                                    Recurring Expense
                                </label>
                                <p class="text-gray-500">This is a recurring expense</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recurring Fields (conditionally shown) -->
                    <div id="recurring_fields" class="md:col-span-2 space-y-4 p-4 bg-gray-50 rounded-lg transition-all duration-300" 
                         style="{{ old('is_recurring', $expense->is_recurring) ? '' : 'display: none;' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-field relative z-0">
                                <div class="relative z-0">
                                    <select id="recurring_interval" name="recurring_interval" 
                                            class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-500 peer">
                                        <option value="" disabled {{ old('recurring_interval', $expense->recurring_interval) ? '' : 'selected' }}></option>
                                        @foreach($recurringIntervals as $value => $label)
                                            <option value="{{ $value }}" {{ old('recurring_interval', $expense->recurring_interval) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <label for="recurring_interval" class="absolute left-0 -top-1 text-gray-500 text-sm duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                        Repeat Every
                                    </label>
                                </div>
                                @error('recurring_interval')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-field relative z-0">
                                <div class="relative z-0">
                                    <input type="date" name="next_recurring_date" id="next_recurring_date" 
                                           value="{{ old('next_recurring_date', $expense->next_recurring_date ? $expense->next_recurring_date->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                                           class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-500 peer" 
                                           placeholder=" ">
                                    <label for="next_recurring_date" class="absolute left-0 -top-1 text-gray-500 text-sm duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                        Next Occurrence
                                    </label>
                                </div>
                                @error('next_recurring_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="form-field relative z-0 md:col-span-2" style="--delay: 9">
                        <div class="relative z-0">
                            <textarea id="notes" name="notes" rows="3" 
                                    class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-500 peer" 
                                    placeholder=" ">{{ old('notes', $expense->notes) }}</textarea>
                            <label for="notes" class="absolute left-0 -top-1 text-gray-500 text-sm duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Notes (Optional)
                            </label>
                            <x-heroicon-o-document-text class="absolute right-0 top-4 h-5 w-5 text-gray-400" />
                        </div>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-8 flex justify-end space-x-3 form-section">
                    <a href="{{ route('expenses.index') }}" 
                       class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105">
                        {{ $expense->exists ? 'Update Expense' : 'Add Expense' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Background Decoration -->
    <div class="fixed inset-0 -z-10 overflow-hidden opacity-10">
        <div class="absolute inset-0 bg-gradient-to-br from-red-100 to-red-200"></div>
    </div>
</div>

@push('scripts')
    <script>
        // Toggle recurring fields
        document.getElementById('is_recurring').addEventListener('change', function() {
            const recurringFields = document.getElementById('recurring_fields');
            if (this.checked) {
                recurringFields.classList.remove('hidden');
            } else {
                recurringFields.classList.add('hidden');
            }
        });

        // Set next occurrence date to today if not set
        document.addEventListener('DOMContentLoaded', function() {
            const isRecurring = document.getElementById('is_recurring');
            const nextRecurringDate = document.getElementById('next_recurring_date');

            if (isRecurring && nextRecurringDate && isRecurring.checked && !nextRecurringDate.value) {
                const today = new Date().toISOString().split('T')[0];
                nextRecurringDate.value = today;
            }
        });

        // Group budgets by category for easy access
        @php
            $budgetsData = $budgets->groupBy('category')->map(function ($budgets, $category) {
                return $budgets->map(function ($budget) use ($category) {
                    return [
                        'id' => $budget->id,
                        'budget_name' => $budget->budget_name,
                        'month' => $budget->month,
                        'year' => $budget->year,
                        'category' => $category
                    ];
                });
            })->toJson(JSON_HEX_APOS | JSON_HEX_QUOT);
        @endphp
        const budgetsByCategory = {!! $budgetsData !!};

        // Function to update budget dropdown based on selected category
        function updateBudgetDropdown(selectedCategory) {
            const budgetSelect = document.getElementById('budget_id');
            if (!budgetSelect) return;

            const currentBudgetId = '{{ old('budget_id', $expense->budget_id) }}';

            // Clear existing options except the first one
            budgetSelect.innerHTML = '<option value="">-- Select a budget --</option>';

            if (selectedCategory && budgetsByCategory[selectedCategory]) {
                // Add optgroup for the selected category
                const optgroup = document.createElement('optgroup');
                optgroup.label = selectedCategory;

                // Add budgets for the selected category
                budgetsByCategory[selectedCategory].forEach(budget => {
                    const option = document.createElement('option');
                    option.value = budget.id;
                    option.textContent = `${budget.budget_name} (${budget.month}/${budget.year})`;
                    if (budget.id == currentBudgetId) {
                        option.selected = true;
                    }
                    optgroup.appendChild(option);
                });

                budgetSelect.appendChild(optgroup);
            } else {
                // If no category selected or no budgets for the category, show all budgets
                Object.entries(budgetsByCategory).forEach(([category, budgets]) => {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = category;

                    budgets.forEach(budget => {
                        const option = document.createElement('option');
                        option.value = budget.id;
                        option.textContent = `${budget.budget_name} (${budget.month}/${budget.year})`;
                        option.dataset.category = budget.category; // Store category in data attribute
                        if (budget.id == currentBudgetId) {
                            option.selected = true;
                        }
                        optgroup.appendChild(option);
                    });

                    budgetSelect.appendChild(optgroup);
                });
            }
        }

        // Function to update category based on selected budget
        function updateCategoryFromBudget(budgetId) {
            if (!budgetId) return;

            // Find the selected budget option
            const budgetSelect = document.getElementById('budget_id');
            const selectedOption = budgetSelect.options[budgetSelect.selectedIndex];

            // If the budget has a category data attribute, update the category field
            if (selectedOption && selectedOption.dataset.category) {
                const categorySelect = document.getElementById('category');
                categorySelect.value = selectedOption.dataset.category;

                // Trigger change event to update any dependent fields
                const event = new Event('change');
                categorySelect.dispatchEvent(event);
            }
        }

        // Add event listener to budget select
        document.addEventListener('DOMContentLoaded', function() {
            const budgetSelect = document.getElementById('budget_id');
            if (budgetSelect) {
                // Set initial category if a budget is already selected
                if (budgetSelect.value) {
                    updateCategoryFromBudget(budgetSelect.value);
                }

                // Update category when budget changes
                budgetSelect.addEventListener('change', function() {
                    updateCategoryFromBudget(this.value);
                });
            }
        });

        // Initialize the budget dropdown based on the initially selected category
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');

            // Set up change event listener for category dropdown
            if (categorySelect) {
                // Initial update
                updateBudgetDropdown(categorySelect.value);

                // Update on category change
                categorySelect.addEventListener('change', function() {
                    updateBudgetDropdown(this.value);
                });
            }
        });
    </script>
@endpush