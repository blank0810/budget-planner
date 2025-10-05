<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Show the settings dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)
            ->orderBy('name')
            ->get();
            
        return view('settings.index', [
            'user' => $user,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->update($validated);

        return redirect()->route('settings')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('settings')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Store a newly created category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'size:7'],
        ]);

        $validated['user_id'] = $request->user()->id;
        
        Category::create($validated);

        return redirect()->route('settings')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Update the specified category.
     */
    public function updateCategory(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'size:7'],
        ]);

        $category->update($validated);

        return redirect()->route('settings')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroyCategory(Category $category)
    {
        $this->authorize('delete', $category);
        
        // Check if category is in use
        if ($category->incomes()->exists() || $category->expenses()->exists()) {
            return redirect()->route('settings')
                ->with('error', 'Cannot delete category that is in use.');
        }

        $category->delete();

        return redirect()->route('settings')
            ->with('success', 'Category deleted successfully.');
    }
}
