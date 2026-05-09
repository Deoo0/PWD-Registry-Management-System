<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('username', 'password');
        $remember    = $request->boolean('remember');

        // Check credentials
        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Invalid username or password.');
        }

        // Block deactivated accounts
        if (! Auth::user()->is_active) {
            Auth::logout();
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}