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
         *    Listings / Index
         */
        'listings'       => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/listings',
            'summary'              => 'The new, faster way to list all ticket groups for an event.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/spaces/API/pages/2853797930/Listings+Index',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'event_id'          => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'ID of the Event for which you would like to list tickets.',
                    'required'    => true,
                ],
                'type'              => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by Ticket Group Type.',
                    'required'    => false,
                    'enum'        => [
                        'event',
                        'parking',
                    ]
                ],
                'quantity'          => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Filter by number of tickets greater than passed value.',
                    'required'    => false,
                ],
                'section'         => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by exact match section.',
                    'required'    => false,
                ],
                'row' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by exact match row.',
                    'required'    => false,
                ],
                'owned'        => [
                    'location'    => 'query',
                    'type'        => ['boolean', 'string'],
                    'description' => 'Show only your own listings.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'order_by'          => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Sort results by any return parameter.',
                    'required'    => false,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Ticket Groups / Show
         */
        'showListing'        => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/listings/{id}',
            'summary'          => 'Get a single Listing (Ticket Group).',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469964',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Listing to return.',
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
