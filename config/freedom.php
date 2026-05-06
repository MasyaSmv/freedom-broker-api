<?php

return [
    'api_url' => env('FREEDOM_API_URL', 'https://tradernet.ru/api'),
    'version' => (int)env('FREEDOM_API_VERSION', 2),

    'http' => [
        'timeout' => (float)env('FREEDOM_HTTP_TIMEOUT', 15),
        'connect_timeout' => (float)env('FREEDOM_HTTP_CONNECT_TIMEOUT', 10),
        'retries' => (int)env('FREEDOM_HTTP_RETRIES', 0),
        'retry_delay' => (int)env('FREEDOM_HTTP_RETRY_DELAY', 100),
        'verify_ssl' => (bool)env('FREEDOM_HTTP_VERIFY_SSL', true),
        'user_agent' => env('FREEDOM_HTTP_USER_AGENT', 'freedom-broker-api'),
    ],
];
