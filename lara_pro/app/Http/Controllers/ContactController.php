<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact');
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'topic' => ['required', 'string', Rule::in(config('contact.topics', []))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

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
                Mail::to(new Address(
                    (string) $recipientAddress,
                    (string) config('contact.recipient.name')
                ))->send(new ContactMessageReceived($contactMessage));
                $mailDelivered = true;
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        if (! $storedInDatabase && ! $mailDelivered) {
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
}
