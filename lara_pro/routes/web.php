<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminLuxuryQuoteController;
use App\Http\Controllers\AdminLetterController;
use App\Http\Controllers\AdminProposalController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminStaffContractController;
use App\Http\Controllers\AdminSubaccountController;
use App\Http\Controllers\ContactController;
use App\Http\Middleware\EnsureLuxuryQuoteAdminAuthenticated;
use App\Support\AdminAccess;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    $siteUrl = rtrim(config('seo.site_url'), '/');
    $pages = collect(config('seo.pages'))
        ->map(function (array $page) use ($siteUrl) {
            $viewPath = isset($page['view'])
                ? resource_path('views/'.str_replace('.', '/', $page['view']).'.blade.php')
                : null;
            $contentFiles = array_filter([
                $viewPath,
                config_path('seo.php'),
                str_starts_with($page['route'], 'services.') ? config_path('service-pages.php') : null,
                $page['route'] === 'contact.show' ? config_path('contact.php') : null,
                in_array($page['route'], ['privacy.show', 'terms.show'], true) ? config_path('legal.php') : null,
            ], fn ($path) => $path && is_file($path));
            $modifiedTimestamp = collect($contentFiles)
                ->map(fn ($path) => filemtime($path))
                ->filter()
                ->max();
            $modifiedAt = $modifiedTimestamp ? date('Y-m-d', $modifiedTimestamp) : null;

            return array_filter([
                'loc' => $siteUrl.'/'.ltrim(route($page['route'], [], false), '/'),
                'lastmod' => $modifiedAt,
                'changefreq' => $page['changefreq'] ?? 'monthly',
                'priority' => $page['priority'] ?? '0.7',
            ], fn ($value) => $value !== null);
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
Route::view('/service', 'services-overview')->name('service.show');
Route::redirect('/index.htm', '/', 301);
Route::redirect('/index.html', '/', 301);
Route::redirect('/service.html', '/service', 301);

Route::view('/single/web', 'service-detail')->name('services.web');
Route::view('/single/mobile', 'service-detail')->name('services.mobile');
Route::view('/single/saas', 'service-detail')->name('services.saas');
Route::view('/single/branding', 'service-detail')->name('services.branding');
Route::redirect('/single-service.html', '/single/web', 301);

// Preserve links from the previous static site without publishing duplicate pages.
Route::redirect('/index2.html', '/single/web', 301);
Route::redirect('/index3.html', '/single/mobile', 301);
Route::redirect('/index4.html', '/single/branding', 301);
Route::redirect('/about.html', '/#about', 301);
Route::redirect('/team.html', '/#about', 301);
Route::redirect('/team-details.html', '/#about', 301);
Route::redirect('/testimonial.html', '/#perspectives', 301);
Route::redirect('/pricing.html', '/service#service-pricing', 301);
Route::redirect('/portfolio.html', '/#work', 301);
Route::redirect('/portfolio-gallery.html', '/#work', 301);
Route::redirect('/portfolio-list.html', '/#work', 301);
Route::redirect('/portfolio-single.html', '/#work', 301);
Route::redirect('/faq.html', '/#faq', 301);

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::redirect('/contact.html', '/contact', 301);
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');
Route::get('/privacy', fn () => view('legal-page', ['legal' => config('legal.privacy')]))->name('privacy.show');
Route::get('/terms', fn () => view('legal-page', ['legal' => config('legal.terms')]))->name('terms.show');
Route::redirect('/privacy-policy', '/privacy', 301);
Route::redirect('/terms-of-service', '/terms', 301);
Route::redirect('/terms-and-conditions', '/terms', 301);
Route::get('/p/{token}', [AdminProposalController::class, 'share'])->name('proposals.share');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store'])
        ->middleware('throttle:admin-login')
        ->name('login.store');

    Route::middleware(EnsureLuxuryQuoteAdminAuthenticated::class)->group(function () {
        Route::get('/', function () {
            $route = AdminAccess::can('invoices')
                ? 'admin.quotes.index'
                : (AdminAccess::can('proposals')
                    ? 'admin.proposals.index'
                    : (AdminAccess::can('staff-contracts')
                        ? 'admin.staff-contracts.index'
                        : (AdminAccess::can('letters') ? 'admin.letters.create' : 'admin.profile')));

            return redirect()->route($route);
        })->name('home');
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');

        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

        Route::prefix('subaccounts')->name('subaccounts.')->middleware('admin.permission:subaccounts')->group(function () {
            Route::get('/', [AdminSubaccountController::class, 'index'])->name('index');
            Route::get('/create', [AdminSubaccountController::class, 'create'])->name('create');
            Route::post('/', [AdminSubaccountController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [AdminSubaccountController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminSubaccountController::class, 'update'])->name('update');
            Route::patch('/{user}/toggle', [AdminSubaccountController::class, 'toggle'])->name('toggle');
        });

        // Keep named quote sections ahead of the {luxuryQuote} wildcard so
        // values such as "activity" are not treated as invoice IDs.
        Route::get('/quotes/activity', [AdminLuxuryQuoteController::class, 'activity'])
            ->middleware('admin.permission:activity')
            ->name('quotes.activity');
        Route::get('/quotes/insights', [AdminLuxuryQuoteController::class, 'insights'])
            ->middleware('admin.permission:insights')
            ->name('quotes.insights');
        Route::get('/quotes/promotion', [AdminLuxuryQuoteController::class, 'promotion'])
            ->middleware('admin.permission:promotion')
            ->name('quotes.promotion');
        Route::post('/quotes/promotion', [AdminLuxuryQuoteController::class, 'updatePromotion'])
            ->middleware('admin.permission:promotion')
            ->name('quotes.promotion.update');

        Route::middleware('admin.permission:invoices')->group(function () {
            Route::get('/quotes', [AdminLuxuryQuoteController::class, 'index'])->name('quotes.index');
            Route::get('/quotes/create', [AdminLuxuryQuoteController::class, 'create'])->name('quotes.create');
            Route::post('/quotes', [AdminLuxuryQuoteController::class, 'store'])->name('quotes.store');
            Route::get('/quotes/archive', [AdminLuxuryQuoteController::class, 'archive'])
                ->middleware('admin.permission:archive')
                ->name('quotes.archive');
            Route::get('/quotes/{luxuryQuote}/edit', [AdminLuxuryQuoteController::class, 'edit'])
                ->whereNumber('luxuryQuote')
                ->name('quotes.edit');
            Route::put('/quotes/{luxuryQuote}', [AdminLuxuryQuoteController::class, 'update'])
                ->whereNumber('luxuryQuote')
                ->name('quotes.update');
            Route::get('/quotes/{luxuryQuote}', [AdminLuxuryQuoteController::class, 'show'])
                ->whereNumber('luxuryQuote')
                ->name('quotes.show');
            Route::get('/quotes/{luxuryQuote}/pdf', [AdminLuxuryQuoteController::class, 'downloadPdf'])
                ->whereNumber('luxuryQuote')
                ->name('quotes.pdf');
            Route::get('/quotes/{luxuryQuote}/mou', [AdminLuxuryQuoteController::class, 'downloadMouPdf'])
                ->whereNumber('luxuryQuote')
                ->name('quotes.mou');
        });

        Route::middleware('admin.permission:proposals')->group(function () {
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

        Route::middleware('admin.permission:staff-contracts')->group(function () {
            Route::get('/staff-contracts', [AdminStaffContractController::class, 'index'])->name('staff-contracts.index');
            Route::get('/staff-contracts/create', [AdminStaffContractController::class, 'create'])->name('staff-contracts.create');
            Route::post('/staff-contracts', [AdminStaffContractController::class, 'store'])->name('staff-contracts.store');
            Route::get('/staff-contracts/{staffContract}/edit', [AdminStaffContractController::class, 'edit'])->name('staff-contracts.edit');
            Route::put('/staff-contracts/{staffContract}', [AdminStaffContractController::class, 'update'])->name('staff-contracts.update');
            Route::get('/staff-contracts/{staffContract}/pdf', [AdminStaffContractController::class, 'downloadPdf'])->name('staff-contracts.pdf');
            Route::get('/staff-contracts/{staffContract}/signed-document/preview', [AdminStaffContractController::class, 'previewSignedDocument'])->name('staff-contracts.signed-document.preview');
            Route::get('/staff-contracts/{staffContract}/signed-document', [AdminStaffContractController::class, 'downloadSignedDocument'])->name('staff-contracts.signed-document');
            Route::get('/staff-contracts/{staffContract}', [AdminStaffContractController::class, 'show'])->name('staff-contracts.show');
        });

        Route::middleware('admin.permission:letters')->prefix('letters')->name('letters.')->group(function () {
            Route::get('/create', [AdminLetterController::class, 'create'])->name('create');
            Route::post('/pdf', [AdminLetterController::class, 'downloadPdf'])->name('pdf');
        });
    });
});
