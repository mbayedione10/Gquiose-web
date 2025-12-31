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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
     // API SMS pour vérification
    'sms' => [
        'provider' => env('SMS_PROVIDER', 'nimba'), // nimba, twilio, vonage
        'nimba' => [
            'base_url' => env('NIMBA_SMS_BASE_URL', 'https://api.nimbasms.com'),
            'service_id' => env('NIMBA_SMS_SERVICE_ID'),
            'secret' => env('NIMBA_SMS_SECRET'),
            'sender_name' => env('NIMBA_SMS_SENDER_NAME', 'GQUIOSE'),
        ],
        'twilio' => [
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
            'from' => env('TWILIO_FROM'),
        ],
        'vonage' => [
            'key' => env('VONAGE_KEY'),
            'secret' => env('VONAGE_SECRET'),
            'from' => env('VONAGE_FROM'),
        ],
    ],
    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'), // Deprecated - keep for backward compatibility
        'credentials_path' => env('FCM_CREDENTIALS_PATH', storage_path('app/firebase/credentials.json')),
    ],

    'apns' => [
        'key_id' => env('APNS_KEY_ID'),
        'team_id' => env('APNS_TEAM_ID'),
        'bundle_id' => env('APNS_BUNDLE_ID'),
        'key_path' => env('APNS_KEY_PATH', storage_path('app/apns/AuthKey.p8')),
        'environment' => env('APNS_ENVIRONMENT', 'production'), // 'production' or 'sandbox'
    ],

    // Social Authentication
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    ],

    'facebook' => [
        'app_id' => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
    ],

    'apple' => [
        'bundle_id' => env('APPLE_BUNDLE_ID'),
        'team_id' => env('APPLE_TEAM_ID'),
        'key_id' => env('APPLE_KEY_ID'),
        'key_path' => storage_path('app/apple/AuthKey_*.p8'),
    ],

];
