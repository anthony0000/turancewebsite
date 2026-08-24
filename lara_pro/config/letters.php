<?php

return [
    'background_path' => env(
        'LETTERHEAD_BACKGROUND_PATH',
        base_path('../assets/img/branding/turance-letterhead.png')
    ),

    'document_types' => [
        'Official Letter',
        'Company Memo',
        'Meeting Notice',
        'General Document',
    ],

    'defaults' => [
        'document_type' => 'Official Letter',
        'greeting' => 'Dear Sir/Madam,',
        'closing' => 'Kind regards,',
        'signatory_name' => '',
        'signatory_title' => '',
    ],
];
