<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Budget Planner</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-lg shadow-md">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">Create an account</h2>
            <p class="mt-2 text-sm text-gray-600">Start managing your budget today</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-700">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('name') }}">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('email') }}">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Account
                </button>
            </div>
        </form>
        
        <div class="text-center text-sm">
            <p class="text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</body>
</html>
