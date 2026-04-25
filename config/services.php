<?php
return [
    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],
    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],
    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'parser' => [
        'secret' => env('PARSER_SECRET', 'change-me-in-production'),
    ],
    'cryptomus' => [
        'id'  => env('CRYPTOMUS_MERCHANT_ID'),
        'key' => env('CRYPTOMUS_PAYMENT_KEY'),
    ],
    'nowpayments' => [
        'api_key'    => env('NOWPAYMENTS_API_KEY'),
        'ipn_secret' => env('NOWPAYMENTS_IPN_SECRET'),
        'sandbox'    => env('NOWPAYMENTS_SANDBOX', false),
    ],
    'usdt_direct' => [
        'wallet'  => env('USDT_TRC20_WALLET'),
        'network' => 'TRC20',
    ],
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI', 'https://seolinkplace.com/auth/google/callback'),
    ],
];
