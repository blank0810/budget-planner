@props(['budget', 'categories', 'months', 'years'])

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
        <!-- Category -->
        <div class="sm:col-span-3">
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <div class="mt-1">
                <select id="category" name="category" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="" disabled {{ old('category', $budget->category) ? '' : 'selected' }}>Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ old('category', $budget->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Budget Name -->
        <div class="sm:col-span-3">
            <label for="budget_name" class="block text-sm font-medium text-gray-700">Budget Name</label>
            <div class="mt-1">
                <input type="text" id="budget_name" name="budget_name" value="{{ old('budget_name', $budget->budget_name ?? '') }}"
                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    placeholder="e.g., Home Insurance, Car Insurance">
                <p class="mt-1 text-xs text-gray-500">A name to identify this specific budget (e.g., "Car Insurance")</p>
                @error('budget_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Amount -->
        <div class="sm:col-span-3"
            x-data="amountField({{ $budget->amount ?? 'null' }})"
            x-init="
                // Format the initial value
                displayValue = addThousandSeparators(rawValue);
             ">
            <label for="amount" class="block text-sm font-medium text-gray-700">Budget Amount</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input
                    type="text"
                    name="amount_display"
                    id="amount_display"
                    x-model="displayValue"
                    x-ref="input"
                    x-on:input="onInput($event)"
                    x-on:blur="onBlur()"
                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                    placeholder="0.00"
                    inputmode="decimal"
                    autocomplete="off"
                >
                <input type="hidden" name="amount" id="amount" x-model="rawValue" value="{{ old('amount', $budget->amount) }}">
            </div>
            @error('amount')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Year -->
        <div class="sm:col-span-3">
            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
            <div class="mt-1">
                <input type="number" id="year" name="year" min="2020" max="2030" value="{{ old('year', $budget->year) }}"
                    placeholder="Enter year (e.g. 2025)"
                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                @error('year')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Month -->
        <div class="sm:col-span-3">
            <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
            <div class="mt-1">
                <select id="month" name="month" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="" disabled {{ old('month', $budget->month) ? '' : 'selected' }}>Select a month</option>
                    @foreach($months as $value => $label)
                        <option value="{{ $value }}" {{ old('month', $budget->month) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('month')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Active Status Toggle -->
        <div class="sm:col-span-6">
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('is_active', $budget->is_active ?? true) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    This budget is active
                </label>
            </div>
        </div>

        <!-- Notes -->
        <div class="sm:col-span-6">
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
            <div class="mt-1">
                <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Additional notes about this budget...">{{ old('notes', $budget->notes) }}</textarea>
                @error('notes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="pt-5">
        <div class="flex justify-end">
            <a href="{{ route('budgets.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ $budget->exists ? 'Update' : 'Create' }} Budget
            </button>
</div>

@push('scripts')
<script>
    // Amount field formatter component
    function amountField(initialAmount = null) {
        return {
            rawValue: initialAmount !== null ? parseFloat(initialAmount).toFixed(2) : '',
            displayValue: '',
            isFormatting: false,
            cursorPosition: 0,
            
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
            },
            
            onBlur() {
                if (this.rawValue) {
                    // Ensure proper decimal places
                    const num = parseFloat(this.rawValue);
                    this.rawValue = num.toFixed(2);
                    this.displayValue = this.addThousandSeparators(this.rawValue);
                }
            },
            
            addThousandSeparators(value) {
                if (!value) return '';
                
                // Split into integer and decimal parts
                const parts = value.toString().split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                
                // Add decimal part if it exists
                if (parts.length > 1) {
                    // Ensure exactly 2 decimal places
                    let decimal = parts[1].substring(0, 2);
                    // Pad with zeros if needed
                    while (decimal.length < 2) decimal += '0';
                    return `${parts[0]}.${decimal}`;
                }
                
                return parts[0];
            },
            
            onKeyDown(event) {
                // Allow: backspace, delete, tab, escape, enter, decimal point, and numbers
                if ([46, 8, 9, 27, 13, 110, 190].includes(event.keyCode) ||
                    // Allow: Ctrl+A, Command+A
                    (event.keyCode === 65 && (event.ctrlKey === true || event.metaKey === true)) ||
                    // Allow: Ctrl+C, Command+C
                    (event.keyCode === 67 && (event.ctrlKey === true || event.metaKey === true)) ||
                    // Allow: Ctrl+X, Command+X
                    (event.keyCode === 88 && (event.ctrlKey === true || event.metaKey === true)) ||
                    // Allow: home, end, left, right
                    (event.keyCode >= 35 && event.keyCode <= 39)) {
                    // Let it happen, don't do anything
                    return;
                }
                
                // Ensure that it is a number and stop the keypress if not
                if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {
                    event.preventDefault();
                }
            },
            
            highlightField() {
                this.isFormatting = true;
                setTimeout(() => {
                    this.isFormatting = false;
                }, 150);
                
                // Highlight the field briefly
                const input = this.$refs.input;
                input.classList.add('ring-2', 'ring-blue-500');
                setTimeout(() => {
                    input.classList.remove('ring-2', 'ring-blue-500');
                }, 150);
            }
        };
    }
</script>
@endpush


