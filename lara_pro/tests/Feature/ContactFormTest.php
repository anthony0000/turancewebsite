<?php

use App\Mail\ContactMessageReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('stores contact form submissions and sends a notification email', function () {
    Mail::fake();

    Config::set('contact.recipient.address', 'team@example.com');
    Config::set('contact.recipient.name', 'Turance Team');

    $response = $this->post(route('contact.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'topic' => 'Web Development',
        'message' => 'We would like to build a new customer portal for our team.',
    ]);

    $response
        ->assertRedirect(route('contact.show'))
        ->assertSessionHas('contact_success');

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'topic' => 'Web Development',
    ]);

    Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
        return $mail->hasTo('team@example.com')
            && $mail->contactMessage->name === 'Jane Doe'
            && $mail->contactMessage->email === 'jane@example.com';
    });
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
            'message',
        ]);
});
