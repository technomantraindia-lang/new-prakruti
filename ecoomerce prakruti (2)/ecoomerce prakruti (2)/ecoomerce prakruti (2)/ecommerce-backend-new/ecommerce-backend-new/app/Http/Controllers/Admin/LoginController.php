<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show the Admin login page.
     */
    public function showLoginForm()
    {
        return response()
            ->view('admin.auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    /**
     * Send removed storefront routes back to the backend login screen.
     */
    public function redirectToLogin()
    {
        return redirect()->route('admin.login');
    }

    /**
     * Process the Admin login.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($validated['email']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return redirect()
                ->back()
                ->withInput($request->only('email'))
                ->with('error', "Too many login attempts. Please try again in {$seconds} seconds.");
        }

        // Check email and password.
        if (!Auth::attempt($validated)) {
            RateLimiter::hit($throttleKey, 60);

            return redirect()
                ->back()
                ->withInput($request->only('email'))
                ->with('error', 'Invalid credentials');
        }

        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            \App\Services\ActivityLogService::log('login', 'auth', "Admin user logged in ({$user->email})", $user);
            return redirect()->route('admin.dashboard');
        }

        RateLimiter::hit($throttleKey, 60);

        // Log out users who are not active Admin users.
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->back()
            ->withInput($request->only('email'))
            ->with('error', 'Unauthorized access');
    }

    /**
     * Log the Admin out.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            \App\Services\ActivityLogService::log('logout', 'auth', "Admin user logged out ({$user->email})", $user);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
