@props(['expense', 'categories', 'recurringIntervals', 'paymentMethods', 'budgets' => collect()])

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

<div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
    <!-- Description -->
    <div class="sm:col-span-3">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <div class="mt-1">
            <input type="text" name="description" id="description" value="{{ old('description', $expense->description) }}" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="e.g., Grocery shopping, Gas, Coffee">
            @error('description')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Category -->
    <div class="sm:col-span-3">
        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
        <div class="mt-1">
            <select id="category" name="category" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="" {{ old('category', $expense->category) ? '' : 'selected' }}>Select a category</option>
                @foreach($categories as $category)
                <option value="{{ $category }}" {{ old('category', $expense->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
            @error('category')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Budget -->
    <div class="sm:col-span-3">
        <label for="budget_id" class="block text-sm font-medium text-gray-700">Budget (Optional)</label>
        <div class="mt-1">
            <select id="budget_id" name="budget_id" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="">-- Select a budget --</option>
                @php
                    $currentCategory = old('category', $expense->category);
                    $filteredBudgets = $budgets->filter(function($budget) use ($currentCategory) {
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
            @error('budget_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Select a budget to track this expense against</p>
        </div>
    </div>

    <!-- Amount -->
    <div class="sm:col-span-3"
        x-data="amountField({{ $expense->amount ?? 'null' }})"
        x-init="
            // Format the initial value
            displayValue = addThousandSeparators(rawValue);
         ">
        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <input
                type="text"
                name="amount"
                id="amount"
                x-model="displayValue"
                x-ref="amountInput"
                x-on:input="onInput($event)"
                x-on:blur="onBlur()"
                x-on:keydown="handleKeyDown($event)"
                :class="{'ring-2 ring-red-500 border-red-500': isFormatting, 'border-gray-300': !isFormatting}"
                class="focus:ring-red-500 focus:border-red-500 block w-full pl-7 pr-12 sm:text-sm rounded-md"
                placeholder="0.00"
                inputmode="decimal">
            <input type="hidden" name="amount" :value="rawValue">
        </div>
        @error('amount')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Payment Method -->
    <div class="sm:col-span-3">
        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
        <div class="mt-1">
            <select id="payment_method" name="payment_method" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="" disabled {{ old('payment_method', $expense->payment_method) ? '' : 'selected' }}>Select payment method</option>
                @foreach($paymentMethods as $value => $label)
                <option value="{{ $value }}" {{ old('payment_method', $expense->payment_method) == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('payment_method')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Date -->
    <div class="sm:col-span-3">
        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
        <div class="mt-1">
            <input type="date" name="date" id="date" value="{{ old('date', $expense->date ? $expense->date->format('Y-m-d') : '') }}" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
            @error('date')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Receipt Upload -->
    <div class="sm:col-span-3">
        <label for="receipt" class="block text-sm font-medium text-gray-700">Receipt (Optional)</label>
        <div class="mt-1">
            <input type="file" name="receipt" id="receipt" accept="image/*,.pdf" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
            <p class="mt-1 text-xs text-gray-500">PNG, JPG, PDF up to 2MB</p>
            @if($expense->receipt_path)
            <p class="mt-1 text-xs text-green-600">Current receipt: <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="underline">View receipt</a></p>
            @endif
            @error('receipt')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Recurring Toggle -->
    <div class="sm:col-span-6">
        <div class="flex items-center">
            <input type="checkbox" id="is_recurring" name="is_recurring" value="1" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" {{ old('is_recurring', $expense->is_recurring) ? 'checked' : '' }}>
            <label for="is_recurring" class="ml-2 block text-sm text-gray-700">
                This is a recurring expense
            </label>
        </div>
    </div>

    <!-- Recurring Interval (conditionally shown) -->
    <div id="recurring_fields" class="sm:col-span-6 {{ old('is_recurring', $expense->is_recurring) ? '' : 'hidden' }}">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-3">
                <label for="recurring_interval" class="block text-sm font-medium text-gray-700">Recurring Interval</label>
                <div class="mt-1">
                    <select id="recurring_interval" name="recurring_interval" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="" disabled {{ old('recurring_interval', $expense->recurring_interval) ? '' : 'selected' }}>Select an interval</option>
                        @foreach($recurringIntervals as $value => $label)
                        <option value="{{ $value }}" {{ old('recurring_interval', $expense->recurring_interval) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('recurring_interval')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="sm:col-span-3">
                <label for="next_recurring_date" class="block text-sm font-medium text-gray-700">Next Occurrence</label>
                <div class="mt-1">
                    <input type="date" name="next_recurring_date" id="next_recurring_date" value="{{ old('next_recurring_date', $expense->next_recurring_date ? $expense->next_recurring_date->format('Y-m-d') : '') }}" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('next_recurring_date')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <div class="sm:col-span-6">
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
        <div class="mt-1">
            <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Additional details about this expense...">{{ old('notes', $expense->notes) }}</textarea>
            @error('notes')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="pt-5">
    <div class="flex justify-end">
        <a href="{{ route('expenses.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Cancel
        </a>
        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            {{ $expense->exists ? 'Update' : 'Create' }} Expense
        </button>
    </div>
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
        $budgetsData = $budgets->groupBy('category')->map(function($budgets, $category) {
            return $budgets->map(function($budget) use ($category) {
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