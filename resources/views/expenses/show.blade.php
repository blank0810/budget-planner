<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expense Details') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    {{ $expense->description }}
                                </h3>
                                <div class="flex space-x-3">
                                    <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Added on {{ $expense->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                            <dl class="sm:divide-y sm:divide-gray-200">
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Amount
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="text-red-600 font-semibold">-${{ number_format($expense->amount, 2) }}</span>
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Category
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $expense->category }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Payment Method
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="capitalize">{{ str_replace('_', ' ', $expense->payment_method) }}</span>
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Date
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $expense->date->format('F j, Y') }}
                                    </dd>
                                </div>
                                @if($expense->receipt_path)
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">
                                            Receipt
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">
                                                <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View Receipt
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                                @if($expense->is_recurring)
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">
                                            Recurring
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($expense->recurring_interval) }}
                                            </span>
                                            @if($expense->next_recurring_date)
                                                <p class="mt-1 text-sm text-gray-500">
                                                    Next occurrence: {{ $expense->next_recurring_date->format('F j, Y') }}
                                                </p>
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                                @if($expense->notes)
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">
                                            Notes
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">
                                            {{ $expense->notes }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('expenses.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Back to Expenses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
