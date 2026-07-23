<?php

use App\Models\ContactMessage;
use App\Models\LuxuryQuote;
use App\Models\PageVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the redesigned admin login page', function () {
    config()->set('luxury-quotes.admin.email', 'admin@example.com');
    config()->set('luxury-quotes.admin.password', 'luxury-pass-123');

    $this
        ->get(route('admin.login'))
        ->assertOk()
        ->assertSee('Welcome back to the invoice desk.')
        ->assertSee('auth-card', false);
});

it('allows an admin to sign in to the invoice generator', function () {
    config()->set('luxury-quotes.admin.email', 'admin@example.com');
    config()->set('luxury-quotes.admin.password', 'luxury-pass-123');

    $response = $this->post(route('admin.login.store'), [
        'email' => 'admin@example.com',
        'password' => 'luxury-pass-123',
    ]);

    $response
        ->assertRedirect(route('admin.quotes.index'))
        ->assertSessionHas('luxury_quote_admin_authenticated', true);
});

it('stores an invoice and exports invoice and mou pdfs', function () {
    $sessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');

    $payload = [
        'template' => 'obsidian',
        'project_category' => 'Luxury Website Experience',
        'company_name' => 'Asterion Holdings',
        'company_industry' => 'Private investment advisory',
        'recipient_name' => 'Nora Kelvin',
        'recipient_title' => 'Managing Director',
        'recipient_email' => 'nora@example.com',
        'recipient_phone' => '+1 555 010 3344',
        'project_title' => 'Boardroom-grade website refresh',
        'executive_summary' => 'We are delivering a <strong>refined website experience</strong> that strengthens first impression, clarifies positioning, and supports premium client conversations from the first visit.',
        'exchange_rate' => 1370,
        'timeline' => '4 to 6 weeks',
        'valid_until' => now()->addDays(10)->toDateString(),
        'line_items' => [
            ['description' => 'Strategic discovery', 'amount' => 75],
            ['description' => 'Luxury interface direction', 'amount' => 100],
            ['description' => 'Responsive implementation', 'amount' => 75],
        ],
        'outcomes' => "<strong>Sharper trust</strong>\nImproved positioning",
        'milestones' => "Discovery approval\nDesign presentation\nLaunch handoff",
        'optional_addons' => "Copywriting refinement\nAnalytics setup",
        'intro_message' => 'Prepared to elevate the company presentation with calm premium clarity.',
        'closing_note' => 'Once approved, the engagement moves into a tightly managed execution phase.',
    ];

    $createResponse = $this
        ->withSession([
            $sessionKey => true,
            'luxury_quote_admin_email' => 'admin@example.com',
        ])
        ->post(route('admin.quotes.store'), $payload);

    $quote = LuxuryQuote::query()->first();

    expect($quote)->not->toBeNull();
    expect((float) $quote->investment_amount)->toBe(250.0);
    expect((float) $quote->exchange_rate)->toBe(1370.0);
    expect($quote->executive_summary)->toContain('<strong>refined website experience</strong>');
    expect($quote->outcomes[0])->toBe('<strong>Sharper trust</strong>');

    $createResponse->assertRedirect(route('admin.quotes.show', $quote));

    $this
        ->withSession([
            $sessionKey => true,
            'luxury_quote_admin_email' => 'admin@example.com',
        ])
        ->get(route('admin.quotes.show', $quote))
        ->assertOk()
        ->assertSee('Download MOU')
        ->assertSee('data:image/png;base64,', false)
        ->assertSee('Turance Technologies logo')
        ->assertSee('<strong>refined website experience</strong>', false)
        ->assertSee('<strong>Sharper trust</strong>', false);

    $this->assertDatabaseHas('luxury_quotes', [
        'company_name' => 'Asterion Holdings',
        'template' => 'obsidian',
        'project_title' => 'Boardroom-grade website refresh',
    ]);

    $pdfResponse = $this
        ->withSession([
            $sessionKey => true,
            'luxury_quote_admin_email' => 'admin@example.com',
        ])
        ->get(route('admin.quotes.pdf', $quote));

    expect($pdfResponse->headers->get('content-type'))->toContain('application/pdf');

    $invoiceHtml = view('admin.quotes.pdf', [
        'quote' => $quote,
        'template' => config('luxury-quotes.templates.obsidian'),
        'brand' => config('luxury-quotes.brand'),
    ])->render();

    expect($invoiceHtml)
        ->toContain('data:image/png;base64,')
        ->toContain('Turance Technologies logo');

    $mouResponse = $this
        ->withSession([
            $sessionKey => true,
            'luxury_quote_admin_email' => 'admin@example.com',
        ])
        ->get(route('admin.quotes.mou', $quote));

    expect($mouResponse->headers->get('content-type'))->toContain('application/pdf');

    $mouHtml = view('admin.quotes.mou-pdf', [
        'quote' => $quote,
        'template' => config('luxury-quotes.templates.obsidian'),
        'brand' => config('luxury-quotes.brand'),
        'mouNumber' => str_replace('-INV-', '-MOU-', $quote->quote_number),
    ])->render();

    expect($mouHtml)
        ->toContain('data:image/png;base64,')
        ->toContain('Turance Technologies logo');
});

