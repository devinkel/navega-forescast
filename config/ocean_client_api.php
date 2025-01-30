<?php
return [
    'url' => env('CLIENT_FORECAST_API'),
    'timeout' => env('CLIENT_FORECAST_TIMEOUT', 30),
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ]
];
