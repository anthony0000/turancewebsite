<?php

return [
    // Set to the IP addresses or CIDR ranges of the reverse proxies in front of this app.
    // Never trust forwarded IP headers from arbitrary visitors.
    'proxies' => env('TRUSTED_PROXIES'),
];
