<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Main Navigation
    |--------------------------------------------------------------------------
    |
    | Multi-level navigation array
    | Only two levels currently implemented in view
    |
    */
    'main' => [
        'news' => [
            'text'          => 'News',
            'route'         => 'home',
            'permissions'   => [],
            'links'         => [],
        ],
        'codes' => [
            'text'          => 'Access Codes',
            'route'         => 'accessCodes',
            'permissions'   => ['accessCodes.view'],
            'links'         => [],
        ],
        'admin' => [
            'text'          => 'Admin',
            'route'         => 'admin',
            'permissions'   => [],
            'links'         => [
                'roles'         => [
                    'text'          => 'Roles',
                    'route'         => 'roles.index',
                    'match'         => 'roles.index',
                    'permissions'   => ['role.view.all'],
                    'links'         => [],
                ],
                'meta'          => [
                    'text'          => 'Meta',
                    'route'         => 'metas.index',
                    'match'         => 'metas.index',
                    'permissions'   => ['meta.view'],
                    'links'         => [],
                ],
            ],
        ],
    ],
];
