<?php

namespace App\Providers;

use Barryvdh\DomPDF\ServiceProvider as DomPdfServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! $this->app->bound('dompdf.wrapper')) {
            $this->app->register(DomPdfServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        RateLimiter::for('contact', function (Request $request): array {
            $email = Str::lower(trim((string) $request->input('email')));

            $limits = [
                Limit::perMinute(5)->by('contact-ip:'.hash('sha256', (string) $request->ip())),
                Limit::perDay(25)->by('contact-ip-day:'.hash('sha256', (string) $request->ip())),
                // A site-wide ceiling protects the SMTP account from distributed bots.
                Limit::perHour(100)->by('contact-site-hourly'),
                Limit::perDay(500)->by('contact-site-daily'),
            ];

            if ($email !== '') {
                $limits[] = Limit::perHour(8)->by('contact-email:'.hash('sha256', $email));
                $limits[] = Limit::perDay(20)->by('contact-email-day:'.hash('sha256', $email));
            }

            return $limits;
        });

        RateLimiter::for('admin-login', function (Request $request): array {
            $email = Str::lower(trim((string) $request->input('email')));
            $maxAttempts = max(1, (int) config('luxury-quotes.admin.login_attempts', 3));
            $decayMinutes = max(1, (int) config('luxury-quotes.admin.login_decay_minutes', 1));
            $lockoutResponse = static function (Request $request, array $headers) {
                return redirect()
                    ->route('admin.login')
                    ->withErrors([
                        'email' => 'Too many sign-in attempts. Please wait a minute before trying again.',
                    ])
                    ->withInput($request->only('email'))
                    ->withHeaders($headers);
            };

            $limits = [
                Limit::perMinutes($decayMinutes, $maxAttempts)
                    ->by('admin-login-ip:'.hash('sha256', (string) $request->ip()))
                    ->response($lockoutResponse),
            ];

            if ($email !== '') {
                $limits[] = Limit::perMinutes($decayMinutes, $maxAttempts)
                    ->by('admin-login-email:'.hash('sha256', $email))
                    ->response($lockoutResponse);
            }

            return $limits;
        });
    }
}
