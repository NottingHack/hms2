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
            'text' => '<i class="fad fa-home fa-lg"></i>',
            'route' => 'index',
            'permissions' => [],
        ],
        'projects' => [
            'text' => 'Projects',
            'route' => 'projects.index',
            'match' => 'projects.index',
            'permissions' => ['project.view.self'],
            'feature' => 'projects',
        ],
        'boxes' => [
            'text' => '<i class="fad fa-box-full fa-lg"></i>',
            'route' => 'boxes.index',
            'match' => 'boxes.index',
            'permissions' => ['box.view.self'],
            'feature' => 'boxes',
        ],
        'snackspaceTransactions' => [
            'text' => 'Snackspace',
            'route' => 'snackspace.transactions.index',
            'match' => 'snackspace.transactions.index',
            'permissions' => ['snackspace.transaction.view.self'],
            'feature' => 'snackspace',
        ],
        'tools' => [
            'text' => 'Tools',
            'route' => 'tools.index',
            'match' => 'tools.index',
            'permissions' => ['tools.view'],
            'feature' => 'tools',
        ],
        'teams' => [
            'text' => 'Teams',
            'route' => 'teams.index',
            'permissions' => ['team.view'],
        ],
        'codes' => [
            'text' => 'Space Access',
            'route' => 'gatekeeper.accessCodes',
            'permissions' => ['accessCodes.view'],
        ],
        'links' => [
            'text' => 'Links',
            'route' => 'links.index',
            'match' => 'links.index',
            'permissions' => [],
        ],
        'statistics' => [
            'text' => '<i class="fad fa-chart-line fa-lg"></i>',
            'permissions' => [],
            'links' => [
                'membership' => [
                    'text' => 'Membership',
                    'route' => 'statistics.membership',
                    'match' => 'statistics.membership',
                    'permissions' => [],
                ],
                'membership-graph' => [
                    'text' => 'Membership Graph',
                    'route' => 'statistics.membership-graph',
                    'match' => 'statistics.membership-graph',
                    'permissions' => [],
                ],
                'electric' => [
                    'text' => 'Electric',
                    'route' => 'instrumentation.electric.index',
                    'match' => 'instrumentation.electric.index',
                    'permissions' => [],
                ],
                'laser-usage' => [
                    'text' => 'Laser Usage',
                    'route' => 'statistics.laser-usage',
                    'match' => 'statistics.laser-usage',
                    'permissions' => [],
                    'feature' => 'tools',
                ],
                'box-usage' => [
                    'text' => 'Member\'s Boxes',
                    'route' => 'statistics.box-usage',
                    'match' => 'statistics.box-usage',
                    'permissions' => [],
                    'feature' => 'boxes',
                ],
                'snackspace-monthly' => [
                    'text' => 'RFID purchases & payments',
                    'route' => 'statistics.snackspace-monthly',
                    'match' => 'statistics.snackspace-monthly',
                    'permissions' => [],
                    'feature' => 'snackspace',
                ],
                'tools' => [
                    'text' => 'Tools',
                    'route' => 'statistics.tools',
                    'match' => 'statistics.tools',
                    'permissions' => [],
                    'feature' => 'tools',
                ],
                'zone-occupants' => [
                    'text' => 'Zone Occupants',
                    'route' => 'statistics.zone-occupants',
                    'match' => 'statistics.zone-occupants',
                    'permissions' => [],
                ],
            ],
        ],
        'vending' => [
            'text' => 'Vending',
            'permissions' => [],
            'feature' => 'vending',
            'links' => [
                'vendingMachines' => [
                    'text' => 'Machines',
                    'route' => 'snackspace.vending-machines.index',
                    'match' => 'snackspace.vending-machines.index',
                    'permissions' => ['snackspace.vendingMachine.view'],
                ],
                'products' => [
                    'text' => 'Products',
                    'route' => 'snackspace.products.index',
                    'match' => 'snackspace.products.index',
                    'permissions' => ['snackspace.product.view'],
                ],
            ],
        ],
        'finance' => [
            'text' => '<i class="fad fa-money-bill fa-lg"></i>',
            'permissions' => [],
            'links' => [
                'joinAccounts' => [
                    'text' => 'Joint Accounts',
                    'route' => 'banking.accounts.listJoint',
                    'match' => 'banking.accounts.listJoint',
                    'permissions' => ['bankTransactions.view.limited', 'bankTransactions.view.all'],
                ],
                'bank' => [
                    'text' => 'Banks',
                    'route' => 'banking.banks.index',
                    'match' => 'banking.banks.index',
                    'permissions' => ['bank.view'],
                ],
                'bankTransactions' => [
                    'text' => 'Reconcile Bank Transaction',
                    'route' => 'banking.bank-transactions.unmatched',
                    'match' => 'banking.bank-transactions.unmatched',
                    'permissions' => ['bankTransactions.reconcile'],
                ],
                'debt' => [
                    'text' => 'Snackspace Debt Graphs',
                    'route' => 'snackspace.debt-graph',
                    'match' => 'snackspace.debt-graph',
                    'permissions' => ['snackspace.debt.view'],
                    'feature' => 'snackspace',
                ],
                'paymentReport' => [
                    'text' => 'Snackspace Payments Report',
                    'route' => 'snackspace.payment-report',
                    'match' => 'snackspace.payment-report',
                    'permissions' => ['snackspace.transaction.view.all'],
                    'feature' => 'snackspace',
                ],
            ],
        ],
        'admin' => [
            'text' => '<i class="fad fa-toolbox fa-lg"></i>',
            'permissions' => [],
            'links' => [
                // 'user'         => [
                //     'text'          => 'Members',
                //     'route'         => 'users.index',
                //     'match'         => 'users.index',
                //     'permissions'   => ['profile.view.all'],
                //     'links'         => [],
                // ],
                'roles' => [
                    'text' => 'Roles',
                    'route' => 'roles.index',
                    'match' => 'roles.index',
                    'permissions' => ['role.view.all'],
                ],
                'meta' => [
                    'text' => 'Meta',
                    'route' => 'metas.index',
                    'match' => 'metas.index',
                    'permissions' => ['meta.view'],
                ],
                'content' => [
                    'text' => 'Content Blocks',
                    'route' => 'content-blocks.index',
                    'match' => 'content-blocks.index',
                    'permissions' => ['contentBlock.view'],
                ],
                'labels' => [
                    'text' => 'Label Templates',
                    'route' => 'labels.index',
                    'match' => 'labels.index',
                    'permissions' => ['labelTemplate.view'],
                    'feature' => 'label_printer',
                ],
                'inviteSearch' => [
                    'text' => 'Resend Invite',
                    'route' => 'membership.invites',
                    'permissions' => ['search.invites'],
                ],
                'emailMembers' => [
                    'text' => 'Email All Members',
                    'route' => 'email-members.draft',
                    'permissions' => ['email.allMembers'],
                    'feature' => 'email_all_members',
                ],
                'membershipApprovals' => [
                    'text' => 'Review Approvals',
                    'route' => 'membership.index',
                    'match' => 'membership.index',
                    'permissions' => ['membership.approval'],
                ],
                'csvDownload' => [
                    'text' => 'CSV Downloads',
                    'route' => 'csv-download.index',
                    'match' => 'csv-download.index',
                    'permissions' => ['profile.view.all'],
                ],
                'meetings' => [
                    'text' => 'Annual Meetings',
                    'route' => 'governance.meetings.index',
                    'match' => 'governance.meetings.index',
                    'permissions' => ['governance.meeting.view'],
                ],
                'registerOfMembers' => [
                    'text' => 'Register Of Members',
                    'route' => 'governance.registerOfMembers.index',
                    'match' => 'governance.registerOfMembers.index',
                    'permissions' => ['governance.registerOfMembers.view'],
                ],
                'registerOfDirectors' => [
                    'text' => 'Register Of Directors',
                    'route' => 'governance.registerOfDirectors.index',
                    'match' => 'governance.registerOfDirectors.index',
                    'permissions' => ['governance.registerOfDirectors.view'],
                ],
            ],
        ],
        'gatekeeper' => [
            'text' => '<i class="far fa-door-closed fa-lg"></i>',
            'permissions' => [],
            'links' => [
                'buildings' => [
                    'text' => 'Access State',
                    'route' => 'gatekeeper.access-state.index',
                    'match' => 'gatekeeper.access-state.index',
                    'permissions' => ['gatekeeper.access.manage'],
                ],
                'bookableAreas' => [
                    'text' => 'Bookable Areas',
                    'route' => 'gatekeeper.bookable-area.index',
                    'match' => 'gatekeeper.bookable-area.index',
                    'permissions' => ['gatekeeper.access.manage'],
                ],
                'temporaryAccess' => [
                    'text' => 'Temporary Access Bookings',
                    'route' => 'gatekeeper.temporary-access',
                    'match' => 'gatekeeper.temporary-access',
                    'permissions' => ['gatekeeper.temporaryAccess.view.all'],
                ],
                'accessLogs' => [
                    'text' => 'Access Logs',
                    'route' => 'access-logs.index',
                    'match' => 'access-logs.index',
                    'permissions' => ['profile.view.all'],
                ],
            ],
        ],
        'debug' => [
            'text' => '<i class="fad fa-bug fa-lg"></i>',
            'permissions' => [],
            'links' => [
                'telescope' => [
                    'text' => 'Telescope',
                    'route' => 'telescope',
                    'match' => 'telescope',
                    'permissions' => ['telescope.view'],
                ],
                'horizon' => [
                    'text' => 'Horizon',
                    'route' => 'horizon.index',
                    'match' => 'horizon.index',
                    'permissions' => ['horizon.view'],
                ],
                'logViewer' => [
                    'text' => 'Log Viewer',
                    'route' => 'log-viewer.index',
                    'match' => 'log-viewer.index',
                    'permissions' => ['logViewer.view'],
                ],
            ],
        ],
    ],
];
