<?php

use App\Mail\ContactMessageReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('contact.turnstile.enabled', false);
    Config::set('contact.delivery.enabled', true);
});

function contactFormContext(?int $issuedAt = null): string
{
    return Crypt::encryptString(json_encode([
        'issued_at' => $issuedAt ?? now()->subSeconds(5)->timestamp,
        'nonce' => Str::random(32),
    ], JSON_THROW_ON_ERROR));
}

function contactSalesAnswers(): array
{
    return [
        'biggest_challenge' => 'Our customers struggle to track their requests.',
        'problem_duration' => 'About six months',
        'previous_attempts' => 'We tried a shared spreadsheet, but updates were often missed.',
    ];
}

it('stores contact form submissions and sends a notification email', function () {
    Mail::fake();

    Config::set('contact.recipient.address', 'team@example.com');
    Config::set('contact.recipient.name', 'Turance Team');

    $response = $this->post(route('contact.store'), [
        ...contactSalesAnswers(),
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'topic' => 'Web Development',
        'message' => 'We would like to build a new customer portal for our team.',
        'contact_context' => contactFormContext(),
    ]);

    $response
        ->assertRedirect(route('contact.show'))
        ->assertSessionHas('contact_success');

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'topic' => 'Web Development',
        'biggest_challenge' => contactSalesAnswers()['biggest_challenge'],
        'problem_duration' => contactSalesAnswers()['problem_duration'],
        'previous_attempts' => contactSalesAnswers()['previous_attempts'],
    ]);

    Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
        return $mail->hasTo('team@example.com')
            && $mail->contactMessage->name === 'Jane Doe'
            && $mail->contactMessage->email === 'jane@example.com'
            && $mail->contactMessage->biggest_challenge === contactSalesAnswers()['biggest_challenge'];
    });
});

it('renders the encrypted form context and honeypot field', function () {
    $this->get(route('contact.show'))
        ->assertOk()
        ->assertSee('name="contact_context"', false)
        ->assertSee('name="company_fax"', false)
        ->assertSee('name="biggest_challenge"', false)
        ->assertSee('name="problem_duration"', false)
        ->assertSee('name="previous_attempts"', false);
});

it('requires answers to all three sales questions', function () {
    $this->postJson(route('contact.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'topic' => 'Web Development',
        'contact_context' => contactFormContext(),
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['biggest_challenge', 'problem_duration', 'previous_attempts']);

    $this->assertDatabaseCount('contact_messages', 0);
});

it('accepts an enquiry without additional project context and includes the answers in email', function () {
    Mail::fake();
    Config::set('contact.recipient.address', 'team@example.com');

    $this->post(route('contact.store'), [
        ...contactSalesAnswers(),
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'topic' => 'Web Development',
        'contact_context' => contactFormContext(),
    ])->assertRedirect(route('contact.show'));

    $this->assertDatabaseHas('contact_messages', [
        'email' => 'jane@example.com',
        'message' => null,
    ]);

    Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
        $mail->assertSeeInHtml(contactSalesAnswers()['biggest_challenge']);
        $mail->assertSeeInText(contactSalesAnswers()['problem_duration']);
        $mail->assertSeeInText(contactSalesAnswers()['previous_attempts']);

        return true;
    });
});

it('keeps the contact submit button disabled until Turnstile is verified', function () {
    Config::set('contact.turnstile.enabled', true);
    Config::set('contact.turnstile.site_key', 'test-site-key');

    $this->get(route('contact.show'))
        ->assertOk()
        ->assertSee('data-turnstile-required="true"', false)
        ->assertSee('data-callback="contactTurnstileSuccess"', false)
        ->assertSee('data-expired-callback="contactTurnstileExpired"', false)
        ->assertSee('aria-disabled="true"', false)
        ->assertSee('disabled', false);
});

it('silently discards honeypot submissions', function () {
    Mail::fake();

    $this->post(route('contact.store'), [
        'company_fax' => 'bot-filled-this',
    ])->assertRedirect(route('contact.show'));

    $this->assertDatabaseCount('contact_messages', 0);
    Mail::assertNothingSent();
});

