@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Settings</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Profile Information</h2>
        <form action="{{ route('settings.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Profile
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Update Password</h2>
        <form action="{{ route('settings.password.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" name="current_password" id="current_password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="password" id="password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Password
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Categories</h2>
            <button type="button" onclick="document.getElementById('addCategoryModal').classList.remove('hidden')"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Add Category
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-4 w-4 rounded-full mr-2" style="background-color: {{ $category->color }}"></div>
                                    <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $category->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($category->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <input type="color" value="{{ $category->color }}" disabled class="h-6 w-10 p-0 border-0">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="editCategory({{ json_encode($category) }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <form action="{{ route('settings.categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                No categories found. Add your first category to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add New Category</h3>
            <form id="categoryForm" action="{{ route('settings.categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" name="name" id="name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                
                <div class="mb-4">
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" id="type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                    <input type="color" name="color" id="color" value="#3b82f6"
                        class="mt-1 block h-10 w-full p-0 border-0">
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" onclick="document.getElementById('addCategoryModal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal (initially hidden) -->
<div id="editCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Category</h3>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="edit_name" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" name="name" id="edit_name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                
                <div class="mb-4">
                    <label for="edit_type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" id="edit_type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="edit_color" class="block text-sm font-medium text-gray-700">Color</label>
                    <input type="color" name="color" id="edit_color" value="#3b82f6"
                        class="mt-1 block h-10 w-full p-0 border-0">
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" onclick="document.getElementById('editCategoryModal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editCategory(category) {
        const form = document.getElementById('editCategoryForm');
        form.action = `/settings/categories/${category.id}`;
        document.getElementById('edit_name').value = category.name;
        document.getElementById('edit_type').value = category.type;
        document.getElementById('edit_color').value = category.color;
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        const addModal = document.getElementById('addCategoryModal');
        const editModal = document.getElementById('editCategoryModal');
        
        if (event.target === addModal) {
            addModal.classList.add('hidden');
        }
        if (event.target === editModal) {
            editModal.classList.add('hidden');
        }
    }
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('addCategoryModal').classList.add('hidden');
            document.getElementById('editCategoryModal').classList.add('hidden');
        }
    });
</script>
@endpush

@endsection
