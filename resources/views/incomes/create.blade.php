<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Add New Income') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">Track your income by adding a new entry</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('incomes.store') }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                @include('incomes._form', [
                    'income' => $income,
                    'categories' => $categories,
                    'recurringIntervals' => $recurringIntervals
                ])
            </form>
        </div>
    </div>
</x-app-layout>
