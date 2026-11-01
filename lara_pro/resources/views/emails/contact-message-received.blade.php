<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Contact Enquiry</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2933;">
    <h1 style="margin-bottom: 16px;">New contact enquiry</h1>

    <p style="margin: 0 0 8px;"><strong>Name:</strong> {{ $contactMessage->name }}</p>
    <p style="margin: 0 0 8px;"><strong>Email:</strong> {{ $contactMessage->email }}</p>
    <p style="margin: 0 0 8px;"><strong>Topic:</strong> {{ $contactMessage->topic }}</p>
    @if ($contactMessage->promo_code)
        <p style="margin: 0 0 8px;"><strong>Promo code:</strong> {{ $contactMessage->promo_code }} ({{ $contactMessage->promo_discount_percent }}% discount)</p>
    @endif
    <p style="margin: 0 0 8px;"><strong>Submitted:</strong> {{ $contactMessage->created_at?->format('F j, Y g:i A') }}</p>

    @if ($contactMessage->biggest_challenge)
        <h2 style="margin: 24px 0 12px; font-size: 18px;">What is the single biggest challenge in your business right now?</h2>
        <p style="white-space: pre-line; margin: 0;">{{ $contactMessage->biggest_challenge }}</p>
        <h2 style="margin: 24px 0 12px; font-size: 18px;">How long has the problem existed?</h2>
        <p style="white-space: pre-line; margin: 0;">{{ $contactMessage->problem_duration }}</p>
        <h2 style="margin: 24px 0 12px; font-size: 18px;">What have you already tried that did not work?</h2>
        <p style="white-space: pre-line; margin: 0;">{{ $contactMessage->previous_attempts }}</p>
    @endif
    @if ($contactMessage->message)
        <h2 style="margin: 24px 0 12px; font-size: 18px;">{{ $contactMessage->biggest_challenge ? 'Additional project context' : 'Message' }}</h2>
        <p style="white-space: pre-line; margin: 0;">{{ $contactMessage->message }}</p>
    @endif
 </body>
</html>
