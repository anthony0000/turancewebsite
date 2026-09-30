<?php

use App\Support\VisitCountryResolver;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('visits:resolve-countries {--limit=100}', function (VisitCountryResolver $resolver) {
    $resolved = $resolver->backfill((int) $this->option('limit'));
    $this->info("Resolved {$resolved} visitor IP addresses.");
})->purpose('Backfill countries for recorded website visits');

Schedule::command('visits:resolve-countries --limit=20')->hourly()->withoutOverlapping();

Schedule::command('db:backup')
    ->hourly()
    ->timezone((string) config('database-backup.timezone', 'Africa/Lagos'))
    ->withoutOverlapping(180)
    ->sendOutputTo(storage_path('logs/database-backup.log'))
    ->when(fn (): bool => (bool) config('database-backup.enabled'));
