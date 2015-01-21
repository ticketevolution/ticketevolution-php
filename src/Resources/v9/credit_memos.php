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
         *    Credit Memos / Index
         */
        'listCreditMemos'          => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/credit_memos',
            'summary'              => 'Get a list of all Credit Memos.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470159',
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
         *    Credit Memos / Show
         */
        'showCreditMemo'           => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/credit_memos/{credit_memo_id}',
            'summary'          => 'Get a single Credit Memo.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470162',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'credit_memo_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit memo to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Credit Memos / Cards
         */
        'listCreditMemoCards'      => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/credit_memos/{credit_memo_id}/cards',
            'summary'          => 'Display credit card transactions on the order that created the credit memo.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470164',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'credit_memo_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit memo to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Credit Memos / Update
         */
        'updateCreditMemo'         => [
            'extends'          => null,
            'httpMethod'       => 'PUT',
            'uri'              => '/v9/credit_memos/{credit_memo_id}',
            'summary'          => 'Update the Quickbooks remote_id parameter for this credit memo.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994259',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'credit_memo_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit memo to return.',
                    'required'    => true,
                ],
                'remote_id'      => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'The remote_id parameter for a credit memo exists to store a remote ID from another
                                      system. It was designed with Quickbooks in mind, but could be used for any remote
                                      application. This endpoint will update that parameter only.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Credit Memo Payments / Index
         */
        'listCreditMemoPayments'   => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/credit_memos/{credit_memo_id}/credit_memo_payments',
            'summary'          => 'List payments for a credit memo.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994245',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'credit_memo_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit memo.',
                    'required'    => true,
                ],
            ],
        ],

        /**
         *    Credit Memo Payments / Show
         */
        'showCreditMemoPayment'    => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/credit_memos/{credit_memo_id}/credit_memo_payments/{credit_memo_payment_id}',
            'summary'          => 'Display details about a credit memo payment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994247',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'credit_memo_id'         => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit Memo.',
                    'required'    => true,
                ],
                'credit_memo_payment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit Memo Payment.',
                    'required'    => true,
                ],
            ],
        ],

        /**
         *    Credit Memo Payments / Create
         */
        'createCreditMemoPayments' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/credit_memos/{credit_memo_id}/credit_memo_payments',
            'summary'          => 'Create a payment for a credit memo.',
            'notes'            => 'Note that this takes an array of Credit Memo Payments even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994249',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'credit_memo_id'       => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit Memo.',
                    'required'    => true,
                ],
                'credit_memo_payments' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Credit Memo Payments to create.',
                    'required'    => true,
                ],
            ],
        ],

        /**
         *    Credit Memo Payments / Apply
         */
        'applyCreditMemoPayment'   => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/credit_memos/{credit_memo_id}/credit_memo_payments/{credit_memo_payment_id}',
            'summary'          => 'Apply a pending credit memo payment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994255',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'credit_memo_id'         => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit memo.',
                    'required'    => true,
                ],
                'credit_memo_payment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit memo.',
                    'required'    => true,
                ],
                'check_number'           => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Adds a check number to a payment of type "check".',
                    'required'    => false,
                ],
            ],
        ],

        /**
         *    Credit Memo Payments / Cancel
         */
        'cancelCreditMemoPayment'  => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/credit_memos/{credit_memo_id}/credit_memo_payments/{credit_memo_payment_id}',
            'summary'          => 'Cancel a pending credit memo payment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994257',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'credit_memo_id'         => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit memo.',
                    'required'    => true,
                ],
                'credit_memo_payment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit memo.',
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