it('allows small website upgrade invoices below the old premium floor', function () {
    $sessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');

    $payload = [
        'template' => 'obsidian',
        'project_category' => 'Luxury Website Experience',
        'company_name' => 'Small Upgrade Co',
        'company_industry' => 'Retail',
        'recipient_name' => 'Amina Stone',
        'recipient_title' => 'Founder',
        'recipient_email' => 'amina@example.com',
        'recipient_phone' => '+1 555 010 2222',
        'project_title' => 'Website upgrade fixes',
        'executive_summary' => 'We are applying a focused website upgrade that improves a small but important customer-facing part of the existing site.',
        'exchange_rate' => 1500,
        'timeline' => '2 days',
        'valid_until' => now()->addDays(10)->toDateString(),
        'line_items' => [
            ['description' => 'Website upgrade support', 'amount' => 25],
        ],
        'outcomes' => "Updated website section\nCleaner customer experience",
        'milestones' => "Apply update\nReview and publish",
        'optional_addons' => '',
        'intro_message' => 'Prepared for a focused website upgrade.',
        'closing_note' => 'Once approved, the update can be completed quickly.',
    ];

    $response = $this
        ->withSession([
            $sessionKey => true,
            'luxury_quote_admin_email' => 'admin@example.com',
        ])
        ->post(route('admin.quotes.store'), $payload);

    $quote = LuxuryQuote::query()->where('company_name', 'Small Upgrade Co')->first();

    expect($quote)->not->toBeNull();
    expect((float) $quote->investment_amount)->toBe(25.0);
    expect((float) $quote->exchange_rate)->toBe(1500.0);

    $response->assertRedirect(route('admin.quotes.show', $quote));

    $this
        ->withSession([
            $sessionKey => true,
            'luxury_quote_admin_email' => 'admin@example.com',
        ])
        ->get(route('admin.quotes.show', $quote))
        ->assertOk()
        ->assertSee('NGN 37,500')
        ->assertSee('$1 = NGN 1,500.00');
});

