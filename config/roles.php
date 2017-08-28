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
        'membership.approval',
        'membership.updateDetails',
        'link.view',
        'link.create',
        'link.edit',
        'labelTemplate.view',
        'labelTemplate.create',
        'labelTemplate.edit',
        'labelTemplate.print',
        'search.users',
        'project.create.self',
        'project.view.self',
        'project.view.all',
        'project.edit.self',
        'project.edit.all',
        'project.printLabel.self',
        'project.printLabel.all',
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
                'link.view',
                'membership.updateDetails',
            ],
        ],
        'member.payment'    => [
            'name'          => 'Awaiting Payment',
            'description'   => 'Awaiting standing order payment',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
                'link.view',
            ],
        ],
        'member.young'      => [
            'name'          => 'Young Hacker',
            'description'   => 'Member between 16 and 18',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
                'link.view',
                'project.create.self',
                'project.view.self',
                'project.edit.self',
                'project.printLabel.self',
            ],
        ],
        'member.ex'         => [
            'name'          => 'Ex Member',
            'description'   => 'Ex Member',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
                'link.view',
            ],
        ],
        'member.current'    => [
            'name'          => 'Current Member',
            'description'   => 'Current Member',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
                'accessCodes.view',
                'link.view',
                'project.create.self',
                'project.view.self',
                'project.edit.self',
                'project.printLabel.self',
            ],
        ],
        'member.temporarybanned'    => [
            'name'          => 'Temporary Banned Member',
            'description'   => 'Temporary Banned Member',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
                'link.view',
            ],
        ],
        'member.banned'    => [
            'name'          => 'Banned Member',
            'description'   => 'Banned Member',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
                'link.view',
            ],
        ],
        'user.super'         => [
            'name'          => 'Super User',
            'description'   => 'Full access to all parts of the system',
            'permissions'   =>  [
                '*',
            ],
        ],
        'team.membership'    => [
            'name'          => 'Membership Team',
            'description'   => 'Membership Team',
            'email'         => 'membership@nottinghack.org.uk',
            'slackChannel'  => '#membership',
            'permissions'   => [
                'profile.view.all',
                'membership.approval',
                'search.users',
                'project.view.all',
                'project.edit.all',
                'project.printLabel.all',
            ],
        ],
        'team.trustees'    => [
            'name'          => 'Trustees',
            'description'   => 'The Trustees',
            'email'         => 'trustees@nottinghack.org.uk',
            'slackChannel'  => '#general',
            'permissions'   => [
                'profile.view.all',
                'profile.edit.all',
                'meta.view',
                'meta.edit',
                'membership.approval',
                'search.users',
                'project.view.all',
                'project.edit.all',
                'project.printLabel.all',
            ],
        ],
        'team.software'    => [
            'name'          => 'Software Team',
            'description'   => 'Software Team',
            'email'         => 'software@nottinghack.org.uk',
            'slackChannel'  => '#software',
            'permissions'   => [
                'profile.view.all',
                'profile.edit.all',
                'role.view.all',
                'role.edit.all',
                'meta.view',
                'meta.edit',
                'membership.approval',
                'search.users',
                'link.view',
                'link.create',
                'link.edit',
                'labelTemplate.view',
                'labelTemplate.create',
                'labelTemplate.edit',
                'labelTemplate.print',
            ],
        ],
    ],

];
