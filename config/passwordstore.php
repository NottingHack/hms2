<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default PasswordStore Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "fileBased", "kerberos", "doctrine"
    |
    */
    'driver' => env('PASSWORDSTORE', 'fileBased'),

    /*
    |--------------------------------------------------------------------------
    | PasswordStore Drivers
    |--------------------------------------------------------------------------
    */
    'filebased' => [
        'name' => env('PASSWORDSTORE_FILE', 'users.json'),
    ],

    'kerberos' => [
        'username' => env('KRB_USERNAME'),
        'keytab' => env('KRB_KEYTAB'),
        'realm' => env('KRB_REALM'),
        'debug' => env('KRB_DEBUG', false),
    ],
];
