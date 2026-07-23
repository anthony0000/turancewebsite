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
    <p style="margin: 0 0 8px;"><strong>Submitted:</strong> {{ $contactMessage->created_at?->format('F j, Y g:i A') }}</p>

    <h2 style="margin: 24px 0 12px; font-size: 18px;">Message</h2>
    <p style="white-space: pre-line; margin: 0;">{{ $contactMessage->message }}</p>
 </body>
</html>
