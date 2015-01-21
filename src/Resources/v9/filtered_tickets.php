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
         *    Filtered Tickets / Held
         */
        'listFilteredTicketsHeld'  => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/filtered_tickets/held',
            'summary'          => 'Display tickets that have been reserved with a "hold" action.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994314',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'page'     => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Which page of results to return.',
                    'required'    => false,
                    'default'     => 1,
                ],
                'per_page' => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'The number of items for each page of results.',
                    'required'    => false,
                    'default'     => 100,
                ],
            ],
        ],


        /**
         *    Filtered Tickets / Taken
         */
        'listFilteredTicketsTaken' => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/filtered_tickets/taken',
            'summary'          => 'Tickets that have been reserved for a buyer with a "take" action.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994317',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'page'     => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Which page of results to return.',
                    'required'    => false,
                    'default'     => 1,
                ],
                'per_page' => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'The number of items for each page of results.',
                    'required'    => false,
                    'default'     => 100,
                ],
            ],
        ],


        /**
         *    Brokerages / Search
         */
        'searchBrokerages'         => [
            'extends'              => 'listBrokerages',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/brokerages/search',
            'summary'              => 'Search across brokerages.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=25002003',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'q' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Search query',
                    'required'    => true
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
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
