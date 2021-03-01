<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Features enable flags
    |--------------------------------------------------------------------------
    | Note certain features are inter dependant.
    | Snackspace is need by vending and paid tools.
    */
    'features' => [
        'projects' => env('FEATURE_PROJECTS_ENABLE', true),
        'boxes' => env('FEATURE_BOXES_ENABLE', true),
        'snackspace' => env('FEATURE_SNACKSPACE_ENABLE', true),
        'vending' => env('FEATURE_VENDING_ENABLE', true),
        'tools' => env('FEATURE_TOOLS_ENABLE', true),
        'label_printer' => env('FEATURE_LABEL_PRINTER_ENABLE', true),
        'cash_membership_payments' => env('FEATURE_CASH_MEMBERSHIP_PAYMENTS_ENABLE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Slack team api hooks
    |--------------------------------------------------------------------------
    */
    'team_slack_webhook' => env('TEAM_SLACK_WEBHOOK', null),
    'trustees_slack_webhook' => env('TRUSTEE_SLACK_WEBHOOK', null),

    /*
    |--------------------------------------------------------------------------
    | Passport token length
    |--------------------------------------------------------------------------
    */
    'passport_token_expire_days' => env('PASSPORT_TOKEN_EXPIRE_DAYS', 15),
    'passport_refresh_token_expire_days' => env('PASSPORT_REFRESH_TOKEN_EXPIRE_DAYS', 30),
    'passport_personal_access_token_expire_days' => env('PASSPORT_PERSONAL_ACCESS_TOKEN_EXPIRE_DAYS', 15),

    /*
    |--------------------------------------------------------------------------
    | Internal ip address range
    |--------------------------------------------------------------------------
    */
    'restriced_ip_range' => env('RESTRICED_IP_RANGE', '10.0.0.0/8'),

    /*
    |--------------------------------------------------------------------------
    | View and Procedure Directory
    |--------------------------------------------------------------------------
    | Paths to folder containing .sql files define any views and procedures.
    | These can be re-loaded with the relevant to hms:database:refresh-*
    | artisan command.
    */
    'views_directory' => database_path(env('VIEWS_DIRECTORY', 'database/views')),
    'procedures_directory' => database_path(env('PROCEDURES_DIRECTORY', 'database/procedures')),

    /*
    |--------------------------------------------------------------------------
    | MQTT broker
    |--------------------------------------------------------------------------
     */
    'mqtt' => [
        'host' => env('MQTT_HOST', '127.0.0.1'),
        'port' => env('MQTT_PORT', 1883),
    ],
];
