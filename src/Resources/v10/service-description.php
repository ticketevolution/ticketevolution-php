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

    'apiVersion'  => 'v10',

    /*
    |--------------------------------------------------------------------------
    | Service Configurations
    |--------------------------------------------------------------------------
    |
    | Configuration files of specific service descriptions to load.
    |
    */

    'imports' => [
        'orders.php',
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
