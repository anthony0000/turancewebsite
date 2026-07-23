<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminLuxuryQuoteController;
use App\Http\Controllers\AdminProposalController;
use App\Http\Controllers\ContactController;
use App\Http\Middleware\EnsureLuxuryQuoteAdminAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    $siteUrl = rtrim(config('seo.site_url'), '/');
    $pages = collect(config('seo.pages'))
        ->map(function (array $page) use ($siteUrl) {
            return [
                'loc' => $siteUrl.'/'.ltrim(route($page['route'], [], false), '/'),
                'lastmod' => now()->toDateString(),
                'changefreq' => $page['changefreq'] ?? 'monthly',
                'priority' => $page['priority'] ?? '0.7',
            ];
        })
        ->unique('loc')
        ->values();

    return response()
        ->view('seo.sitemap', ['pages' => $pages])
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('seo.sitemap');

Route::get('/robots.txt', function () {
    return response()
        ->view('seo.robots')
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('seo.robots');

Route::get('/llms.txt', function () {
    return response()
        ->view('seo.llms')
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('seo.llms');

Route::view('/', 'index')->name('home');
Route::view('/service', 'service')->name('service.show');
Route::redirect('/index.htm', '/', 301);
Route::redirect('/index.html', '/', 301);
Route::redirect('/service.html', '/service', 301);

Route::view('/single/web', 'single-web')->name('services.web');
Route::view('/single/mobile', 'single-mobile')->name('services.mobile');
Route::view('/single/saas', 'single-saas')->name('services.saas');
Route::view('/single/branding', 'branding')->name('services.branding');
Route::redirect('/single-service.html', '/single/web', 301);

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::redirect('/contact.html', '/contact', 301);
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/p/{token}', [AdminProposalController::class, 'share'])->name('proposals.share');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store'])->name('login.store');

    Route::middleware(EnsureLuxuryQuoteAdminAuthenticated::class)->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.quotes.index');
        })->name('home');
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
        Route::get('/quotes', [AdminLuxuryQuoteController::class, 'index'])->name('quotes.index');
        Route::get('/quotes/activity', [AdminLuxuryQuoteController::class, 'activity'])->name('quotes.activity');
        Route::get('/quotes/insights', [AdminLuxuryQuoteController::class, 'insights'])->name('quotes.insights');
        Route::get('/quotes/create', [AdminLuxuryQuoteController::class, 'create'])->name('quotes.create');
        Route::get('/quotes/archive', [AdminLuxuryQuoteController::class, 'archive'])->name('quotes.archive');
        Route::post('/quotes', [AdminLuxuryQuoteController::class, 'store'])->name('quotes.store');
        Route::get('/quotes/{luxuryQuote}/edit', [AdminLuxuryQuoteController::class, 'edit'])->name('quotes.edit');
        Route::put('/quotes/{luxuryQuote}', [AdminLuxuryQuoteController::class, 'update'])->name('quotes.update');
        Route::get('/quotes/{luxuryQuote}', [AdminLuxuryQuoteController::class, 'show'])->name('quotes.show');
        Route::get('/quotes/{luxuryQuote}/pdf', [AdminLuxuryQuoteController::class, 'downloadPdf'])->name('quotes.pdf');
        Route::get('/quotes/{luxuryQuote}/mou', [AdminLuxuryQuoteController::class, 'downloadMouPdf'])->name('quotes.mou');

        Route::get('/proposals', [AdminProposalController::class, 'index'])->name('proposals.index');
        Route::post('/proposals', [AdminProposalController::class, 'store'])->name('proposals.store');
        Route::post('/proposals/ai/improve', [AdminProposalController::class, 'improve'])->name('proposals.ai.improve');
        Route::get('/proposals/{proposal}/edit', [AdminProposalController::class, 'edit'])->name('proposals.edit');
        Route::put('/proposals/{proposal}', [AdminProposalController::class, 'update'])->name('proposals.update');
        Route::post('/proposals/{proposal}/duplicate', [AdminProposalController::class, 'duplicate'])->name('proposals.duplicate');
        Route::delete('/proposals/{proposal}', [AdminProposalController::class, 'destroy'])->name('proposals.destroy');
        Route::get('/proposals/{proposal}', [AdminProposalController::class, 'show'])->name('proposals.show');
        Route::get('/proposals/{proposal}/pdf', [AdminProposalController::class, 'downloadPdf'])->name('proposals.pdf');
        Route::get('/proposals/{proposal}/word', [AdminProposalController::class, 'downloadWord'])->name('proposals.word');
        Route::get('/proposals/{proposal}/print', [AdminProposalController::class, 'print'])->name('proposals.print');
    });
});
