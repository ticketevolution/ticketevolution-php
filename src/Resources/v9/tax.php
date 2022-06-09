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
         *    Tax Quotes / Create
         */
        'createTaxQuote' => [
            'extends'              => null,
            'httpMethod'           => 'POST',
            'uri'                  => '/v9/tax_quotes',
            'summary'              => 'Create a tax quote.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/spaces/API/pages/2898427919/WIP+Tax+Quotes+Create',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'quantity'        => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'The number of tickets the customer is purchasing.',
                    'required'    => true,
                ],
                'ticket_group_id' => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'The ID of the ticket group from which the customer is purchasing.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => ['location' => 'json'],
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

    'models' => [],
];
