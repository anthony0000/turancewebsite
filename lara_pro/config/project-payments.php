<?php

return [
    'currency' => env('PROJECT_PAYMENT_CURRENCY', 'USD'),
    'local_currency' => env('PROJECT_PAYMENT_LOCAL_CURRENCY', 'NGN'),
    'exchange_rate' => (float) env('PROJECT_PAYMENT_EXCHANGE_RATE', 1370),
    'bank_name' => env('PROJECT_PAYMENT_BANK_NAME', 'ZENITH BANK PLC'),
    'account_name' => env('PROJECT_PAYMENT_ACCOUNT_NAME', 'TURANCE TECHNOLOGIES'),
    'account_number' => env('PROJECT_PAYMENT_ACCOUNT_NUMBER', '1313068587'),
];
