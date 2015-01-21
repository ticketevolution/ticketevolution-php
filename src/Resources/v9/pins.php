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
         *    Pins / Index
         */
        'listPins'   => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/pins',
            'summary'              => 'Get a list of all pins.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=34308114',
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
                'embedded' => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'If true the response will only contain the id and url of the Pin.',
                    'required'    => false,
                    'default'     => false,
                    'format'      => 'boolean-string',
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Pins / Show
         */
        'showPin'    => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/pins/{pin_id}',
            'summary'              => 'Get a single pin.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470086',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'pin_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Pin to return.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => null,
        ],


        /**
         *    Pins / Create
         */
        'createPins' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/pins',
            'summary'          => 'Create one or more Pins.',
            'notes'            => 'Note that this takes an array of Pins even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=34308109',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'pins' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Pins to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Pins / Delete
         */
        'deletePin'  => [
            'extends'          => null,
            'httpMethod'       => 'DELETE',
            'uri'              => '/v9/pins/{pin_id}',
            'summary'          => 'Delete the specified Pin.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=34308106',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'pin_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the pin.',
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
