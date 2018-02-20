<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Operations
    |--------------------------------------------------------------------------
    |
    | This array of operations is translated into methods that complete these
    | requests based on their configuration.
    |
    */

    'operations' => [

        /**
         *    Orders / Create
         */
        'createOrders'                 => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v10/orders',
            'summary'          => 'Create an Order.',
            'notes'            => 'Creates a single order.',
            'documentationUrl' => null,
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order' => [
                    'location'    => 'json',
                    'type'        => 'object',
                    'description' => 'An Order to create.',
                    'required'    => true,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | This array of models is specifications to returning the response
    | from the operation methods.
    |
    */

    'models'     => [],
];
