<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        if (!Auth::attempt($validated, $request->boolean('remember'))) {
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

    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = strtolower(trim((string) $validated['email']));
        $user = User::where('email', $email)->first();
        $configuredPhone = $this->normalizePhone(Setting::get('company_phone', ''));
        $submittedPhone = $this->normalizePhone($validated['phone']);

        if ($configuredPhone === '') {
            return back()
                ->withInput($request->only('email', 'phone'))
                ->withErrors(['phone' => 'Admin forgot password phone is not configured in Store Settings.']);
        }

        if (! $user || ! $user->isAdmin() || $submittedPhone !== $configuredPhone) {
            return back()
                ->withInput($request->only('email', 'phone'))
                ->withErrors(['email' => 'Admin email and phone number do not match.']);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route('admin.login')->with('status', 'Password changed successfully. Please login with your new password.');
    }

    private function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone) ?? '';
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
