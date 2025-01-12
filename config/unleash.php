<?php

return [
    'enabled' => env('UNLEASH_ENABLED', false),
    'token' => env('UNLEASH_API_TOKEN'),
    'app-url' => env('UNLEASH_APP_URL'),
    'app-name' => 'ezhpo',
    'instance-id' => env('UNLEASH_INSTANCE_ID'),
    'hostname' => env('APP_URL', 'http://localhost'),
];
