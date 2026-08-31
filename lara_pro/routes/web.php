<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminLuxuryQuoteController;
use App\Http\Controllers\AdminLetterController;
use App\Http\Controllers\AdminProposalController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminProjectController;
use App\Http\Controllers\AdminProjectManagementController;
use App\Http\Controllers\ProjectManagementApiController;
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
Route::get('/shared/project-files/{projectFile:share_token}', [AdminProjectController::class, 'sharedFile'])
    ->name('project-files.share');
Route::get('/shared/project-files/{projectFile:share_token}/download', [AdminProjectController::class, 'downloadSharedFile'])
    ->name('project-files.download');

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
                        : (AdminAccess::can('projects')
                            ? (AdminAccess::isFullAdmin() || AdminAccess::can('project-management')
                                ? 'admin.project-management.dashboard'
                                : 'admin.project-management.projects')
                            : (AdminAccess::can('letters') ? 'admin.letters.create' : 'admin.profile'))));

            return redirect()->route($route);
        })->name('home');
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');

        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

        Route::middleware('admin.permission:projects')->prefix('projects')->name('projects.')->group(function () {
            Route::get('/', [AdminProjectController::class, 'index'])->name('index');
            Route::get('/{project}', [AdminProjectController::class, 'show'])->name('show');
            Route::get('/files/{projectFile}/download', [AdminProjectController::class, 'downloadFile'])->name('files.download');
            Route::get('/files/{projectFile}/preview', [AdminProjectController::class, 'previewFile'])->name('files.preview');

            Route::middleware('admin.permission:project-files')->group(function () {
                Route::post('/files', [AdminProjectController::class, 'storeExternalFile'])->name('files.external.store');
                Route::post('/{project}/files', [AdminProjectController::class, 'storeFile'])->name('files.store');
                Route::put('/files/{projectFile}', [AdminProjectController::class, 'updateFile'])->name('files.update');
                Route::post('/files/{projectFile}/share', [AdminProjectController::class, 'toggleShare'])->name('files.share');
                Route::delete('/files/{projectFile}', [AdminProjectController::class, 'destroyFile'])->name('files.destroy');
            });
        });

        Route::middleware('admin.permission:projects')->prefix('project-management')->name('project-management.')->group(function () {
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/projects', [ProjectManagementApiController::class, 'projects'])->name('projects');
                Route::post('/projects', [ProjectManagementApiController::class, 'storeProject'])->name('projects.store');
                Route::get('/projects/{project}', [ProjectManagementApiController::class, 'project'])->name('projects.show');
                Route::get('/projects/{project}/members', [ProjectManagementApiController::class, 'members'])->name('members');
                Route::post('/projects/{project}/members', [ProjectManagementApiController::class, 'addMember'])->name('members.store');
                Route::delete('/projects/{project}/members/{user}', [ProjectManagementApiController::class, 'removeMember'])->name('members.destroy');
                Route::get('/projects/{project}/columns', [ProjectManagementApiController::class, 'columns'])->name('columns');
                Route::post('/projects/{project}/columns', [ProjectManagementApiController::class, 'storeColumn'])->name('columns.store');
                Route::put('/columns/{column}', [ProjectManagementApiController::class, 'updateColumn'])->name('columns.update');
                Route::delete('/columns/{column}', [ProjectManagementApiController::class, 'deleteColumn'])->name('columns.destroy');
                Route::get('/projects/{project}/comments', [ProjectManagementApiController::class, 'comments'])->name('comments');
                Route::post('/projects/{project}/comments', [ProjectManagementApiController::class, 'storeComment'])->name('comments.store');
                Route::get('/projects/{project}/tasks/{task}/comments', [ProjectManagementApiController::class, 'comments'])->name('tasks.comments');
                Route::post('/projects/{project}/tasks/{task}/comments', [ProjectManagementApiController::class, 'storeComment'])->name('tasks.comments.store');
                Route::post('/projects/{project}/tasks', [ProjectManagementApiController::class, 'storeTask'])->name('tasks.store');
                Route::post('/projects/{project}/milestones', [ProjectManagementApiController::class, 'storeMilestone'])->name('milestones.store');
                Route::post('/projects/{project}/sprints', [ProjectManagementApiController::class, 'storeSprint'])->name('sprints.store');
                Route::get('/projects/{project}/activity', [ProjectManagementApiController::class, 'activity'])->name('activity');
                Route::patch('/tasks/{task}', [ProjectManagementApiController::class, 'updateTask'])->name('tasks.update');
                Route::delete('/tasks/{task}', [ProjectManagementApiController::class, 'deleteTask'])->name('tasks.destroy');
                Route::patch('/tasks/{task}/move', [ProjectManagementApiController::class, 'moveTask'])->name('tasks.move');
                Route::patch('/tasks/{task}/complete', [ProjectManagementApiController::class, 'completeTask'])->name('tasks.complete');
                Route::patch('/tasks/{task}/reopen', [ProjectManagementApiController::class, 'reopenTask'])->name('tasks.reopen');
                Route::post('/tasks/{task}/time', [ProjectManagementApiController::class, 'storeTimeEntry'])->name('tasks.time.store');
                Route::post('/tasks/{task}/checklists', [ProjectManagementApiController::class, 'storeChecklist'])->name('tasks.checklists.store');
                Route::post('/tasks/{task}/checklist-items', [ProjectManagementApiController::class, 'storeChecklistItem'])->name('tasks.checklist-items.store');
                Route::get('/tasks/{task}/attachments', [ProjectManagementApiController::class, 'attachments'])->name('tasks.attachments');
                Route::post('/tasks/{task}/attachments', [ProjectManagementApiController::class, 'storeAttachment'])->name('tasks.attachments.store');
                Route::get('/notifications', [ProjectManagementApiController::class, 'notifications'])->name('notifications');
                Route::patch('/notifications/{notification}/read', [ProjectManagementApiController::class, 'markNotification'])->name('notifications.read');
                Route::get('/search', [ProjectManagementApiController::class, 'search'])->name('search');
                Route::get('/reports', [ProjectManagementApiController::class, 'reports'])->name('reports');
            });
            Route::get('/', [AdminProjectManagementController::class, 'dashboard'])->name('dashboard');
            Route::get('/projects', [AdminProjectManagementController::class, 'projects'])->name('projects');
            Route::get('/projects/create', [AdminProjectManagementController::class, 'createProject'])->name('projects.create');
            Route::post('/projects', [AdminProjectManagementController::class, 'storeProject'])->name('projects.store');
            Route::get('/projects/archived', [AdminProjectManagementController::class, 'projects'])->name('archived');
            Route::get('/projects/{project}', [AdminProjectManagementController::class, 'showProject'])->name('projects.show');
            Route::put('/projects/{project}', [AdminProjectManagementController::class, 'updateProject'])->name('projects.update');
            Route::patch('/projects/{project}/archive', [AdminProjectManagementController::class, 'archiveProject'])->name('projects.archive');
            Route::patch('/projects/{project}/restore', [AdminProjectManagementController::class, 'restoreProject'])->name('projects.restore');
            Route::delete('/projects/{project}', [AdminProjectManagementController::class, 'destroyProject'])->name('projects.destroy');
            Route::get('/projects/{project}/board', [AdminProjectManagementController::class, 'board'])->name('board');
            Route::get('/projects/{project}/backlog', [AdminProjectManagementController::class, 'backlog'])->name('backlog');
            Route::get('/projects/{project}/sprints', [AdminProjectManagementController::class, 'sprints'])->name('sprints');
            Route::get('/projects/{project}/settings', [AdminProjectManagementController::class, 'settings'])->name('settings');
            Route::get('/calendar', [AdminProjectManagementController::class, 'calendar'])->name('calendar');
            Route::get('/team', [AdminProjectManagementController::class, 'team'])->name('team');
            Route::get('/reports', [AdminProjectManagementController::class, 'reports'])->name('reports');
            Route::get('/search', [AdminProjectManagementController::class, 'search'])->name('search');
            Route::get('/notifications', [AdminProjectManagementController::class, 'notifications'])->name('notifications');
            Route::post('/filters', [AdminProjectManagementController::class, 'storeSavedFilter'])->name('filters.store');
            Route::delete('/filters/{savedFilter}', [AdminProjectManagementController::class, 'destroySavedFilter'])->name('filters.destroy');
            Route::patch('/notifications/{notification}/read', [AdminProjectManagementController::class, 'markNotification'])->name('notifications.read');

            Route::post('/projects/{project}/tasks', [AdminProjectManagementController::class, 'storeTask'])->name('tasks.store');
            Route::get('/tasks/{task}', [AdminProjectManagementController::class, 'task'])->name('tasks.show');
            Route::put('/tasks/{task}', [AdminProjectManagementController::class, 'updateTask'])->name('tasks.update');
            Route::delete('/tasks/{task}', [AdminProjectManagementController::class, 'destroyTask'])->name('tasks.destroy');
            Route::patch('/tasks/{task}/move', [AdminProjectManagementController::class, 'moveTask'])->name('tasks.move');
            Route::patch('/tasks/{task}/complete', [AdminProjectManagementController::class, 'completeTask'])->name('tasks.complete');
            Route::patch('/tasks/{task}/reopen', [AdminProjectManagementController::class, 'reopenTask'])->name('tasks.reopen');
            Route::patch('/tasks/{task}/sprint', [AdminProjectManagementController::class, 'assignTaskSprint'])->name('tasks.sprint');
            Route::post('/projects/{project}/tasks/{task}/comments', [AdminProjectManagementController::class, 'storeComment'])->name('tasks.comments.store');
            Route::post('/tasks/{task}/time', [AdminProjectManagementController::class, 'storeTimeEntry'])->name('tasks.time.store');
            Route::post('/tasks/{task}/timer/start', [AdminProjectManagementController::class, 'startTimer'])->name('tasks.timer.start');
            Route::post('/tasks/{task}/timer/stop', [AdminProjectManagementController::class, 'stopTimer'])->name('tasks.timer.stop');
            Route::post('/tasks/{task}/attachments', [AdminProjectManagementController::class, 'storeAttachment'])->name('tasks.attachments.store');
            Route::post('/tasks/{task}/checklists', [AdminProjectManagementController::class, 'storeChecklist'])->name('tasks.checklists.store');
            Route::post('/tasks/{task}/checklist-items', [AdminProjectManagementController::class, 'storeChecklistItem'])->name('tasks.checklist-items.store');
            Route::patch('/checklist-items/{item}', [AdminProjectManagementController::class, 'toggleChecklistItem'])->name('checklist-items.toggle');
            Route::get('/attachments/{attachment}/download', [AdminProjectManagementController::class, 'downloadAttachment'])->name('attachments.download');

            Route::post('/projects/{project}/columns', [AdminProjectManagementController::class, 'storeColumn'])->name('columns.store');
            Route::put('/columns/{column}', [AdminProjectManagementController::class, 'updateColumn'])->name('columns.update');
            Route::delete('/columns/{column}', [AdminProjectManagementController::class, 'deleteColumn'])->name('columns.destroy');
            Route::post('/projects/{project}/labels', [AdminProjectManagementController::class, 'storeLabel'])->name('labels.store');
            Route::post('/projects/{project}/milestones', [AdminProjectManagementController::class, 'storeMilestone'])->name('milestones.store');
            Route::post('/projects/{project}/sprints', [AdminProjectManagementController::class, 'storeSprint'])->name('sprints.store');
            Route::patch('/sprints/{sprint}/start', [AdminProjectManagementController::class, 'startSprint'])->name('sprints.start');
            Route::patch('/sprints/{sprint}/complete', [AdminProjectManagementController::class, 'completeSprint'])->name('sprints.complete');
            Route::post('/projects/{project}/members', [AdminProjectManagementController::class, 'storeMember'])->name('members.store');
            Route::delete('/projects/{project}/members/{user}', [AdminProjectManagementController::class, 'removeMember'])->name('members.destroy');
            Route::post('/projects/{project}/comments', [AdminProjectManagementController::class, 'storeComment'])->name('comments.store');
        });

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
                ->name('quotes.edit');
            Route::put('/quotes/{luxuryQuote}', [AdminLuxuryQuoteController::class, 'update'])
                ->name('quotes.update');
            Route::get('/quotes/{luxuryQuote}', [AdminLuxuryQuoteController::class, 'show'])
                ->name('quotes.show');
            Route::get('/quotes/{luxuryQuote}/pdf', [AdminLuxuryQuoteController::class, 'downloadPdf'])
                ->name('quotes.pdf');
            Route::get('/quotes/{luxuryQuote}/mou', [AdminLuxuryQuoteController::class, 'downloadMouPdf'])
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
