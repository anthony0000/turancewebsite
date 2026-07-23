<?php

return [
    'recipient' => [
        'address' => env('CONTACT_RECIPIENT_ADDRESS', 'support@turancetechnologies.com'),
        'name' => env('CONTACT_RECIPIENT_NAME', env('APP_NAME', 'Turance Technologies')),
    ],

    'security' => [
        'minimum_form_seconds' => (int) env('CONTACT_MINIMUM_FORM_SECONDS', 2),
        'maximum_form_age_seconds' => (int) env('CONTACT_MAXIMUM_FORM_AGE_SECONDS', 7200),
        'deduplication_minutes' => (int) env('CONTACT_DEDUPLICATION_MINUTES', 15),
    ],

    'delivery' => [
        'queue' => (bool) env('CONTACT_QUEUE_MAIL', false),
        'queue_name' => env('CONTACT_MAIL_QUEUE', 'emails'),
    ],

    'turnstile' => [
        'enabled' => (bool) env('CONTACT_TURNSTILE_ENABLED', false),
        'site_key' => env('CONTACT_TURNSTILE_SITE_KEY'),
        'secret_key' => env('CONTACT_TURNSTILE_SECRET_KEY'),
        'hostname' => env('CONTACT_TURNSTILE_HOSTNAME'),
        'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
    ],

    'topics' => [
        'Website Upgrade',
        'Web Design',
        'Web Development',
        'Mobile App Development',
        'SaaS Platform Development',
        'Branding',
        'UI/UX Design',
        'Graphics Design',
    ],

    'service_topics' => [
        'web' => 'Web Design',
        'mobile' => 'Mobile App Development',
        'saas' => 'SaaS Platform Development',
        'branding' => 'Branding',
    ],

    'success_message' => 'Thanks for reaching out. We have received your message and will get back to you shortly.',
];
