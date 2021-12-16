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
    | Configuration files of specific service descriptions to load.
    |
    */

    'imports' => [
        'accounts.php',
        'brokerages.php',
        'categories.php',
        'clients.php',
        'commissions.php',
        'companies.php',
        'credentials.php',
        'credit_memos.php',
        'events.php',
        'filtered_tickets.php',
        'listings.php',
        'offices.php',
        'orders.php',
        'payments.php',
        'performers.php',
        'pins.php',
        'promotion_codes.php',
        'queued_orders.php',
        'quotes.php',
        'rate_options.php',
        'reports.php',
        'search.php',
        'settings.php',
        'shipments.php',
        'tax.php',
        'ticket_groups.php',
        'tickets.php',
        'users.php',
        'venues.php',
        'configurations.php',
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
