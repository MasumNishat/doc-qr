<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>View Document - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <div x-data="{ currentPage: 1, totalPages: {{ $document->page_count }} }">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900">View Document</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors"
                            >
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Document Info -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $document->original_filename }}</h2>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span class="font-semibold">Verification Code:</span>
                                    <span class="ml-1 px-2 py-1 bg-indigo-100 text-indigo-800 rounded font-mono text-xs">{{ $document->verification_code }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-semibold">CRTS No.:</span>
                                    <span class="ml-1">{{ $document->crts_no }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-semibold">Type:</span>
                                    <span class="ml-1 uppercase">{{ $document->file_type }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-semibold">Uploaded:</span>
                                    <span class="ml-1">{{ $document->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 flex gap-2">
                            <a href="{{ route('documents.edit', $document) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('documents.destroy', $document) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this document?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Page Navigation -->
                @if ($document->page_count > 1)
                    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <button
                                @click="currentPage = Math.max(1, currentPage - 1)"
                                :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>

                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700">
                                    Page <span x-text="currentPage" class="font-semibold"></span> of <span x-text="totalPages" class="font-semibold"></span>
                                </span>
                                <select
                                    x-model.number="currentPage"
                                    class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:ring-2 focus:ring-indigo-500"
                                >
                                    @for ($i = 1; $i <= $document->page_count; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <button
                                @click="currentPage = Math.min(totalPages, currentPage + 1)"
                                :disabled="currentPage === totalPages"
                                :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Document Viewer -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-center">
                        @for ($i = 1; $i <= $document->page_count; $i++)
                            <img
                                src="{{ route('documents.page', [$document, $i]) }}"
                                alt="Page {{ $i }}"
                                class="max-w-full h-auto rounded-lg shadow-lg"
                                x-show="currentPage === {{ $i }}"
                                @if ($i !== 1) style="display: none;" @endif
                            >
                        @endfor
                    </div>
                </div>

                <!-- Thumbnail Gallery (for multi-page documents) -->
                @if ($document->page_count > 1)
                    <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">All Pages</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @for ($i = 1; $i <= $document->page_count; $i++)
                                <div
                                    @click="currentPage = {{ $i }}; window.scrollTo({ top: 0, behavior: 'smooth' })"
                                    class="cursor-pointer border-2 rounded-lg overflow-hidden transition-all hover:shadow-md"
                                    :class="currentPage === {{ $i }} ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200'"
                                >
                                    <img
                                        src="{{ route('documents.page', [$document, $i]) }}"
                                        alt="Page {{ $i }}"
                                        class="w-full h-auto"
                                    >
                                    <div class="p-2 text-center text-xs font-medium" :class="currentPage === {{ $i }} ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-50 text-gray-600'">
                                        Page {{ $i }}
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
