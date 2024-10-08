<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Repository Interface Namespace
    |--------------------------------------------------------------------------
    |
    | This base namespace prefix will be used with each of the repositories listed
    | below to generate the full interface and implementation class names.
    |
    */
    'repository_namespace' => 'HMS\Repositories',

    /*
    |--------------------------------------------------------------------------
    | Entity Namespace
    |--------------------------------------------------------------------------
    |
    | This base prefix will be used with each repositories listed below to
    | generate the Entity full class name.
    |
    */
    'entity_namespace' => 'HMS\Entities',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Repositories
    |--------------------------------------------------------------------------
    |
    | The repositories listed here will be automatically loaded on the
    | request to your application.
    |
    */
    'repositories' => [
        'Link',
        'Meta',
        'ContentBlock',
        'Role',
        'User',
        'Invite',
        'Profile',
        'Banking\Account',
        'Email',
        'RoleUpdate',
        'LabelTemplate',
        'Gatekeeper\Building',
        'Gatekeeper\BookableArea',
        'Gatekeeper\Pin',
        'Gatekeeper\RfidTag',
        'Gatekeeper\Door',
        'Gatekeeper\Zone',
        'Gatekeeper\ZoneOccupant',
        'Gatekeeper\ZoneOccupancyLog',
        'Gatekeeper\AccessLog',
        'Gatekeeper\TemporaryAccessBooking',
        'Governance\Meeting',
        'Governance\Proxy',
        'Governance\RegisterOfMembers',
        'Governance\RegisterOfDirectors',
        'Banking\Bank',
        'Banking\BankTransaction',
        'Banking\MembershipStatusNotification',
        'Banking\Stripe\Charge',
        'Banking\Stripe\Event',
        'Members\Project',
        'Members\Box',
        'Membership\RejectedLog',
        'Snackspace\Debt',
        'Snackspace\Product',
        'Snackspace\Transaction',
        'Snackspace\PurchasePayment',
        'Snackspace\VendingMachine',
        'Snackspace\VendingLocation',
        'Snackspace\VendLog',
        'Tools\Tool',
        'Tools\Booking',
        'Tools\Usage',
        'Instrumentation\BarometricPressure',
        'Instrumentation\ElectricMeter',
        'Instrumentation\ElectricReading',
        'Instrumentation\Event',
        'Instrumentation\Humidity',
        'Instrumentation\LightLevel',
        'Instrumentation\MacAddress',
        'Instrumentation\SensorBattery',
        'Instrumentation\Service',
        'Instrumentation\Temperature',
    ],
];
