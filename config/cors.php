<?php

return [
    'paths' => ['api/chat'],
    'allowed_methods' => ['POST', 'DELETE'],
    'allowed_origins' => [env('CHAT_ALLOWED_ORIGIN', env('APP_URL', 'http://localhost'))],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'X-CSRF-TOKEN', 'Accept'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
