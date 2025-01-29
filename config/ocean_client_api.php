<?php
return [
    'url' => env('CLIENT_OCEAN_API'),
    'timeout' => env('EXTERNAL_API_TIMEOUT', 30),
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ]
];
