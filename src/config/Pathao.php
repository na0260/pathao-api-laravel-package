<?php

return [
    'client_id' => env('PATHAO_CLIENT_ID'),
    'client_secret' => env('PATHAO_CLIENT_SECRET'),
    'username' => env('PATHAO_USERNAME'),
    'password' => env('PATHAO_PASSWORD'),
    'sandbox' => env('PATHAO_SANDBOX', true),
    'base_urls' => [
        'sandbox' => 'https://sandbox.pathao.com/',
        'live' => 'https://api-hermes.pathao.com/'
    ],
];