it('rejects forms submitted faster than a human can complete them', function () {
    Mail::fake();

    Config::set('contact.security.minimum_form_seconds', 3);

    $this->postJson(route('contact.store'), [
        ...contactSalesAnswers(),
        'name' => 'Fast Bot',
        'email' => 'fast@example.com',
        'topic' => 'Web Development',
        'message' => 'This otherwise valid message arrived too quickly.',
        'contact_context' => contactFormContext(now()->timestamp),
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('message');

    $this->assertDatabaseCount('contact_messages', 0);
    Mail::assertNothingSent();
});

it('deduplicates repeat submissions with the same signed context', function () {
    Mail::fake();

    Config::set('contact.recipient.address', 'team@example.com');

    $payload = [
        ...contactSalesAnswers(),
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'topic' => 'Web Development',
        'message' => 'Please send this project enquiry only once.',
        'contact_context' => contactFormContext(),
    ];

    $this->post(route('contact.store'), $payload)->assertRedirect(route('contact.show'));
    $this->post(route('contact.store'), $payload)->assertRedirect(route('contact.show'));

    $this->assertDatabaseCount('contact_messages', 1);
    Mail::assertSentCount(1);
});

it('rate limits repeated contact submissions by ip address', function () {
    Mail::fake();

    Config::set('contact.recipient.address', 'team@example.com');

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.15'])
            ->post(route('contact.store'), [
                ...contactSalesAnswers(),
                'name' => 'Rate Limit Test',
                'email' => "sender{$attempt}@example.com",
                'topic' => 'Web Development',
                'message' => "Legitimate-looking unique enquiry number {$attempt}.",
                'contact_context' => contactFormContext(),
            ])
            ->assertRedirect(route('contact.show'));
    }

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.15'])
        ->post(route('contact.store'), [
            ...contactSalesAnswers(),
            'name' => 'Rate Limit Test',
            'email' => 'blocked@example.com',
            'topic' => 'Web Development',
            'message' => 'This sixth request should be rate limited.',
            'contact_context' => contactFormContext(),
        ])
        ->assertTooManyRequests();

    $this->assertDatabaseCount('contact_messages', 5);
    Mail::assertSentCount(5);
});

it('validates turnstile server side when enabled', function () {
    Mail::fake();
    Http::fake([
        'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ]),
    ]);

    Config::set('contact.turnstile.enabled', true);
    Config::set('contact.turnstile.site_key', 'test-site-key');
    Config::set('contact.turnstile.secret_key', 'test-secret-key');

    $this->postJson(route('contact.store'), [
        ...contactSalesAnswers(),
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'topic' => 'Web Development',
        'message' => 'This submission has an invalid challenge token.',
        'contact_context' => contactFormContext(),
        'cf-turnstile-response' => 'invalid-token',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('cf-turnstile-response');

    Http::assertSent(fn ($request) => $request['secret'] === 'test-secret-key'
        && $request['response'] === 'invalid-token');
    $this->assertDatabaseCount('contact_messages', 0);
    Mail::assertNothingSent();
});

it('rejects contact submissions without a Turnstile token when enabled', function () {
    Mail::fake();
    Http::fake();

    Config::set('contact.turnstile.enabled', true);
    Config::set('contact.turnstile.secret_key', 'test-secret-key');

    $this->postJson(route('contact.store'), [
        ...contactSalesAnswers(),
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'topic' => 'Web Development',
        'message' => 'This submission has no completed challenge token.',
        'contact_context' => contactFormContext(),
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('cf-turnstile-response');

    Http::assertNothingSent();
    $this->assertDatabaseCount('contact_messages', 0);
    Mail::assertNothingSent();
});

it('can queue contact notifications when queue delivery is enabled', function () {
    Mail::fake();

    Config::set('contact.recipient.address', 'team@example.com');
    Config::set('contact.delivery.queue', true);

    $this->post(route('contact.store'), [
        ...contactSalesAnswers(),
        'name' => 'Queued Sender',
        'email' => 'queued@example.com',
        'topic' => 'Mobile App Development',
        'message' => 'Please process this notification through the email queue.',
        'contact_context' => contactFormContext(),
    ])->assertRedirect(route('contact.show'));

    Mail::assertQueued(
        ContactMessageReceived::class,
        fn (ContactMessageReceived $mail) => $mail->hasTo('team@example.com')
    );
});

it('renders a plain text alternative for contact notifications', function () {
    $contactMessage = new \App\Models\ContactMessage([
        'name' => 'Plain Text Sender',
        'email' => 'plain@example.com',
        'topic' => 'Branding',
        'message' => 'This message should remain readable without HTML.',
    ]);
    $contactMessage->created_at = now();

    (new ContactMessageReceived($contactMessage))
        ->assertSeeInText('New contact enquiry')
        ->assertSeeInText('plain@example.com')
        ->assertSeeInText('This message should remain readable without HTML.');
});

it('marks contact notifications as automated to prevent reply loops', function () {
    $contactMessage = new \App\Models\ContactMessage([
        'name' => 'Header Test',
        'email' => 'header@example.com',
        'topic' => 'Branding',
        'message' => 'Check the generated mail headers.',
    ]);

    $headers = (new ContactMessageReceived($contactMessage))->headers()->text;

    expect($headers)->toMatchArray([
        'Auto-Submitted' => 'auto-generated',
        'X-Auto-Response-Suppress' => 'All',
    ]);
});

it('returns validation errors for invalid ajax submissions', function () {
    $response = $this->postJson(route('contact.store'), [
        'name' => '',
        'email' => 'not-an-email',
        'topic' => 'Unknown Topic',
        'message' => 'short',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'name',
            'email',
            'topic',
            'biggest_challenge',
            'problem_duration',
            'previous_attempts',
        ]);
});
