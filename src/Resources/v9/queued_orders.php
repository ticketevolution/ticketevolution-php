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
         *    Queued Orders / Index
         */
        'listQueuedOrders'       => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/queued_orders',
            'summary'              => 'Get a list of all Queued Orders.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994281',
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
                    'description' => 'The integer of items for each page of results.',
                    'required'    => false,
                    'default'     => 100,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Queued Orders / Show
         */
        'showQueuedOrder'        => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/queued_orders/{queued_order_id}',
            'summary'              => 'Get a single category.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994286',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'queued_order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Queued Order.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => null,
        ],


        /**
         *    Queued Orders / Recent
         */
        'listRecentQueuedOrders' => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/queued_orders/recent',
            'summary'              => 'List all orders that have been queued for background processing in the last 48 hours.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994284',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [],
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