it('edits an existing invoice and regenerates the pdf from the saved details', function () {
    $sessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');

    $quote = LuxuryQuote::query()->create([
        'quote_number' => 'TT-INV-20260516-002',
        'template' => 'obsidian',
        'project_category' => 'Luxury Website Experience',
        'company_name' => 'Asterion Holdings',
        'company_industry' => 'Private investment advisory',
        'recipient_name' => 'Nora Kelvin',
        'recipient_title' => 'Managing Director',
        'recipient_email' => 'nora@example.com',
        'recipient_phone' => '+1 555 010 3344',
        'project_title' => 'Boardroom-grade website refresh',
        'executive_summary' => 'A refined website experience that strengthens first impression and supports premium client conversations.',
        'investment_amount' => 6000,
        'timeline' => '4 to 6 weeks',
        'valid_until' => now()->subDay(),
        'scope_items' => ['Strategic discovery', 'Luxury interface direction'],
        'outcomes' => ['Sharper trust'],
        'milestones' => ['Discovery approval'],
        'optional_addons' => ['Analytics setup'],
        'intro_message' => 'Prepared to elevate the company presentation with calm premium clarity.',
        'closing_note' => 'Once approved, the engagement moves into a tightly managed execution phase.',
    ]);

    $session = [
        $sessionKey => true,
        'luxury_quote_admin_email' => 'admin@example.com',
    ];

    $this
        ->withSession($session)
        ->get(route('admin.quotes.edit', $quote))
        ->assertOk()
        ->assertSee('Edit invoice details')
        ->assertSee('Boardroom-grade website refresh');

    $payload = [
        'template' => 'obsidian',
        'project_category' => 'Luxury Website Experience',
        'company_name' => 'Asterion Capital',
        'company_industry' => 'Private investment advisory',
        'recipient_name' => 'Nora Kelvin',
        'recipient_title' => 'Chief Operating Officer',
        'recipient_email' => 'nora@example.com',
        'recipient_phone' => '+1 555 010 3344',
        'project_title' => 'Premium website upgrade',
        'executive_summary' => 'We are updating the invoice so the regenerated document reflects the latest commercial scope, client positioning, and delivery expectations.',
        'exchange_rate' => 1425,
        'timeline' => '6 weeks',
        'valid_until' => now()->addDays(14)->toDateString(),
        'line_items' => [
            ['description' => 'Strategic workshop', 'amount' => 1800],
            ['description' => 'Premium website direction', 'amount' => 2400],
            ['description' => 'Responsive delivery', 'amount' => 3000],
        ],
        'outcomes' => "Sharper trust\nImproved conversion clarity",
        'milestones' => "Workshop approval\nDesign signoff\nFinal handoff",
        'optional_addons' => "Analytics setup\nCopy refinement",
        'intro_message' => 'Updated invoice opening message.',
        'closing_note' => 'Updated invoice closing note.',
    ];

    $this
        ->withSession($session)
        ->put(route('admin.quotes.update', $quote), $payload)
        ->assertRedirect(route('admin.quotes.show', $quote))
        ->assertSessionHas('status');

    $quote->refresh();

    expect($quote->quote_number)->toBe('TT-INV-20260516-002');
    expect($quote->company_name)->toBe('Asterion Capital');
    expect((float) $quote->investment_amount)->toBe(7200.0);
    expect((float) $quote->exchange_rate)->toBe(1425.0);
    expect($quote->scope_items)->toBe(['Strategic workshop', 'Premium website direction', 'Responsive delivery']);
    expect($quote->line_items)->toEqual([
        ['description' => 'Strategic workshop', 'amount' => 1800],
        ['description' => 'Premium website direction', 'amount' => 2400],
        ['description' => 'Responsive delivery', 'amount' => 3000],
    ]);

    $pdfResponse = $this
        ->withSession($session)
        ->get(route('admin.quotes.pdf', $quote));

    expect($pdfResponse->headers->get('content-type'))->toContain('application/pdf');
});

it('renders the admin dashboard with aggregated activity data', function () {
    $sessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');

    LuxuryQuote::query()->create([
        'quote_number' => 'TT-INV-20260516-001',
        'template' => 'obsidian',
        'project_category' => 'Luxury Website Experience',
        'company_name' => 'Asterion Holdings',
        'project_title' => 'Boardroom-grade website refresh',
        'executive_summary' => 'A refined website experience that strengthens first impression and supports premium client conversations.',
        'investment_amount' => 6000,
        'timeline' => '4 to 6 weeks',
        'valid_until' => now()->addDays(10),
        'scope_items' => ['Strategic discovery', 'Luxury interface direction'],
        'outcomes' => ['Sharper trust'],
        'milestones' => ['Discovery approval'],
        'optional_addons' => ['Analytics setup'],
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2),
    ]);

    ContactMessage::query()->create([
        'name' => 'Nora Kelvin',
        'email' => 'nora@example.com',
        'topic' => 'Project enquiry',
        'message' => 'We would like a new invoice for a premium website.',
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    PageVisit::query()->create([
        'path' => '/',
        'route_name' => 'home',
        'page_group' => 'Landing',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this
        ->withSession([
            $sessionKey => true,
            'luxury_quote_admin_email' => 'admin@example.com',
        ])
        ->get(route('admin.quotes.index'))
        ->assertOk()
        ->assertSee('Performance Overview')
        ->assertSee('data-rich-editor', false)
        ->assertSee('data-rich-command="bold"', false)
        ->assertSee('data-exchange-rate', false)
        ->assertSee('Asterion Holdings')
        ->assertSee('Landing Page');
});
