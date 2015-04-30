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
         *    Companies / Index
         */
        'listCompanies'   => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/companies',
            'summary'              => 'Get a list of all companies.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=30572746',
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
         *    Companies / Show
         */
        'showCompany'     => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/companies/{company_id}',
            'summary'          => 'Get a single company.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=30572749',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'company_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Company to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Companies / Create
         */
        'createCompanies' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/companies',
            'summary'          => 'Create one or more Companies.',
            'notes'            => 'Note that this takes an array of Companies even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=30572753',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'companies' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Companies to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Companies / Update
         */
        'updateCompany'   => [
            'extends'              => null,
            'httpMethod'           => 'PUT',
            'uri'                  => '/v9/companies/{company_id}',
            'summary'              => 'Update a company.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=30572755',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'company_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The company number.',
                    'required'    => true,
                ],
                'name'       => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The company’s full name.',
                    'required'    => false,
                ],
            ],
            'additionalParameters' => ['location' => 'json'],
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
