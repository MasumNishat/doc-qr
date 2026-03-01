<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Display the dashboard.
     */
    public function dashboard(): View
    {
        $documents = Document::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('dashboard', compact('documents'));
    }
}
