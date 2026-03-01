<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md" x-data="{ showPassword: false }">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                <p class="text-gray-600">Please sign in to your account</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <ul class="list-none space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror"
                        placeholder="you@example.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            required
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror"
                            placeholder="Enter your password"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-colors"
                        >
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Sign In
                </button>
            </form>
        </div>

        <div class="text-center mt-6 text-sm text-gray-600">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
