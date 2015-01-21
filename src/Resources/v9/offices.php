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
         *    Offices / Index
         */
        'listOffices'             => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/offices',
            'summary'              => 'Get a list of all offices.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470028',
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
         *    Offices / Show
         */
        'showOffice'              => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/offices/{office_id}',
            'summary'          => 'Get a single office.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470035',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'office_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Office to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Offices / Search
         */
        'searchOffices'           => [
            'extends'              => 'listOffices',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/offices/search',
            'summary'              => 'Search across offices.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470037',
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


        /**
         *    Offices / Credit Cards / Index
         */
        'listOfficeCreditCards'   => [
            'extends'              => 'listOffices',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/offices/{office_id}/credit_cards',
            'summary'              => 'List Credit Card for a specified Office.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129301',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'office_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Office.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Offices / Credit Cards / Show
         */
//        'showOfficeCreditCard'        => [
//            'extends'          => null,
//            'httpMethod'       => 'GET',
//            'uri'              => '/v9/offices/{office_id}/credit_cards/{credit_card_id}',
//            'summary'          => 'Get a single office Credit Card.',
//            'notes'            => '',
//            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394167',
//            'deprecated'       => false,
//            'responseModel'    => 'defaultJsonResponse',
//            'parameters'       => [
//                'office_id' => [
//                    'location'    => 'uri',
//                    'type'        => 'integer',
//                    'description' => 'ID of the Office.',
//                    'required'    => true,
//                ],
//                'credit_card_id' => [
//                    'location'    => 'uri',
//                    'type'        => 'integer',
//                    'description' => 'ID of the Credit Card.',
//                    'required'    => true,
//                ],
//            ],
//        ],


        /**
         *    Offices / Credit Cards / Create
         */
        'createOfficeCreditCards' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/offices/{office_id}/credit_cards',
            'summary'          => 'Create one or more Credit Cards for an existing office.',
            'notes'            => 'Note that this takes an array of Credit Cards even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129312',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'office_id'    => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Office to return.',
                    'required'    => true,
                ],
                'credit_cards' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Credit Cards to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Offices / Credit Cards / Update
         */
//        'updateOfficeCreditCard'   => [
//            'extends'          => null,
//            'httpMethod'       => 'PUT',
//            'uri'              => '/v9/offices/{office_id}/credit_cards/{credit_card_id}',
//            'summary'          => 'Update a office Credit Card.',
//            'notes'            => '',
//            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394225',
//            'deprecated'       => false,
//            'responseModel'    => 'defaultJsonResponse',
//            'parameters'       => [
//                'office_id' => [
//                    'location'    => 'uri',
//                    'type'        => 'integer',
//                    'description' => 'ID of the Office.',
//                    'required'    => true,
//                ],
//                'credit_card_id' => [
//                    'location'    => 'uri',
//                    'type'        => 'integer',
//                    'description' => 'ID of the Credit Card.',
//                    'required'    => true,
//                ],
//            ],
//            'additionalParameters' => ['location' => 'json'],
//        ],


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
