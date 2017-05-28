<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Slack team api hooks
    |--------------------------------------------------------------------------
    */
    'team_slack_webhook' => env('TRUSTEE_SLACK_WEBHOOK', ''),
    'trustees_slack_webhook' => env('TEAM_SLACK_WEBHOOK', ''),

    /*
    |--------------------------------------------------------------------------
    | Passport token length
    |--------------------------------------------------------------------------
    */
    'passport_token_expire_days' => env('PASSPORT_TOKEN_EXPIRE_DAYS', 15),
    'passport_refresh_token_expire_days' =>env('PASSPORT_REFRESH_TOKEN_EXPIRE_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Internal ip address range
    |--------------------------------------------------------------------------
    */
    'restriced_ip_range' => env('RESTRICED_IP_RANGE', '10.0.0.0/8'),
];
