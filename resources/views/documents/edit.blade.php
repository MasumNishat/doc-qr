<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Document - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <div>
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('documents.show', $document) }}" class="text-indigo-600 hover:text-indigo-800 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900">Edit Document</h1>
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
        <main class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Document Info Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if ($document->file_type === 'pdf')
                                <div class="h-12 w-12 rounded-lg bg-red-100 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @else
                                <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $document->original_filename }}</h2>
                            <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <span class="font-medium">Type:</span>
                                    <span class="ml-1 uppercase">{{ $document->file_type }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium">Pages:</span>
                                    <span class="ml-1">{{ $document->page_count }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium">Uploaded:</span>
                                    <span class="ml-1">{{ $document->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Update Verification Code</h3>

                    <form method="POST" action="{{ route('documents.update', $document) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Verification Code <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="verification_code"
                                name="verification_code"
                                value="{{ old('verification_code', $document->verification_code) }}"
                                maxlength="10"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-lg @error('verification_code') border-red-500 @enderror"
                                placeholder="Enter 10-digit code"
                            >
                            <p class="mt-2 text-sm text-gray-500">
                                Must be exactly 10 characters and unique
                            </p>
                            @error('verification_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-yellow-700">
                                    <p class="font-medium">Important:</p>
                                    <p class="mt-1">Changing the verification code will update the storage folder name and may affect any existing references to this document.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a
                                href="{{ route('documents.show', $document) }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </a>
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors"
                            >
                                Update Verification Code
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Document Preview -->
                <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Document Preview</h3>
                    <div class="flex justify-center">
                        <img
                            src="{{ route('documents.page', [$document, 1]) }}"
                            alt="Document preview"
                            class="max-w-full h-auto rounded-lg shadow-md"
                            style="max-height: 400px;"
                        >
                    </div>
                    @if ($document->page_count > 1)
                        <p class="text-center text-sm text-gray-500 mt-3">
                            Showing page 1 of {{ $document->page_count }}
                        </p>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
