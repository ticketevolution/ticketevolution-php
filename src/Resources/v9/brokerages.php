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
         *    Brokerages / Index
         */
        'listBrokerages'   => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/brokerages',
            'summary'              => 'Get a list of all brokerages.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=25001994',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
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
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Brokerages / Show
         */
        'showBrokerage'    => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/brokerages/{brokerage_id}',
            'summary'          => 'Get a single brokerage.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=25002003',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'brokerage_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Brokerage to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Brokerages / Search
         */
        'searchBrokerages' => [
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
