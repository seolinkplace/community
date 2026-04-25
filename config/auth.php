<?php
return [
    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
        'unified' => [
            'driver'   => 'session',
            'provider' => 'unified_users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],
        'unified_users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\UnifiedUser::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire'   => 60,
            'throttle' => 60,
        ],
        'unified_users' => [
            'provider' => 'unified_users',
            'table'    => 'unified_password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
