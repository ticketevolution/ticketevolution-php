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
         *    Search
         */
        'search'                => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/search',
            'summary'              => 'Searches against given entities.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/display/API/Search',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'entities' => [
                    'location'    => 'query',
                    'type'        => 'array',
                    'description' => 'An array of entities for which you would like to get suggestions.',
                    'required'    => true,
                ],
                'q'        => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The search query for suggestions.',
                    'required'    => true,
                ],
                'limit'    => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'The limit of results you would like returned for each entity.',
                    'required'    => false,
                ],
                'fuzzy'    => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'If search results are to match any instead of all words passed.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Searching / Global / Suggestions
         */
        'listSearchSuggestions' => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/searches/suggestions',
            'summary'              => 'Provides multiple suggestions for each entity requested.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=6455318',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'entities'  => [
                    'location'    => 'query',
                    'type'        => 'array',
                    'description' => 'An array of entities for which you would like to get suggestions.',
                    'required'    => false,
                    'sentAs'      => 'entities',
                ],
                'entities2' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'An array of entities for which you would like to get suggestions.',
                    'required'    => false,
                    'sentAs'      => 'entities',
                ],
                'q'         => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The search query for suggestions.',
                    'required'    => true,
                ],
                'limit'     => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'The limit of results you would like returned for each entity.',
                    'required'    => false,
                ],
                'fuzzy'     => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'If search results are to match any instead of all words passed.',
                    'required'    => false,
                    'format'      => 'boolean-string',
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
