@props(['income', 'categories', 'recurringIntervals'])

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
        <!-- Source -->
        <div class="sm:col-span-3">
            <label for="source" class="block text-sm font-medium text-gray-700">Source</label>
            <div class="mt-1">
                <input type="text" name="source" id="source" value="{{ old('source', $income->source) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                @error('source')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Category -->
        <div class="sm:col-span-3">
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <div class="mt-1">
                <select id="category" name="category" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="" disabled {{ old('category', $income->category) ? '' : 'selected' }}>Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ old('category', $income->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Amount -->
        <div class="sm:col-span-3">
            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount', $income->amount) }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
            </div>
            @error('amount')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Date -->
        <div class="sm:col-span-3">
            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
            <div class="mt-1">
                <input type="date" name="date" id="date" value="{{ old('date', $income->date ? $income->date->format('Y-m-d') : '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                @error('date')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Recurring Toggle -->
        <div class="sm:col-span-6">
            <div class="flex items-center">
                <input type="checkbox" id="is_recurring" name="is_recurring" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('is_recurring', $income->is_recurring) ? 'checked' : '' }}>
                <label for="is_recurring" class="ml-2 block text-sm text-gray-700">
                    This is a recurring income
                </label>
            </div>
        </div>

        <!-- Recurring Interval (conditionally shown) -->
        <div id="recurring_fields" class="sm:col-span-6 {{ old('is_recurring', $income->is_recurring) ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="recurring_interval" class="block text-sm font-medium text-gray-700">Recurring Interval</label>
                    <div class="mt-1">
                        <select id="recurring_interval" name="recurring_interval" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option value="" disabled {{ old('recurring_interval', $income->recurring_interval) ? '' : 'selected' }}>Select an interval</option>
                            @foreach($recurringIntervals as $value => $label)
                                <option value="{{ $value }}" {{ old('recurring_interval', $income->recurring_interval) == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                        <input type="date" name="next_recurring_date" id="next_recurring_date" value="{{ old('next_recurring_date', $income->next_recurring_date ? $income->next_recurring_date->format('Y-m-d') : '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
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
                <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('notes', $income->notes) }}</textarea>
                @error('notes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="pt-5">
        <div class="flex justify-end">
            <a href="{{ route('incomes.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ $income->exists ? 'Update' : 'Create' }} Income
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
        
        if (isRecurring.checked && !nextRecurringDate.value) {
            const today = new Date().toISOString().split('T')[0];
            nextRecurringDate.value = today;
        }
    });
</script>
@endpush
