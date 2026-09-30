<?php

use App\Models\PageVisit;
use App\Support\VisitAnalytics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-30 18:00:00');
    $this->withSession([
        'luxury_quote_admin_authenticated' => true,
        'luxury_quote_admin_email' => 'analytics@example.com',
        'admin_role' => 'admin',
    ]);
});

afterEach(fn () => Carbon::setTestNow());

function analyticsVisit(array $attributes = []): PageVisit
{
    return PageVisit::query()->forceCreate(array_merge([
        'path' => '/contact', 'page_group' => 'Contact', 'route_name' => 'contact.show',
        'session_id' => 'session-private-a', 'ip_address' => '203.0.113.123',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0) Chrome/130.0 Safari/537.36',
        'created_at' => '2026-09-29 14:00:00', 'updated_at' => '2026-09-29 14:00:00',
    ], $attributes));
}

it('requires authentication and activity permission for the page and export', function () {
    $this->withSession(['luxury_quote_admin_authenticated' => false])
        ->get(route('admin.visits.index'))->assertRedirect(route('admin.login'));
    $this->get(route('admin.visits.export'))->assertRedirect(route('admin.login'));

    $this->withSession(['luxury_quote_admin_authenticated' => true, 'admin_role' => 'subaccount', 'admin_permissions' => ['invoices']]);
    $this->get(route('admin.visits.index'))->assertForbidden();
    $this->get(route('admin.visits.export'))->assertForbidden();

    $this->withSession(['admin_permissions' => ['activity']]);
    $this->get(route('admin.visits.index'))->assertOk()->assertSee('Website visits');
    $this->get(route('admin.visits.export'))->assertOk();
});

