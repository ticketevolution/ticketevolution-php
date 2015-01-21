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
         *    Venues / Index
         */
        'listVenues'        => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/venues',
            'summary'              => 'Get a list of all venues.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470018',
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
         *    Venues / Show
         */
        'showVenue'         => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/venues/{venue_id}',
            'summary'              => 'Get a single venue.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470020',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'venue_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Venue to return.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => null,
        ],


        /**
         *    Venues / Deleted
         */
        'listVenuesDeleted' => [
            'extends'              => 'listVenues',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/venues/deleted',
            'summary'              => 'Obtain a list of Venues that have been deleted.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=31948891',
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
         *    Venues / Search
         */
        'searchVenues'      => [
            'extends'              => 'listVenues',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/venues/search',
            'summary'              => 'Get a list of all venues.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470023',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'q' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Full-text search venues.',
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
