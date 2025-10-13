@props(['income', 'categories', 'recurringIntervals'])

{{-- Debug output
@if(app()->environment('local'))
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Debug Information</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p class="font-semibold">Income Object:</p>
                    <pre class="bg-white p-2 rounded border border-yellow-200 overflow-auto max-h-40">{{ print_r($income->toArray(), true) }}</pre>
                    
                    <p class="font-semibold mt-2">Categories:</p>
                    <pre class="bg-white p-2 rounded border border-yellow-200 overflow-auto max-h-40">{{ print_r($categories, true) }}</pre>
                </div>
            </div>
        </div>
    </div>
@endif --}}

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
        .floating-input select:not([value=""]) + label,
        .floating-input textarea:not(:placeholder-shown) + label {
            @apply transform -translate-y-6 scale-75 text-emerald-500;
        }
        .floating-input input:focus,
        .floating-input select:focus,
        .floating-input textarea:focus {
            @apply border-emerald-500 ring-2 ring-emerald-500/20 outline-none;
        }
        .form-radio:checked {
            @apply bg-emerald-500 border-emerald-500;
        }
        .form-radio:focus {
            @apply ring-2 ring-offset-2 ring-emerald-500/50;
        }
    </style>
@endpush

<!-- Form Container with Gradient Header -->
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-xl overflow-hidden animate-fade-in-up">
    <!-- Gradient Header -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-5">
        <div class="flex items-center">
            <div class="p-2.5 bg-white/10 rounded-lg mr-4">
                <x-heroicon-o-banknotes class="h-6 w-6 text-white" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">
                    {{ $income->exists ? 'Update Income' : 'Add New Income' }}
                </h2>
                <p class="text-emerald-100 text-sm mt-1">
                    {{ $income->exists ? 'Update your income details' : 'Track your income with ease' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Form Validation Errors -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-6 rounded-r-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-s-x-mark class="h-5 w-5 text-red-500" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        {{ $errors->count() === 1 ? 'There is 1 error' : "There are {$errors->count()} errors" }} with your submission
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="p-6">
        <form class="space-y-6">
            <!-- Income Details Section -->
            <div class="space-y-6 form-section">
                <div class="flex items-center space-x-2 text-gray-700">
                    <x-heroicon-o-currency-dollar class="h-5 w-5 text-emerald-500" />
                    <h3 class="text-lg font-medium">Income Details</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Source -->
                    <div class="form-field relative z-0" style="--delay: 1">
                        <div class="relative z-0 floating-input">
                            <input type="text" name="source" id="source" value="{{ old('source', $income->source) }}" 
                                class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-emerald-500 peer" 
                                placeholder=" " required />
                            <label for="source" class="absolute text-gray-500 duration-300 transform -translate-y-6 scale-75 top-4 z-10 origin-[0] peer-focus:left-0 peer-focus:text-emerald-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Income Source (e.g., Salary, Freelance)
                            </label>
                            <div class="absolute right-0 top-4 text-gray-400">
                                <x-heroicon-o-tag class="h-5 w-5" />
                            </div>
                        </div>
                        @error('source')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="form-field relative z-0" style="--delay: 2">
                        <div class="relative z-0 floating-input">
                            <input type="text" name="category" id="category" 
                                   class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-emerald-500 peer"
                                   value="{{ old('category', $income->category) }}" 
                                   placeholder=" " 
                                   list="category-suggestions"
                                   autocomplete="off">
                            <label for="category" class="absolute text-gray-500 duration-300 transform -translate-y-6 scale-75 top-4 z-10 origin-[0] peer-focus:left-0 peer-focus:text-emerald-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Category (e.g., Salary, Freelance, Bonus)
                            </label>
                            <div class="absolute right-0 top-4 text-gray-400">
                                <x-heroicon-o-tag class="h-5 w-5" />
                            </div>
                            <datalist id="category-suggestions">
                                @if(isset($categorySuggestions) && is_array($categorySuggestions))
                                    @foreach($categorySuggestions as $suggestion)
                                        <option value="{{ $suggestion }}">
                                    @endforeach
                                @endif
                            </datalist>
                        </div>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div class="form-field relative z-0" style="--delay: 3"
                         x-data="amountField()"
                         x-init="init({{ $income->amount ?: 0 }})">
                        <div class="relative z-0">
                            <input type="text" 
                                   id="amount" 
                                   x-model="displayValue"
                                   @input="onInput($event)"
                                   @blur="onBlur()"
                                   @keydown="handleKeyDown($event)"
                                   class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-emerald-500 peer text-xl font-semibold"
                                   placeholder=" " 
                                   required>
                            <label for="amount" class="absolute text-gray-500 duration-300 transform -translate-y-6 scale-75 top-4 z-10 origin-[0] peer-focus:left-0 peer-focus:text-emerald-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Amount
                            </label>
                            <div class="absolute right-0 top-4 flex items-center">
                                <span class="text-gray-500 mr-2">USD</span>
                                <x-heroicon-o-currency-dollar class="h-5 w-5 text-emerald-500" />
                            </div>
                            <input type="hidden" name="amount" :value="rawValue">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="form-field relative z-0" style="--delay: 4">
                        <div class="relative z-0 floating-input">
                            <input type="date" name="date" id="date" 
                                class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-emerald-500 peer"
                                value="{{ old('date', $income->date ? $income->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            <label for="date" class="absolute text-gray-500 duration-300 transform -translate-y-6 scale-75 top-4 z-10 origin-[0] peer-focus:left-0 peer-focus:text-emerald-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Transaction Date
                            </label>
                            <div class="absolute right-0 top-4 text-gray-400">
                                <x-heroicon-o-calendar-days class="h-5 w-5" />
                            </div>
                        </div>
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Recurring Income -->
                    <div class="form-field md:col-span-2" style="--delay: 5">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <input type="checkbox" id="is_recurring" name="is_recurring" value="1"
                                {{ old('is_recurring', $income->is_recurring) ? 'checked' : '' }}
                                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded transition-all duration-200">
                            <label for="is_recurring" class="ml-3 block text-sm font-medium text-gray-700">
                                This is a recurring income
                            </label>
                        </div>
                    </div>

                    <!-- Recurring Options (Conditional) -->
                    <div id="recurring-options" class="md:col-span-2 space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200 transition-all duration-300" 
                         style="display: {{ old('is_recurring', $income->is_recurring) ? 'block' : 'none' }}; --delay: 6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-field relative z-0">
                                <div class="relative z-0 floating-input">
                                    <select id="recurring_interval" name="recurring_interval" 
                                            class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-emerald-500 peer">
                                        @foreach ($recurringIntervals as $value => $label)
                                            <option value="{{ $value }}" {{ old('recurring_interval', $income->recurring_interval) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="recurring_interval" class="absolute text-gray-500 duration-300 transform -translate-y-6 scale-75 top-4 z-10 origin-[0] peer-focus:left-0 peer-focus:text-emerald-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                        Recurring Interval
                                    </label>
                                </div>
                            </div>
                            <div class="form-field relative z-0">
                                <div class="relative z-0 floating-input">
                                    <input type="date" id="recurring_end_date" name="recurring_end_date"
                                        value="{{ old('recurring_end_date', $income->recurring_end_date ? $income->recurring_end_date->format('Y-m-d') : '') }}"
                                        class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-emerald-500 peer"
                                        placeholder=" ">
                                    <label for="recurring_end_date" class="absolute text-gray-500 duration-300 transform -translate-y-6 scale-75 top-4 z-10 origin-[0] peer-focus:left-0 peer-focus:text-emerald-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                        End Date (Optional)
                                    </label>
                                    <div class="absolute right-0 top-4 text-gray-400">
                                        <x-heroicon-o-calendar-days class="h-5 w-5" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="form-field md:col-span-2" style="--delay: 6">
                        <div class="relative z-0 floating-input">
                            <textarea id="notes" name="notes" rows="3"
                                class="block w-full px-0 pt-4 pb-2 text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-emerald-500 peer"
                                placeholder=" ">{{ old('notes', $income->notes) }}</textarea>
                            <label for="notes" class="absolute text-gray-500 duration-300 transform -translate-y-6 scale-75 top-4 z-10 origin-[0] peer-focus:left-0 peer-focus:text-emerald-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                Additional Notes (Optional)
                            </label>
                            <div class="absolute right-0 top-4 text-gray-400">
                                <x-heroicon-o-document-text class="h-5 w-5" />
                            </div>
                        </div>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-8 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 form-section">
                    <a href="{{ route('incomes.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:scale-[1.02]">
                        <x-heroicon-o-arrow-uturn-left class="h-5 w-5 mr-2" />
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:scale-[1.02] hover:shadow-md">
                        <x-heroicon-o-plus-circle class="h-5 w-5 mr-2" />
                        {{ $income->exists ? 'Update Income' : 'Add Income' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Background Decoration -->
    <div class="fixed inset-0 -z-10 overflow-hidden opacity-10">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-100 to-teal-200"></div>
    </div>
    </div>
</div>

@push('scripts')
    <script>
        // Toggle recurring fields
        document.addEventListener('DOMContentLoaded', function() {
            const recurringCheckbox = document.getElementById('is_recurring');
            const recurringOptions = document.getElementById('recurring-options');
            
            if (recurringCheckbox && recurringOptions) {
                recurringCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        recurringOptions.style.display = 'block';
                        // Trigger animation
                        recurringOptions.classList.add('animate-slide-in');
                    } else {
                        recurringOptions.style.display = 'none';
                    }
                });
            }
        });

        // Amount field AlpineJS component
        function amountField() {
            return {
                rawValue: '0.00',
                displayValue: '',
                isFormatting: false,

                init(initialAmount) {
                    // Parse the initial amount if provided
                    if (initialAmount !== null && initialAmount !== '' && initialAmount !== 0) {
                        const amount = parseFloat(initialAmount);
                        if (!isNaN(amount)) {
                            this.rawValue = amount.toString();
                            this.displayValue = this.addThousandSeparators(amount.toFixed(2));
                            console.log('Initialized with amount:', {
                                initialAmount,
                                rawValue: this.rawValue,
                                displayValue: this.displayValue
                            });
                            return;
                        }
                    }
                    console.log('No valid initial amount provided');
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
                    } else if (decimalSplit.length === 2) {
                        // Limit to 2 decimal places
                        decimalSplit[1] = decimalSplit[1].substring(0, 2);
                        raw = decimalSplit.join('.');
                    }

                    // Store the raw value (e.g., "1234.56")
                    this.rawValue = raw || '0.00';
                    
                    // Format the display value with thousand separators
                    this.displayValue = this.addThousandSeparators(raw);
                    
                    // Restore cursor position after formatting
                    this.$nextTick(() => {
                        // Calculate new cursor position
                        let newPosition = cursorPosition;
                        const diff = this.displayValue.length - inputValue.length;
                        
                        // If we added characters (like commas), adjust the cursor position
                        if (diff > 0) {
                            newPosition += diff;
                        } else if (diff < 0) {
                            // If we removed characters, don't let cursor go before start
                            newPosition = Math.max(0, newPosition + diff);
                        }
                        
                        event.target.setSelectionRange(newPosition, newPosition);
                    });
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
                    // Allow: backspace, delete, tab, escape, enter, decimal point, numbers, keypad numbers
                    if ([46, 8, 9, 27, 13, 110, 190].includes(event.keyCode) ||
                        // Allow: Ctrl+A, Ctrl+C, Ctrl+X
                        (event.keyCode === 65 && (event.ctrlKey || event.metaKey)) ||
                        (event.keyCode === 67 && (event.ctrlKey || event.metaKey)) ||
                        (event.keyCode === 88 && (event.ctrlKey || event.metaKey)) ||
                        // Allow: home, end, left, right
                        (event.keyCode >= 35 && event.keyCode <= 39) ||
                        // Allow numbers and keypad numbers
                        (event.keyCode >= 48 && event.keyCode <= 57) ||
                        (event.keyCode >= 96 && event.keyCode <= 105)) {
                        // Let it happen, don't do anything
                        return;
                    }
                    
                    // Ensure that it is a number and stop the keypress
                    if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {
                        event.preventDefault();
                    }
                }
            };
        }
    </script>
@endpush