<?php

return [
    'key' => env('AI_API_KEY'),
    'model' => env('AI_MODEL', 'gemini-3.8-flash'),
    'base_url' => env('AI_BASE_URL', 'https://api.scalemax.pro/token/v1'),
    'ca_bundle' => env('AI_CA_BUNDLE', storage_path('app/private/gemini-ca.pem')),
    'origin' => env('CHAT_ALLOWED_ORIGIN', env('APP_URL', 'http://localhost')),
];
