New contact enquiry

Name: {{ $contactMessage->name }}
Email: {{ $contactMessage->email }}
Topic: {{ $contactMessage->topic }}
@if ($contactMessage->promo_code)
Promo code: {{ $contactMessage->promo_code }} ({{ $contactMessage->promo_discount_percent }}% discount)
@endif
Submitted: {{ $contactMessage->created_at?->format('F j, Y g:i A') }}

Message:
{{ $contactMessage->message }}
