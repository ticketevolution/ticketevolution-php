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
         *    Promotion Codes / Index
         */
        'listPromotionCodes' => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/promotion_codes',
            'summary'              => 'Get a list of all Promotion Codes.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994378',
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
                'code'     => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter results by case-insensitive code.',
                    'required'    => false,
                ],
                'inactive' => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Display inactive results (default is active-only).',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Promotion Codes / Show
         */
        'showPromotionCode'  => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/promotion_codes/{promotion_code_id}',
            'summary'              => 'Get a single Promotion Code.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994380',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'promotion_code_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Promotion Code.',
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
