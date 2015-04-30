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
         *    Accounts / Index
         */
        'listAccounts'       => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/accounts',
            'summary'              => 'Get a list of all accounts.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751480',
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
         *    Accounts / Show
         */
        'showAccount'        => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/accounts/{account_id}',
            'summary'          => 'Get a single account.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751482',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'account_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Account to return.',
                    'required'    => true,
                ],
            ],
        ],

        /**
         *    Transactions / Index
         */
        'listTransactions'   => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/accounts/{account_id}/transactions',
            'summary'              => 'Get a list of all transactions.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751489',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'page'       => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Which page of results to return.',
                    'required'    => false,
                    'default'     => 1,
                ],
                'per_page'   => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'The integer of items for each page of results.',
                    'required'    => false,
                    'default'     => 100,
                ],
                'account_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The account number.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Transactions / Show
         */
        'showTransaction'    => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/accounts/{account_id}/transactions/{transaction_id}',
            'summary'          => 'Get a single transaction.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751495',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'account_id'     => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The account number.',
                    'required'    => true,
                ],
                'transaction_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The transaction number.',
                    'required'    => true,
                ],
            ],
        ],

        /**
         *    Transactions / Create
         */
        'createTransactions' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/accounts/{account_id}/transactions',
            'summary'          => 'Get a list of all transactions.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751480',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'account_id'   => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The account number.',
                    'required'    => true,
                ],
                'transactions' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of transactions to create.',
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
