<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the dynamic Turance letterhead workspace and downloads a PDF', function () {
    $sessionKey = config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated');
    $session = [
        $sessionKey => true,
        'luxury_quote_admin_email' => 'admin@example.com',
    ];

    $this
        ->withSession($session)
        ->get(route('admin.letters.create'))
        ->assertOk()
        ->assertSee('Write it on the Turance letterhead.')
        ->assertSee('Letterhead preview')
        ->assertSee('Download PDF')
        ->assertSee('Corporate identity')
        ->assertSee('.letterhead-background')
        ->assertSee('position: absolute');

    $pdfResponse = $this
        ->withSession($session)
        ->post(route('admin.letters.pdf'), [
            'document_type' => 'Official Letter',
            'date' => '2026-08-23',
            'recipient_name' => 'Amaka Okafor',
            'recipient_role' => 'Managing Director',
            'recipient_company' => 'Asterion Holdings',
            'recipient_address' => "14 Unity Crescent\nAbuja, Nigeria",
            'subject' => 'Confirmation of engagement',
            'greeting' => 'Dear Amaka,',
            'body' => "Thank you for trusting Turance Technologies.\n\nWe are pleased to confirm the next stage of our engagement.",
            'closing' => 'Kind regards,',
            'signatory_name' => 'Tony Stark',
            'signatory_title' => 'Managing Director',
        ]);

    $pdfResponse
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Content-Disposition', 'attachment; filename=official-letter-amaka-okafor.pdf');

    expect(substr($pdfResponse->getContent(), 0, 4))->toBe('%PDF')
        ->and(strlen($pdfResponse->getContent()))->toBeGreaterThan(1000);
});
