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
        'home' => [
            'text'          => '<i class="fas fa-home fa-lg"></i>',
            'route'         => 'index',
            'permissions'   => [],
            'links'         => [],
        ],
        'projects' => [
            'text'          => 'Projects',
            'route'         => 'projects.index',
            'match'         => 'projects.index',
            'permissions'   => ['project.view.self'],
            'links'         => [],
        ],
        'boxes' => [
            'text'          => 'Boxes',
            'route'         => 'boxes.index',
            'match'         => 'boxes.index',
            'permissions'   => ['box.view.self'],
            'links'         => [],
        ],
        'snackspaceTransactions' => [
            'text'          => 'Snackspace',
            'route'         => 'snackspace.transactions.index',
            'match'         => 'snackspace.transactions.index',
            'permissions'   => ['snackspace.transaction.view.self'],
            'links'         => [],
        ],
        'tools' => [
            'text'          => 'Tools',
            'route'         => 'tools.index',
            'match'         => 'tools.index',
            'permissions'   => ['tools.view'],
            'links'         => [],
        ],
        'teams' => [
            'text'          => 'Teams',
            'route'         => 'teams.index',
            'permissions'   => ['team.view'],
            'links'         => [],
        ],
        'codes' => [
            'text'          => 'Access Codes',
            'route'         => 'accessCodes',
            'permissions'   => ['accessCodes.view'],
            'links'         => [],
        ],
        'links' => [
            'text'          => 'Links',
            'route'         => 'links.index',
            'match'         => 'links.index',
            'permissions'   => [],
            'links'         => [],
        ],
        'statistics' => [
            'text' => '<i class="fas fa-chart-line fa-lg"></i>',
            'permissions'   => [],
            'links'         => [
                'membership' => [
                    'text'          => 'Membership',
                    'route'         => 'statistics.membership',
                    'match'         => 'statistics.membership',
                    'permissions'   => [],
                    'links'         => [],
                ],
                'snackspace-monthly' => [
                    'text'          => 'RFID purchases & payments',
                    'route'         => 'statistics.snackspace-monthly',
                    'match'         => 'statistics.snackspace-monthly',
                    'permissions'   => [],
                    'links'         => [],
                ],
                'laser-usage' => [
                    'text'          => 'Laser Usage',
                    'route'         => 'statistics.laser-usage',
                    'match'         => 'statistics.laser-usage',
                    'permissions'   => [],
                    'links'         => [],
                ],
                'box-usage' => [
                    'text'          => "Member's Boxes",
                    'route'         => 'statistics.box-usage',
                    'match'         => 'statistics.box-usage',
                    'permissions'   => [],
                    'links'         => [],
                ],
                'electric' => [
                    'text'          => 'Electric',
                    'route'         => 'instrumentation.electric.index',
                    'match'         => 'instrumentation.electric.index',
                    'permissions'   => [],
                    'links'         => [],
                ],
                'zone-occupants' => [
                    'text'          => 'Zone Occupants',
                    'route'         => 'statistics.zone-occupants',
                    'match'         => 'statistics.zone-occupants',
                    'permissions'   => [],
                    'links'         => [],
                ],
            ],
        ],
        'vending' => [
            'text'          => 'Vending',
            'permissions'   => [],
            'links'         => [
                'vendingMachines'          => [
                    'text'          => 'Machines',
                    'route'         => 'snackspace.vending-machines.index',
                    'match'         => 'snackspace.vending-machines.index',
                    'permissions'   => ['snackspace.vendingMachine.view'],
                    'links'         => [],
                ],
                'products'      => [
                    'text'          => 'Products',
                    'route'         => 'snackspace.products.index',
                    'match'         => 'snackspace.products.index',
                    'permissions'   => ['snackspace.product.view'],
                    'links'         => [],
                ],
            ],
        ],
        'finance' => [
            'text' => '<i class="far fa-money-bill fa-lg"></i>',
            'permissions'   => [],
            'links'         => [
                'joinAccounts' => [
                    'text'          => 'Joint Accounts',
                    'route'         => 'banking.accounts.listJoint',
                    'match'         => 'banking.accounts.listJoint',
                    'permissions'   => ['profile.view.limited', 'profile.view.all'],
                    'links'         => [],
                ],
                'bankTransactions' => [
                    'text'          => 'Reconcile Bank Transaction',
                    'route'         => 'bank-transactions.unmatched',
                    'match'         => 'bank-transactions.unmatched',
                    'permissions'   => ['bankTransactions.reconcile'],
                    'links'         => [],
                ],
                'debt'      => [
                    'text'          => 'Snackspace Debt Graphs',
                    'route'         => 'snackspace.debt-graph',
                    'match'         => 'snackspace.debt-graph',
                    'permissions'   => ['snackspace.debt.view'],
                    'links'         => [],
                ],
            ],
        ],
        'admin' => [
            'text'          => '<i class="fas fa-toolbox fa-lg"></i>',
            'permissions'   => [],
            'links'         => [
                // 'dashboard'         => [
                //     'text'          => 'Dashboard',
                //     'route'         => 'admin',
                //     'match'         => 'admin',
                //     'permissions'   => ['profile.view.all'],
                //     'links'         => [],
                // ],
                // 'user'         => [
                //     'text'          => 'Members',
                //     'route'         => 'users.index',
                //     'match'         => 'users.index',
                //     'permissions'   => ['profile.view.all'],
                //     'links'         => [],
                // ],
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
                'labels'          => [
                    'text'          => 'Label Templates',
                    'route'         => 'labels.index',
                    'match'         => 'labels.index',
                    'permissions'   => ['labelTemplate.view'],
                    'links'         => [],
                ],
                'inviteSearch' => [
                    'text'          => 'Resend Invite',
                    'route'         => 'membership.invites',
                    'permissions'   => ['search.invites'],
                    'links'         => [],
                ],
                'emailMembers' => [
                    'text'          => 'Email All Members',
                    'route'         => 'email-members.draft',
                    'permissions'   => ['email.allMembers'],
                    'links'         => [],
                ],
                'membershipApprovals' => [
                    'text'          => 'Review Approvals',
                    'route'         => 'membership.index',
                    'permissions'   => ['membership.approval'],
                    'links'         => [],
                ],
                'csvDownload' => [
                    'text'          => 'CSV Downloads',
                    'route'         => 'csv-download.index',
                    'permissions'   => ['profile.view.all'],
                    'links'         => [],
                ],
                'meetings' => [
                    'text'          => 'Annual Meetings',
                    'route'         => 'governance.meetings.index',
                    'permissions'   => ['governance.meeting.view'],
                    'links'         => [],
                ],
                'horizon'          => [
                    'text'          => 'Horizon',
                    'route'         => 'horizon.index',
                    'match'         => 'horizon.index',
                    'permissions'   => ['horizon.view'],
                    'links'         => [],
                ],
            ],
        ],
    ],
];
