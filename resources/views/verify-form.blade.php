<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify a Report - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    <!-- Header -->
    <header class="border-b border-gray-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">
                    <a href="{{ route('verify') }}" class="text-gray-900">{{ config('app.name') }}</a>
                </h1>
                <nav>
                    <ul class="flex space-x-6 text-sm">
                        <li><a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a></li>
                        <li><a href="{{ route('verify') }}" class="text-gray-900 font-medium">Verify a Report</a></li>
                        <li><a href="#" class="text-gray-700 hover:text-gray-900">Check Status</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 py-8">
        <div class="max-w-xl">
            <h2 class="text-2xl font-semibold mb-6">Verify a report</h2>

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form method="GET" action="{{ route('verify') }}">
                <div class="mb-4">
                    <label for="vcode" class="block text-sm font-medium text-gray-700 mb-2">
                        Verification Code No <span class="text-red-600">[*]</span>
                    </label>
                    <input
                        type="text"
                        id="vcode"
                        name="vcode"
                        required
                        maxlength="10"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        value="{{ old('vcode') }}"
                    >
                </div>

                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Verify
                </button>
            </form>
        </div>
    </main>
</body>
</html>
