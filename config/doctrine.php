<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Entity Mangers
    |--------------------------------------------------------------------------
    |
    | Configure your Entity Managers here. You can set a different connection
    | and driver per manager and configure events and filters. Change the
    | paths setting to the appropriate path and replace App namespace
    | by your own namespace.
    |
    | Available meta drivers: fluent|annotations|yaml|xml|config|static_php|php
    |
    | Available connections: mysql|oracle|pgsql|sqlite|sqlsrv
    | (Connections can be configured in the database config)
    |
    | --> Warning: Proxy auto generation should only be enabled in dev!
    |
    */
    'managers' => [
        'default' => [
            'dev' => env('APP_DEBUG', false),
            'meta' => env('DOCTRINE_METADATA', 'annotations'),
            'connection' => env('DB_CONNECTION', 'mysql'),
            'namespaces' => [
                'HMS\Entities',
            ],
            'paths' => [
                base_path('app/HMS/Mappings'),
            ],
            'repository' => Doctrine\ORM\EntityRepository::class,
            'proxies' => [
                'namespace' => 'DoctrineProxies',
                'path' => storage_path('proxies'),
                'auto_generate' => env('DOCTRINE_PROXY_AUTOGENERATE', false),
            ],
            /*
            |--------------------------------------------------------------------------
            | Doctrine events
            |--------------------------------------------------------------------------
            |
            | The listener array expects the key to be a Doctrine event
            | e.g. Doctrine\ORM\Events::onFlush
            |
            */
            'events' => [
                'listeners' => [],
                'subscribers' => [],
            ],
            'filters' => [],
            /*
            |--------------------------------------------------------------------------
            | Doctrine mapping types
            |--------------------------------------------------------------------------
            |
            | Link a Database Type to a Local Doctrine Type
            |
            | Using 'enum' => 'string' is the same of:
            | $doctrineManager->extendAll(function (\Doctrine\ORM\Configuration $configuration,
            |         \Doctrine\DBAL\Connection $connection,
            |         \Doctrine\Common\EventManager $eventManager) {
            |     $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
            | });
            |
            | References:
            | http://doctrine-orm.readthedocs.org/en/latest/cookbook/custom-mapping-types.html
            | http://doctrine-dbal.readthedocs.org/en/latest/reference/types.html#custom-mapping-types
            | http://doctrine-orm.readthedocs.org/en/latest/cookbook/advanced-field-value-conversion-using-custom-mapping-types.html
            | http://doctrine-orm.readthedocs.org/en/latest/reference/basic-mapping.html#reference-mapping-types
            | http://symfony.com/doc/current/cookbook/doctrine/dbal.html#registering-custom-mapping-types-in-the-schematool
            |--------------------------------------------------------------------------
            */
            'mapping_types' => [
                //'enum' => 'string'
            ],

            /**
             * References:
             * https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/architecture.html#middlewares
             */
            'middlewares' => [
                // Doctrine\DBAL\Logging\Middleware::class
            ],
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Doctrine Extensions
    |--------------------------------------------------------------------------
    |
    | Enable/disable Doctrine Extensions by adding or removing them from the list
    |
    | If you want to require custom extensions you will have to require
    | laravel-doctrine/extensions in your composer.json
    |
    */
    'extensions' => [
        //LaravelDoctrine\ORM\Extensions\TablePrefix\TablePrefixExtension::class,
        LaravelDoctrine\Extensions\Timestamps\TimestampableExtension::class,
        LaravelDoctrine\Extensions\SoftDeletes\SoftDeleteableExtension::class,
        //LaravelDoctrine\Extensions\Sluggable\SluggableExtension::class,
        //LaravelDoctrine\Extensions\Sortable\SortableExtension::class,
        //LaravelDoctrine\Extensions\Tree\TreeExtension::class,
        //LaravelDoctrine\Extensions\Loggable\LoggableExtension::class,
        //LaravelDoctrine\Extensions\Blameable\BlameableExtension::class,
        //LaravelDoctrine\Extensions\IpTraceable\IpTraceableExtension::class,
        //LaravelDoctrine\Extensions\Translatable\TranslatableExtension::class
    ],
    /*
    |--------------------------------------------------------------------------
    | Doctrine custom types
    |--------------------------------------------------------------------------
    |
    | Create a custom or override a Doctrine Type
    |--------------------------------------------------------------------------
    */
    'custom_types' => [
        'datetime' => HMS\Doctrine\CarbonType::class,
        'date' => HMS\Doctrine\CarbonDateType::class,
        'time' => HMS\Doctrine\CarbonTimeType::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | DQL custom datetime functions
    |--------------------------------------------------------------------------
    */
    'custom_datetime_functions' => [
        'SEC_TO_TIME' => DoctrineExtensions\Query\Mysql\SecToTime::class,
        'DATE' => DoctrineExtensions\Query\Mysql\Date::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | DQL custom numeric functions
    |--------------------------------------------------------------------------
    */
    'custom_numeric_functions' => [],
    /*
    |--------------------------------------------------------------------------
    | DQL custom string functions
    |--------------------------------------------------------------------------
    */
    'custom_string_functions' => [
        'CAST' => DoctrineExtensions\Query\Mysql\Cast::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Register custom hydrators
    |--------------------------------------------------------------------------
    */
    'custom_hydration_modes' => [
        // e.g. 'hydrationModeName' => MyHydrator::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Configure meta-data, query and result caching here.
    | Optionally you can enable second level caching.
    |
    | Available: apc|array|file|memcached|redis|void
    |
    */
    'cache' => [
        'second_level' => false,
        'default' => env('DOCTRINE_CACHE', 'array'),
        'namespace' => null,
        'metadata' => [
            'driver' => env('DOCTRINE_METADATA_CACHE', env('DOCTRINE_CACHE', 'array')),
            'namespace' => 'metadata',
        ],
        'query' => [
            'driver' => env('DOCTRINE_QUERY_CACHE', env('DOCTRINE_CACHE', 'array')),
            'namespace' => 'query',
        ],
        'result' => [
            'driver' => env('DOCTRINE_RESULT_CACHE', env('DOCTRINE_CACHE', 'array')),
            'namespace' => 'result',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Gedmo extensions
    |--------------------------------------------------------------------------
    |
    | Settings for Gedmo extensions
    | If you want to use this you will have to require
    | laravel-doctrine/extensions in your composer.json
    |
    */
    'gedmo' => [
        'all_mappings' => false,
    ],
    /*
     |--------------------------------------------------------------------------
     | Validation
     |--------------------------------------------------------------------------
     |
     |  Enables the Doctrine Presence Verifier for Validation
     |
     */
    'doctrine_presence_verifier' => true,

    /*
     |--------------------------------------------------------------------------
     | Notifications
     |--------------------------------------------------------------------------
     |
     |  Doctrine notifications channel
     |
     */
    'notifications' => [
        'channel' => 'database',
    ],
];
