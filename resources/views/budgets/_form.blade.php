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

        <!-- Amount -->
        <div class="sm:col-span-3">
            <label for="amount" class="block text-sm font-medium text-gray-700">Budget Amount</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount', $budget->amount) }}" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
            </div>
            @error('amount')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Year -->
        <div class="sm:col-span-3">
            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
            <div class="mt-1">
                <select id="year" name="year" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="" disabled {{ old('year', $budget->year) ? '' : 'selected' }}>Select a year</option>
                    @foreach($years as $value => $label)
                        <option value="{{ $value }}" {{ old('year', $budget->year) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
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
    </div>
</div>

@push('scripts')
<script>
    // Set current year and month as defaults if not editing
    document.addEventListener('DOMContentLoaded', function() {
        const yearSelect = document.getElementById('year');
        const monthSelect = document.getElementById('month');
        
        if (!yearSelect.value && !monthSelect.value) {
            const now = new Date();
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth() + 1; // JavaScript months are 0-indexed
            
            // Set current year and month as selected
            yearSelect.value = currentYear;
            monthSelect.value = currentMonth;
        }
    });
</script>
@endpush

