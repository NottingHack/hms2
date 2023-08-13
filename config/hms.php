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
        'standing_order_membership_payments' => env('FEATURE_STANDING_ORDER_MEMBERSHIP_PAYMENTS', true),
        'cash_membership_payments' => env('FEATURE_CASH_MEMBERSHIP_PAYMENTS_ENABLE', false),
        'ofx_bank_upload' => env('FEATURE_OFX_BANK_UPLOAD_ENABLE', false),
        'match_legacy_ref' => env('FEATURE_MATCH_LEGACY_REF', false),
        'email_all_members' => env('FEATURE_EMAIL_ALL_MEMBERS', true)
            && (env('MAILGUN_DOMAIN', false) && env('MAILGUN_SECRET', false)),
        'voting_status' => env('FEATURE_VOTING_STATUS', true),
        'members_enroll_pin' => env('FEATURE_MEMBERS_ENROLL_PIN', true),
        'space_api' => env('FEATURE_SPACEAPI', true),
        'mw_auth_hms' => env('FEATURE_MW_AUTH_WIKI', true),
        'slack' => env('FEATURE_SLACK', false),
        'discord' => env('FEATURE_DISCORD', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Account reference prefix
    |--------------------------------------------------------------------------
    | The prefix used to generate and match Payment references
    | Payment reference is 16 characters long, recommend prefix length is no more than 6 characters
    */
    'account_prefix' => strtoupper(env('ACCOUNT_REFERENCE_PREFIX', 'HSNTSB')),

    /*
    |--------------------------------------------------------------------------
    | Account legacy matching regular expression
    |--------------------------------------------------------------------------
    | A regular expression to match descriptions with the Account legacyRef column
    | Needed when FEATURE_MATCH_LEGACY_REF is enabled
    */
    'account_legacy_regex' => env('ACCOUNT_LEGACY_REGEX', '/\b(M0[0-9]+)\b/'),

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

];
