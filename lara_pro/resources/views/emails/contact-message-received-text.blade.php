New contact enquiry

Name: {{ $contactMessage->name }}
Email: {{ $contactMessage->email }}
Topic: {{ $contactMessage->topic }}
@if ($contactMessage->promo_code)
Promo code: {{ $contactMessage->promo_code }} ({{ $contactMessage->promo_discount_percent }}% discount)
@endif
Submitted: {{ $contactMessage->created_at?->format('F j, Y g:i A') }}

@if ($contactMessage->biggest_challenge)
What is the single biggest challenge in your business right now?
{{ $contactMessage->biggest_challenge }}

How long has the problem existed?
{{ $contactMessage->problem_duration }}

What have you already tried that did not work?
{{ $contactMessage->previous_attempts }}
@endif
@if ($contactMessage->message)
{{ $contactMessage->biggest_challenge ? 'Additional project context' : 'Message' }}:
{{ $contactMessage->message }}
@endif
