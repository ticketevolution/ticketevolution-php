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
         *    Events / Index
         */
        'listEvents'        => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/events',
            'summary'              => 'Get a list of all events.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470092',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'min_and_max_price' => [
                    'location'    => 'query',
                    'type'        => ['boolean', 'string'],
                    'description' => 'If true the response will include avg_price, sum_price, median_price, min_price & max_price, calculated based on that Credential’s settings',
                    'required'    => false,
                    'default'     => false,
                    'format'      => 'boolean-string',
                ],
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
         *    Events / Show
         */
        'showEvent'         => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/events/{event_id}',
            'summary'              => 'Get a single event.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470094',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'event_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Event to return.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => null,
        ],


        /**
         *    Events / Deleted
         */
        'listEventsDeleted' => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/events/deleted',
            'summary'              => 'Obtain a list of Events that have been deleted.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=31948920',
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
         *    Events / Search
         */
        'searchEvents'      => [
            'extends'              => 'listEvents',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/events/search',
            'summary'              => 'Get a list of all events.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470096',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'q' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Full-text search events.',
                    'required'    => false,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Events / Pinned
         */
        'listEventsPinned'  => [
            'extends'          => 'listEvents',
            'httpMethod'       => 'GET',
            'uri'              => '/v9/events/pinned',
            'summary'          => 'Obtain a list of upcoming Events that have been Pinned by this User.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=34308120',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [],
        ],


        /**
         *    Events / Stats
         */
        'showEventStats'         => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/events/{event_id}/stats',
            'summary'              => 'Get statistics for a single event.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/spaces/API/pages/3057319937/Events+Stats',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'event_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Event for which to return statistics.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => null,
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
