<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OneSignal App ID
    |--------------------------------------------------------------------------
    |
    | Your OneSignal application ID. You can find this in your OneSignal
    | dashboard under Settings > Keys & IDs.
    |
    */
    'app_id' => env('ONESIGNAL_APP_ID'),

    /*
    |--------------------------------------------------------------------------
    | OneSignal REST API Key
    |--------------------------------------------------------------------------
    |
    | Your OneSignal REST API key. You can find this in your OneSignal
    | dashboard under Settings > Keys & IDs.
    |
    */
    'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | OneSignal User Auth Key (Optional)
    |--------------------------------------------------------------------------
    |
    | Your OneSignal User Auth Key. This is only needed for certain
    | account-level operations.
    |
    */
    'user_auth_key' => env('ONESIGNAL_USER_AUTH_KEY'),
];
