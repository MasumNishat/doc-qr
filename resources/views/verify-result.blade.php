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
        <h2 class="text-xl font-semibold mb-6">Verify a report</h2>

        <div class="border border-gray-300 rounded mb-6 overflow-hidden">
            <table class="w-full">
                <tbody>
                    <tr class="border-b border-gray-300 text-2xl">
                        <td class="px-4 py-3 bg-gray-50 font-medium text-gray-700 w-48">Date:</td>
                        <td class="px-4 py-3">{{ $document->date->format('Y-m-d') }}</td>
                    </tr>
                    <tr class="border-b border-gray-300 text-2xl">
                        <td class="px-4 py-3 bg-gray-50 font-medium text-gray-700">CRTS No:</td>
                        <td class="px-4 py-3">{{ $document->crts_no }}</td>
                    </tr>
                    <tr class="border-b border-gray-300 text-2xl">
                        <td class="px-4 py-3 bg-gray-50 font-medium text-gray-700">Test:</td>
                        <td class="px-4 py-3">{{ $document->original_filename }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 bg-gray-50 font-medium text-gray-700 text-2xl">File:</td>
                        <td class="px-4 py-3">
                            <a
                                href="{{ route('verify.download', $document->verification_code) }}"
                                class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                            >
                                Download
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a
            href="{{ route('verify') }}"
            class="inline-block px-6 py-2 bg-yellow-400 text-gray-900 rounded hover:bg-yellow-500 font-medium"
        >
            Back
        </a>

        <!-- Document Preview -->
{{--        @if ($document->page_count > 0)--}}
{{--            <div class="mt-8 space-y-6">--}}
{{--                @for ($i = 1; $i <= $document->page_count; $i++)--}}
{{--                    <div class="border border-gray-300 rounded overflow-hidden">--}}
{{--                        <img--}}
{{--                            src="{{ route('verify.page', [$document->verification_code, $i]) }}"--}}
{{--                            alt="Page {{ $i }}"--}}
{{--                            class="w-full h-auto"--}}
{{--                        >--}}
{{--                    </div>--}}
{{--                @endfor--}}
{{--            </div>--}}
{{--        @endif--}}
    </main>
</body>
</html>
