<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public verification routes
Route::get('/verify', [DocumentController::class, 'verify'])->name('verify');
Route::get('/verify/{verificationCode}/page/{page}', [DocumentController::class, 'servePublicPage'])->name('verify.page');
Route::get('/verify/{verificationCode}/download', [DocumentController::class, 'downloadPdf'])->name('verify.download');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Document routes
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/page/{page}', [DocumentController::class, 'servePage'])->name('documents.page');
});
