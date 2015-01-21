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
         *    Users / Index
         */
        'listUsers'   => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/users',
            'summary'              => 'Return all users for a brokerage.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470068',
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
         *    Users / Show
         */
        'showUser'    => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/users/{user_id}',
            'summary'          => 'Get a single user.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470081',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'user_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the User to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Users / Search
         */
        'searchUsers' => [
            'extends'              => 'listUsers',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/users/search',
            'summary'              => 'Search across users.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470083',
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
