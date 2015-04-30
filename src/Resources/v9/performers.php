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
         *    Performers / Index
         */
        'listPerformers'        => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/performers',
            'summary'              => 'Get a list of all performers.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470084',
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
         *    Performers / Show
         */
        'showPerformer'         => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/performers/{performer_id}',
            'summary'              => 'Get a single performer.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470086',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'performer_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Performer to return.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => null,
        ],


        /**
         *    Performers / Deleted
         */
        'listPerformersDeleted' => [
            'extends'              => 'listPerformers',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/performers/deleted',
            'summary'              => 'Obtain a list of Performers that have been deleted.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=31948895',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'deleted_at.gte' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'ISO 8601 Date of deletion',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
                'deleted_at.lte' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'ISO 8601 Date of deletion',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Performers / Search
         */
        'searchPerformers'      => [
            'extends'              => 'listPerformers',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/performers/search',
            'summary'              => 'Get a list of all performers.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470088',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'q' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Full-text search performers.',
                    'required'    => true,
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
