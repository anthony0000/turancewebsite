<?php

return [
    'admin' => [
        'email' => env('LUXURY_INVOICE_ADMIN_EMAIL', env('LUXURY_QUOTE_ADMIN_EMAIL')),
        'password' => env('LUXURY_INVOICE_ADMIN_PASSWORD', env('LUXURY_QUOTE_ADMIN_PASSWORD')),
        'session_key' => 'luxury_quote_admin_authenticated',
    ],

    'brand' => [
        'studio_name' => env('LUXURY_INVOICE_STUDIO_NAME', env('LUXURY_QUOTE_STUDIO_NAME', env('MAIL_FROM_NAME', 'Turance Technologies'))),
        'tagline' => env('LUXURY_INVOICE_TAGLINE', env('LUXURY_QUOTE_TAGLINE', 'Excellence Delivered')),
        'logo_path' => env('LUXURY_INVOICE_LOGO_PATH', env('LUXURY_QUOTE_LOGO_PATH', base_path('../assets/img/logo/logo.png'))),
        'contact_email' => env('LUXURY_INVOICE_CONTACT_EMAIL', env('LUXURY_QUOTE_CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@turancetechnologies.com'))),
        'contact_phone' => env('LUXURY_INVOICE_CONTACT_PHONE', env('LUXURY_QUOTE_CONTACT_PHONE', '+2349124948602')),
        'website' => env('LUXURY_INVOICE_WEBSITE', env('LUXURY_QUOTE_WEBSITE', env('APP_URL', 'https://turancetechnologies.com'))),
        'currency' => env('LUXURY_INVOICE_CURRENCY', env('LUXURY_QUOTE_CURRENCY', 'NGN')),
    ],

    'categories' => [
        'Luxury Website Experience',
        'Mobile Product Build',
        'SaaS Platform Delivery',
        'Brand Identity System',
        'Custom Digital Engagement',
    ],

    'templates' => [
        'obsidian' => [
            'name' => 'Slate Invoice',
            'description' => 'Charcoal header band with a clean white invoice body and structured totals.',
            'badge' => 'Invoice Standard',
            'palette' => [
                'page' => '#ffffff',
                'surface' => '#f5f6f8',
                'panel' => '#343b48',
                'accent' => '#343b48',
                'accent_soft' => '#ffffff',
                'text' => '#1f232b',
                'muted' => '#6b7280',
                'line' => '#e6e8ed',
            ],
        ],
        'champagne' => [
            'name' => 'Studio Letterhead',
            'description' => 'Soft neutral paper tones with an editorial invoice layout and restrained contrast.',
            'badge' => 'Letterhead Style',
            'palette' => [
                'page' => '#fffdf8',
                'surface' => '#f4f1eb',
                'panel' => '#38404c',
                'accent' => '#38404c',
                'accent_soft' => '#ffffff',
                'text' => '#252830',
                'muted' => '#7c736a',
                'line' => '#e7e1d7',
            ],
        ],
        'emerald' => [
            'name' => 'Boardroom Invoice',
            'description' => 'A crisp corporate invoice with steel-gray structure and presentation-ready pricing.',
            'badge' => 'Executive Invoice',
            'palette' => [
                'page' => '#ffffff',
                'surface' => '#f4f6f7',
                'panel' => '#2f3841',
                'accent' => '#2f3841',
                'accent_soft' => '#ffffff',
                'text' => '#1f2328',
                'muted' => '#68717a',
                'line' => '#e5e8eb',
            ],
        ],
    ],

    'defaults' => [
        'scope_items' => [
            'Strategic discovery and positioning alignment',
            'Premium visual direction tailored to the client brand',
            'Responsive delivery across the priority customer journey',
            'Launch support with a refinement window after handoff',
        ],
        'outcomes' => [
            'A stronger first impression for clients, investors, and stakeholders',
            'Sharper digital positioning that supports premium pricing conversations',
            'A cleaner user journey designed to increase trust and response',
        ],
        'milestones' => [
            'Direction approval and commercial alignment',
            'Luxury presentation and refinement cycle',
            'Production, QA, and stakeholder-ready handoff',
        ],
        'optional_addons' => [
            'Copywriting refinement',
            'Analytics and conversion tracking setup',
            'Admin dashboard or reporting layer',
        ],
    ],
];
