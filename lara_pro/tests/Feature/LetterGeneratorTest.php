<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\LetterDraft;
use App\Support\LetterBody;
use App\Support\LetterPageLayout;

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
        ->assertSee('position: absolute')
        ->assertDontSee('letter-document-type')
        ->assertDontSee('data-letter-preview="document_type"', false);

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

it('saves incomplete letters as drafts and reopens them for editing', function () {
    $session = [
        config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        'luxury_quote_admin_email' => 'admin@example.com',
    ];

    $this->withSession($session)
        ->post(route('admin.letters.drafts.store'), [
            'document_type' => 'Official Letter',
            'date' => '',
            'subject' => 'First draft',
            'greeting' => '',
            'closing' => '',
            'body' => '<p>Hello <strong>team</strong>.</p>',
        ])
        ->assertRedirect();

    $draft = LetterDraft::query()->sole();
    expect($draft->content['body'])->toBe('<p>Hello <strong>team</strong>.</p>');

    $this->withSession($session)
        ->get(route('admin.letters.drafts.edit', $draft))
        ->assertOk()
        ->assertSee('First draft')
        ->assertSee('Hello <strong>team</strong>.', false)
        ->assertSee('id="date" type="date" name="date" value=""', false)
        ->assertSee('Save draft');

    $this->withSession($session)
        ->post(route('admin.letters.drafts.update', $draft), [
            'subject' => 'Revised draft',
            'body' => '<p>Updated <em>copy</em>.</p>',
        ])
        ->assertRedirect(route('admin.letters.drafts.edit', $draft));

    expect($draft->fresh()->content['subject'])->toBe('Revised draft')
        ->and(LetterDraft::query()->count())->toBe(1);
});

it('keeps only supported formatting in letter bodies', function () {
    expect(LetterBody::clean('<p onclick="bad()">Hello <strong style="color:red">team</strong><script>alert(1)</script></p>'))
        ->toBe('<p>Hello <strong>team</strong></p>');
});

it('paginates long letters without dropping body text or letterhead pages', function () {
    $body = implode('', array_map(
        fn (int $number) => '<p>Paragraph '.$number.': '.str_repeat('The subscription details remain readable. ', 4).'</p>',
        range(1, 38)
    ));
    $pages = LetterPageLayout::pages([
        'body' => $body,
        'recipient_name' => 'Phillips Anthony',
        'subject' => 'Subscription renewal',
    ]);

    expect(count($pages))->toBeGreaterThan(1)
        ->and($pages[0]['first'])->toBeTrue()
        ->and($pages[0]['last'])->toBeFalse()
        ->and($pages[count($pages) - 1]['last'])->toBeTrue()
        ->and(implode('', array_column($pages, 'body')))->toBe($body);
    expect(array_filter($pages, fn (array $page) => trim(strip_tags($page['body'])) === ''))->toBeEmpty();

    $plainText = LetterBody::clean("First line\nSecond line\n\nFourth line");
    $plainPages = LetterPageLayout::pages(['body' => $plainText]);
    expect(preg_replace('/\s+/u', '', implode('', array_column($plainPages, 'body'))))
        ->toContain('Firstline<br>Secondline<br><br>Fourthline');

    $singleParagraphPages = LetterPageLayout::pages([
        'body' => '<p>'.str_repeat('A long paragraph still needs a readable continuation. ', 80).'</p>',
        'recipient_role' => 'Managing Director',
        'recipient_company' => 'Turance Technologies',
        'subject' => 'Long paragraph',
    ]);
    expect(count($singleParagraphPages))->toBeGreaterThan(1);
    expect(array_filter($singleParagraphPages, fn (array $page) => trim(strip_tags($page['body'])) === ''))->toBeEmpty();
});

it('keeps a letter with recipient details and a sign-off on one PDF page when it fits', function () {
    $paragraph = 'This is a review of subscriptions, infrastructure, storage plans and communication services. Each line should remain in the writing area when the document continues.';
    $body = implode('', array_fill(0, 7, '<p>'.$paragraph.'</p>'));
    $session = [
        config('luxury-quotes.admin.session_key', 'luxury_quote_admin_authenticated') => true,
        'luxury_quote_admin_email' => 'admin@example.com',
    ];

    $response = $this->withSession($session)->post(route('admin.letters.pdf'), [
        'document_type' => 'Official Letter',
        'date' => '2026-10-18',
        'recipient_name' => 'Phillips Anthony',
        'recipient_role' => 'Managing Director',
        'recipient_company' => 'Turance Technologies',
        'recipient_address' => 'Abuja, Nigeria',
        'subject' => 'Subscriptions and renewal dates',
        'greeting' => 'Dear team,',
        'body' => $body,
        'closing' => 'Kind regards,',
        'signatory_name' => 'Phillips Anthony',
        'signatory_title' => 'Managing Director',
    ]);

    $response->assertOk();
    expect(preg_match_all('~/Type\s*/Page\b~', $response->getContent()))->toBe(1);
});
