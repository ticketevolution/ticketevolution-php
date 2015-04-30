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
         *    Quotes / Index
         */
        'listQuotes'                  => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/quotes',
            'summary'              => 'Get a list of all quotes.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=5341218',
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
         *    Quotes / Show
         */
        'showQuote'                   => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/quotes/{quote_id}',
            'summary'          => 'Get a single quote.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=5341220',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'quote_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Quote to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Quotes / Create
         */
        'createQuotes'                => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/quotes',
            'summary'          => 'Create one or more quotes.',
            'notes'            => 'Note that this takes an array of Quotes even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=5341222',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'quotes' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Quotes to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Quotes / Update
         */
        'updateQuote'                 => [
            'extends'          => null,
            'httpMethod'       => 'PUT',
            'uri'              => '/v9/quotes/{quote_id}',
            'summary'          => 'Update a quote.',
            'notes'            => 'At this time, only updating state is supported.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=5341224',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'quote_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The quote number.',
                    'required'    => true,
                ],
                'state'    => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'Verb describing transition.',
                    'required'    => true,
                    'enum'        => ['convert', 'cancel'],
                ],
            ],
        ],


        /**
         *    Quotes / Resend
         */
        'resendQuote'                 => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/quotes/{quote_id}/resend',
            'summary'          => 'Resend a quote after it has been created.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470145',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'quote_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Quote to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Quotes / Autocomplete
         */
        'searchQuotesForAutocomplete' => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/quotes/autocomplete',
            'summary'          => 'Retrieve autocomplete field data.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=5341228',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'q'            => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Search term.',
                    'required'    => true,
                ],
                'search_field' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Field for which you would like data.',
                    'required'    => true,
                    'enum'        => ['events', 'venues', 'name', 'email', 'prepared_for'],
                ],
                'limit'        => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Number of results.',
                    'required'    => false,
                    'default'     => 8,
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
