New contact enquiry

Name: {{ $contactMessage->name }}
Email: {{ $contactMessage->email }}
Topic: {{ $contactMessage->topic }}
Submitted: {{ $contactMessage->created_at?->format('F j, Y g:i A') }}

Message:
{{ $contactMessage->message }}
