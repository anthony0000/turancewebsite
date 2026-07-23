<?php

use App\Models\Proposal;
use App\Models\ProposalSection;
use App\Models\ProposalTemplate;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates, previews, and exports a premium proposal', function () {
    $sessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');
    $session = [
        $sessionKey => true,
        'luxury_quote_admin_email' => 'admin@example.com',
    ];

    $this
        ->withSession($session)
        ->get(route('admin.proposals.index'))
        ->assertOk()
        ->assertSee('Proposal Generator')
        ->assertSee('Corporate Green Proposal');

    $template = ProposalTemplate::query()->where('slug', 'corporate-green')->firstOrFail();

    $payload = [
        'proposal_template_id' => $template->id,
        'status' => 'draft',
        'title' => 'Enterprise Growth Proposal',
        'client_name' => 'Nora Kelvin',
        'client_company' => 'Asterion Holdings',
        'prepared_by' => 'Turance Technologies',
        'company_name' => 'Turance Technologies',
        'company_slogan' => 'Excellence Delivered',
        'proposal_date' => now()->toDateString(),
        'reference_number' => 'PROP-TEST-001',
        'contact_email' => 'hello@example.com',
        'phone_number' => '+1 555 010 3344',
        'website' => 'https://example.com',
        'business_address' => '100 Boardroom Avenue',
        'currency' => 'USD',
        'primary_color' => '#143d32',
        'secondary_color' => '#0b241e',
        'accent_color' => '#8ccf5f',
        'font_family' => 'Aptos',
        'header_style' => 'Minimal bar',
        'footer_style' => 'Reference footer',
        'page_numbering' => '1',
        'sections_payload' => json_encode(config('proposals.sections')),
        'pricing_payload' => json_encode(config('proposals.pricing_items')),
        'timeline_payload' => json_encode(config('proposals.timeline')),
        'team_payload' => json_encode(config('proposals.team')),
    ];

    $response = $this
        ->withSession($session)
        ->post(route('admin.proposals.store'), $payload);

    $proposal = Proposal::query()->with(['sections', 'pricingItems', 'settings'])->first();

    expect($proposal)->not->toBeNull();
    expect($proposal->sections)->toHaveCount(count(config('proposals.sections')));
    expect((float) $proposal->grand_total)->toBe(11500.0);

    $response->assertRedirect(route('admin.proposals.show', $proposal));

    $this
        ->withSession($session)
        ->get(route('admin.proposals.show', $proposal))
        ->assertOk()
        ->assertSee('PDF Export')
        ->assertSee('data:image/png;base64,', false)
        ->assertSee('Turance Technologies logo')
        ->assertSee('Enterprise Growth Proposal');

    $pdfResponse = $this
        ->withSession($session)
        ->get(route('admin.proposals.pdf', $proposal));

    expect($pdfResponse->headers->get('content-type'))->toContain('application/pdf');
    expect(substr($pdfResponse->getContent(), 0, 4))->toBe('%PDF');
    expect(strlen($pdfResponse->getContent()))->toBeGreaterThan(1000);

    $pageCount = preg_match_all('/\/Type\s*\/Page\b/', $pdfResponse->getContent());
    expect($pageCount)->toBeGreaterThan(0)
        ->and($pageCount)->toBeGreaterThanOrEqual(count(config('proposals.sections')));

    $this
        ->get(route('proposals.share', $proposal->public_token))
        ->assertOk()
        ->assertSee('Enterprise Growth Proposal');
});

it('embeds the default Turance logo when a proposal has no custom logo', function () {
    $proposal = new Proposal([
        'proposal_number' => 'TT-PROP-TEST',
        'theme_key' => 'gold',
        'title' => 'Test Proposal',
        'client_name' => 'Client',
        'client_company' => 'Client Company',
        'prepared_by' => 'Turance Technologies',
        'company_name' => 'Turance Technologies',
        'company_slogan' => 'Excellence Delivered',
        'proposal_date' => now(),
        'currency' => 'USD',
        'grand_total' => 0,
    ]);

    $proposal->setRelation('sections', new EloquentCollection([
        new ProposalSection([
            'type' => 'cover',
            'title' => 'Cover',
            'is_visible' => true,
            'sort_order' => 0,
        ]),
    ]));
    $proposal->setRelation('pricingItems', new EloquentCollection);
    $proposal->setRelation('timelines', new EloquentCollection);
    $proposal->setRelation('teamMembers', new EloquentCollection);
    $proposal->setRelation('settings', null);
    $proposal->setRelation('template', null);

    $html = view('admin.proposals.word', ['proposal' => $proposal])->render();

    expect($html)
        ->toContain('data:image/png;base64,')
        ->toContain('Turance Technologies logo');
});
