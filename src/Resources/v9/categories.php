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
         *    Categories / Index
         */
        'listCategories'        => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/categories',
            'summary'              => 'Get a list of all categories.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470005',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => null,
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Categories / Show
         */
        'showCategory'          => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/categories/{category_id}',
            'summary'              => 'Get a single category.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470098',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'category_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Category to return.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => null,
        ],


        /**
         *    Categories / Deleted
         */
        'listCategoriesDeleted' => [
            'extends'              => 'listCategories',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/categories/deleted',
            'summary'              => 'Obtain a list of Categories that have been deleted.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=31948905',
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
