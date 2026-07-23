<?php

return [
    'recipient' => [
        'address' => env('CONTACT_RECIPIENT_ADDRESS', 'support@turancetechnologies.com'),
        'name' => env('CONTACT_RECIPIENT_NAME', env('APP_NAME', 'Turance Technologies')),
    ],

    'topics' => [
        'Website Upgrade',
        'Web Design',
        'Web Development',
        'Mobile App Development',
        'Branding',
        'UI/UX Design',
        'Graphics Design',
    ],

    'success_message' => 'Thanks for reaching out. We have received your message and will get back to you shortly.',
];
