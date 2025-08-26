<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the rate limiting rules for the API endpoints.
    | Rate limiting helps prevent abuse and ensures fair usage of the API.
    |
    */

    'api' => [
        'global' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],

        'auth' => [
            'max_attempts' => 5,
            'decay_minutes' => 1,
        ],

        'uploads' => [
            'max_attempts' => 10,
            'decay_minutes' => 1,
        ],

        'search' => [
            'max_attempts' => 30,
            'decay_minutes' => 1,
        ],
    ],

    'web' => [
        'global' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
    ],

];
