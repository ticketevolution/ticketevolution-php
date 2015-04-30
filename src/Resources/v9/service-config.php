<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Service Name
    |--------------------------------------------------------------------------
    |
    | Name of the API service these description configs are for.
    |
    */

    'name'        => 'Ticket Evolution',

    /*
    |--------------------------------------------------------------------------
    | Service Description
    |--------------------------------------------------------------------------
    |
    | Description of the API service.
    |
    */

    'description' => 'A Ticket Evolution API Wrapper built using Guzzle.',

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | Description of the API service.
    |
    */

    'apiVersion'  => 'v9',

    /*
    |--------------------------------------------------------------------------
    | Service Configurations
    |--------------------------------------------------------------------------
    |
    | Configuration files of specfic service descriptions to load.
    |
    */

    'services'    => [
        'accounts',
        'brokerages',
        'categories',
        'clients',
        'commissions',
        'companies',
        'credentials',
        'credit_memos',
        'events',
        'filtered_tickets',
        'offices',
        'orders',
        'payments',
        'performers',
        'pins',
        'promotion_codes',
        'queued_orders',
        'quotes',
        'rate_options',
        'reports',
        'search',
        'settings',
        'shipments',
        'ticket_groups',
        'tickets',
        'users',
        'venues',
        'configurations',
    ],


    /*
    |--------------------------------------------------------------------------
    | Default models
    |--------------------------------------------------------------------------
    |
    | Default response models for typical usage of responses
    |
    */

    'models'      => [
        'defaultJsonResponse' => [
            'type'                 => 'object',
            'additionalProperties' => [
                'location' => 'json',
            ],
        ],
    ],
];
