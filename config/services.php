<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // 'sso' => [
    //     'base'          => env('SSO_BASE'),
    //     'client_id'     => env('SSO_CLIENT_ID'),
    //     'client_secret' => env('SSO_CLIENT_SECRET'),
    //     'redirect'      => env('SSO_REDIRECT'),
    //     'scope'         => '', // kalau perlu
    // ],

    'sso' => [
        'base'          => env('OAUTH_SERVER_URL'),
        'client_id'     => env('OAUTH_CLIENT_ID'),
        'client_secret' => env('OAUTH_CLIENT_SECRET'),
        'redirect'      => env('OAUTH_REDIRECT_URI'),
        'scope'         => 'read-profile',
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
