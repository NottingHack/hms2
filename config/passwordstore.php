<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default PasswordStore Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "file", "kerberos", "doctrine"
    |
    */
    'driver' => env('PASSWORDSTORE', 'file'),

    /*
    |--------------------------------------------------------------------------
    | PasswordStore Drivers
    |--------------------------------------------------------------------------
    */
    'file' => [
        'name' => env('PASSWORDSTORE_FILE', 'users.json'),
    ],

    'kerberos' => [
        'username' => env('KRB_USERNAME'),
        'keytab' => env('KRB_KEYTAB'),
        'realm' => env('KRB_REALM'),
        'debug' => env('KRB_DEBUG', false),
    ],
];
