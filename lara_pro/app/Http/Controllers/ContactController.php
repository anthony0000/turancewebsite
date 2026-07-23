<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use JsonException;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact', [
            'contactContext' => Crypt::encryptString(json_encode([
                'issued_at' => now()->timestamp,
                'nonce' => Str::random(32),
            ], JSON_THROW_ON_ERROR)),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->filled('company_fax')) {
            return $this->successResponse($request);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'topic' => ['required', 'string', Rule::in(config('contact.topics', []))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'contact_context' => ['required', 'string', 'max:4096'],
            'company_fax' => ['nullable', 'string', 'max:0'],
            'cf-turnstile-response' => ['nullable', 'string', 'max:2048'],
        ]);

        if (! $this->hasValidFormContext($validated['contact_context'])) {
            throw ValidationException::withMessages([
                'message' => 'Please refresh the page and try sending your enquiry again.',
            ]);
        }

        if (! $this->passesTurnstile($request)) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'We could not verify this submission. Please try again.',
            ]);
        }

        $deduplicationKey = 'contact-submission:'.hash('sha256', $validated['contact_context']);
        $deduplicationMinutes = max(1, (int) config('contact.security.deduplication_minutes', 15));

        if (! Cache::add($deduplicationKey, true, now()->addMinutes($deduplicationMinutes))) {
            return $this->successResponse($request);
        }

        unset(
            $validated['contact_context'],
            $validated['company_fax'],
            $validated['cf-turnstile-response'],
        );

        $messagePayload = [
            ...$validated,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
        ];

        $storedInDatabase = false;

        try {
            $contactMessage = ContactMessage::create($messagePayload);
            $storedInDatabase = true;
        } catch (Throwable $exception) {
            report($exception);

            $contactMessage = new ContactMessage($messagePayload);
            $contactMessage->created_at = now();
            $contactMessage->updated_at = now();
        }

        $recipientAddress = config('contact.recipient.address');
        $mailDelivered = false;

        if (filled($recipientAddress)) {
            try {
                $pendingMail = Mail::to(new Address(
                    (string) $recipientAddress,
                    (string) config('contact.recipient.name')
                ));

                $mail = new ContactMessageReceived($contactMessage);

                if ($storedInDatabase && config('contact.delivery.queue')) {
                    $pendingMail->queue(
                        $mail->onQueue((string) config('contact.delivery.queue_name', 'emails'))
                    );
                } else {
                    $pendingMail->send($mail);
                }

                $mailDelivered = true;
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        if (! $storedInDatabase && ! $mailDelivered) {
            Cache::forget($deduplicationKey);

            $errorMessage = 'We could not send your message right now. Please try again in a moment.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $errorMessage,
                ], 503);
            }

            return back()
                ->withErrors(['message' => $errorMessage])
                ->withInput();
        }

        return $this->successResponse($request);
    }

    private function successResponse(Request $request): JsonResponse|RedirectResponse
    {
        $successMessage = (string) config(
            'contact.success_message',
            'Thanks for reaching out. We have received your message and will get back to you shortly.'
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $successMessage,
            ]);
        }

        return redirect()
            ->route('contact.show')
            ->with('contact_success', $successMessage);
    }

    private function hasValidFormContext(string $encryptedContext): bool
    {
        try {
            $context = json_decode(
                Crypt::decryptString($encryptedContext),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (DecryptException|JsonException) {
            return false;
        }

        if (! is_array($context) || ! isset($context['issued_at'], $context['nonce'])) {
            return false;
        }

        $issuedAt = filter_var($context['issued_at'], FILTER_VALIDATE_INT);

        if ($issuedAt === false || ! is_string($context['nonce']) || strlen($context['nonce']) < 20) {
            return false;
        }

        $age = now()->timestamp - (int) $issuedAt;
        $minimumAge = max(0, (int) config('contact.security.minimum_form_seconds', 2));
        $maximumAge = max($minimumAge + 1, (int) config('contact.security.maximum_form_age_seconds', 7200));

        return $age >= $minimumAge && $age <= $maximumAge;
    }

    private function passesTurnstile(Request $request): bool
    {
        if (! config('contact.turnstile.enabled')) {
            return true;
        }

        $token = trim((string) $request->input('cf-turnstile-response'));
        $secret = trim((string) config('contact.turnstile.secret_key'));

        if ($token === '' || $secret === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout(5)
                ->post((string) config('contact.turnstile.verify_url'), [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                    'idempotency_key' => (string) Str::uuid(),
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        if (! $response->successful() || ! $response->json('success')) {
            return false;
        }

        $expectedHostname = trim((string) config('contact.turnstile.hostname'));
        $responseHostname = trim((string) $response->json('hostname'));

        return $expectedHostname === ''
            || ($responseHostname !== '' && hash_equals($expectedHostname, $responseHostname));
    }
}
