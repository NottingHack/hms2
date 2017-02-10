<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | The permission strings for HMS
    |
    */
    'permissions' => [
        'profile.view.self',
        'profile.view.all',
        'role.view.all',
        'role.edit.all',
        'profile.edit.self',
        'profile.edit.all',
        'accessCodes.view',
        'meta.view',
        'meta.edit',
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | Default roles and the permissions to go with them
    |
    */
    'roles' => [
        'member.approval'   => [
            'name'          => 'Awaiting Approval',
            'description'   => 'Member awaiting approval',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        'member.payment'    => [
            'name'          => 'Awaiting Payment',
            'description'   => 'Awaiting standing order payment',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        'member.young'      => [
            'name'          => 'Young Hacker',
            'description'   => 'Member between 16 and 18',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        'member.ex'         => [
            'name'          => 'Ex Member',
            'description'   => 'Ex Member',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        'member.current'    => [
            'name'          => 'Current Member',
            'description'   => 'Current Member',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        'user.super'         => [
            'name'          => 'Super User',
            'description'   => 'Full access to all parts of the system',
            'permissions'   =>  [
                '*',
            ],
        ],
    ],

];
