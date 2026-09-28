<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            if (method_exists($notifiable, 'isAdmin') && $notifiable->isAdmin()) {
                return url('/admin/reset-password/' . $token) . '?email=' . urlencode((string) $notifiable->email);
            }

            $frontendUrl = env('FRONTEND_URL') ?: (app()->environment('local') ? 'http://localhost:5173' : config('app.url'));

            return rtrim((string) $frontendUrl, '/') . '/#reset-password/' . $token . '?email=' . urlencode((string) $notifiable->email);
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
        });

        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });
    }
}