it('calculates periods, returning browsers, separate page paths and traffic breakdowns', function () {
    analyticsVisit(['path' => '/services/design', 'route_name' => 'service.show', 'page_group' => 'Services', 'created_at' => '2026-09-28 09:00:00', 'user_agent' => 'Mozilla/5.0 (iPhone) Version/18.0 Mobile Safari/605.1', 'referrer' => 'https://google.com/search?q=private']);
    analyticsVisit(['path' => '/services/development', 'route_name' => 'service.show', 'page_group' => 'Services', 'created_at' => '2026-09-28 10:00:00', 'referrer' => 'https://google.com/another?token=hidden']);
    analyticsVisit(['session_id' => 'browser-b', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0) Chrome/130.0 Safari/537.36 Edg/130.0']);
    analyticsVisit(['session_id' => 'browser-b']);
    analyticsVisit(['session_id' => null, 'user_agent' => null, 'created_at' => '2026-09-30 08:00:00']);
    analyticsVisit(['session_id' => 'bot-c', 'path' => '/', 'created_at' => '2026-09-30 09:00:00', 'user_agent' => 'Googlebot/2.1']);
    analyticsVisit(['path' => '/earlier', 'created_at' => '2026-09-27 23:59:59']);
    analyticsVisit(['session_id' => 'earlier-d', 'path' => '/', 'created_at' => '2026-09-26 10:00:00']);
    analyticsVisit(['created_at' => '2026-10-01 00:00:00']);

    $response = $this->get(route('admin.visits.index', ['start' => '2026-09-28', 'end' => '2026-09-30']))->assertOk();
    $report = $response->viewData('report');

    expect($report['summary'])->toMatchArray(['views' => 6, 'visitors' => 3, 'pages' => 4, 'identified_views' => 5, 'views_per_visitor' => 1.67])
        ->and($report['previous']['views'])->toBe(2)
        ->and($report['returning'])->toBe(1)
        ->and($report['newVisitors'])->toBe(2)
        ->and(array_column($report['trend'], 'views'))->toBe([2, 2, 2])
        ->and($report['hours'][14]['count'])->toBe(2)
        ->and(array_sum($report['weekdays']))->toBe(6)
        ->and(collect($report['sources'])->firstWhere('label', 'google.com')['count'])->toBe(2)
        ->and(collect($report['breakdowns']['device'])->firstWhere('label', 'Bot')['count'])->toBe(1)
        ->and(collect($report['breakdowns']['browser'])->firstWhere('label', 'Edge')['count'])->toBe(1)
        ->and($report['pages']->pluck('path')->all())->toContain('/services/design', '/services/development');

    $response->assertDontSee('session-private-a')->assertDontSee('203.0.113.123')->assertDontSee('token=hidden');
});

it('applies filters consistently to totals, charts, tables, and exports', function () {
    analyticsVisit(['path' => '/service', 'page_group' => 'Services', 'user_agent' => 'iPhone Mobile Safari/605']);
    analyticsVisit(['path' => '/service', 'page_group' => 'Services']);
    analyticsVisit(['path' => '/service', 'page_group' => 'Services', 'user_agent' => 'Mobile Googlebot']);
    analyticsVisit(['path' => '/other', 'page_group' => 'Services', 'user_agent' => 'iPhone Mobile Safari/605']);
    $filters = ['start' => '2026-09-29', 'end' => '2026-09-29', 'path' => '/service', 'page_group' => 'Services', 'device' => 'Mobile', 'traffic' => 'people'];
    $report = $this->get(route('admin.visits.index', $filters))->assertOk()->viewData('report');

    expect($report['summary']['views'])->toBe(1)
        ->and($report['trend'][0]['views'])->toBe(1)
        ->and($report['recent']->total())->toBe(1)
        ->and($report['pages']->total())->toBe(1);
    $csv = $this->get(route('admin.visits.export', $filters))->assertOk()->streamedContent();
    expect(substr_count(trim($csv), "\n"))->toBe(1)
        ->and($csv)->toContain('/service')->not->toContain('/other', 'Googlebot', 'session-private-a', '203.0.113.123');
});

it('handles empty results, zero baselines, and missing tracking tables', function () {
    $empty = $this->get(route('admin.visits.index'))->assertOk()->assertSee('No visits match these filters.');
    expect($empty->viewData('report')['summary']['views_per_visitor'])->toBe(0)
        ->and($empty->viewData('report')['trend'])->toHaveCount(30);
    analyticsVisit();
    $this->get(route('admin.visits.index'))->assertOk()->assertSee('No prior baseline');

    Schema::drop('page_visits');
    $this->get(route('admin.visits.index'))->assertOk()->assertSee('Visit analytics are not available yet.');
    $this->get(route('admin.visits.export'))->assertStatus(503);
});

it('validates date ranges and filter values', function (array $filters, string $error) {
    $this->from(route('admin.visits.index'))->get(route('admin.visits.index', $filters))->assertSessionHasErrors($error);
})->with([
    [['start' => '2026-09-30', 'end' => '2026-09-28'], 'end'],
    [['start' => '2024-01-01', 'end' => '2026-09-30'], 'start'],
    [['start' => '2026-09-31', 'end' => '2026-09-30'], 'start'],
    [['start' => '2026-09-28'], 'end'],
    [['end' => '2026-09-30'], 'start'],
    [['start' => '2026-10-01', 'end' => '2026-10-02'], 'start'],
    [['device' => 'invalid-device'], 'device'],
    [['traffic' => 'unknown'], 'traffic'],
]);

it('paginates the visit log independently and retains filters in links', function () {
    foreach (range(1, 31) as $number) {
        analyticsVisit(['path' => '/page-'.$number]);
    }
    $response = $this->get(route('admin.visits.index', ['period' => 7, 'traffic' => 'people', 'visits_page' => 2, 'pages_page' => 2]))->assertOk();
    $report = $response->viewData('report');
    expect($report['summary']['views'])->toBe(31)
        ->and($report['recent']->count())->toBe(6)
        ->and($report['pages']->count())->toBe(15)
        ->and($report['recent']->previousPageUrl())->toContain('traffic=people', 'period=7', 'pages_page=2');
});

it('escapes untrusted text and neutralises spreadsheet formulas in the export', function () {
    analyticsVisit(['path' => '/<script>alert(1)</script>', 'page_group' => '=HYPERLINK("evil")', 'referrer' => 'https://example.org/private?secret=hidden']);
    $this->get(route('admin.visits.index'))->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    $csv = $this->get(route('admin.visits.export'))->assertOk()->streamedContent();
    $rows = array_map(fn ($row) => str_getcsv($row, ',', '"', ''), explode("\n", trim($csv)));
    expect($rows[1][2])->toBe("'=HYPERLINK(\"evil\")")
        ->and($csv)->toContain('example.org')->not->toContain('secret=hidden', 'session-private-a', '203.0.113.123');
});

it('uses stable pseudonymous visitor labels and handles missing referrers', function () {
    $analytics = app(VisitAnalytics::class);
    expect($analytics->visitor('one'))->toBe($analytics->visitor('one'))
        ->not->toBe($analytics->visitor('two'))
        ->and($analytics->visitor(null))->toBe('Unidentified')
        ->and($analytics->source(null))->toBe('Direct / unavailable')
        ->and($analytics->source('not a url'))->toBe('Unknown referrer')
        ->and($analytics->source('https://Example.com/path?private=true'))->toBe('example.com');
});
