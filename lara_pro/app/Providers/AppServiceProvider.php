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
            ];

            if ($email !== '') {
                $limits[] = Limit::perHour(8)->by('contact-email:'.hash('sha256', $email));
                $limits[] = Limit::perDay(20)->by('contact-email-day:'.hash('sha256', $email));
            }

            return $limits;
        });
    }
}
