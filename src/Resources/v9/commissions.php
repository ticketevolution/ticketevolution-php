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
         *    Commissions / Index
         */
        'listCommissions'         => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/commissions',
            'summary'              => 'Get a list of all commissions.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994334',
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
         *    Commissions / Show
         */
        'showCommission'          => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/commissions/{commission_id}',
            'summary'          => 'Get a single commission.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994338',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'commission_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Commission to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Commissions / Cancel
         */
        'cancelCommissions'       => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/commissions/{commission_id}/cancel',
            'summary'          => 'Cancel an active commission.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994342',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'commission_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Commission to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Commissions / Pay
         */
        'payCommissions'          => [
            'extends'              => null,
            'httpMethod'           => 'POST',
            'uri'                  => '/v9/commissions/pay',
            'summary'              => 'Create a payment for one or more commissions.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994344',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'transactions' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'An array of transactions to create.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => ['location' => 'json'],
        ],


        /**
         *    Commission Payments / Index
         */
        'listCommissionPayments'  => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/commissions/{commission_id}/commission_payments',
            'summary'          => 'List all payments on a commission.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994352',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'commission_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Commission.',
                    'required'    => true,
                ],
            ],
        ],

        /**
         *    Commission Payments / Show
         */
        'showCommissionPayment'   => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/commissions/{commission_id}/commission_payments/{commission_payment_id}',
            'summary'          => 'Display information about a commission payment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994354',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'commission_id'         => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Commission.',
                    'required'    => true,
                ],
                'commission_payment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Commission.',
                    'required'    => true,
                ],
            ],
        ],

        /**
         *    Commission Payments / Apply
         */
        'applyCommissionPayment'  => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/commissions/{commission_id}/commission_payments/{commission_payment_id}',
            'summary'          => 'Complete a commission payment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994357',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'commission_id'         => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Commission.',
                    'required'    => true,
                ],
                'commission_payment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Commission.',
                    'required'    => true,
                ],
            ],
        ],

        /**
         *    Commission Payments / Cancel
         */
        'cancelCommissionPayment' => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/commissions/{commission_id}/commission_payments/{commission_payment_id}',
            'summary'          => 'Cancel a commission payment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994361',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'commission_id'         => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Commission.',
                    'required'    => true,
                ],
                'commission_payment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Commission.',
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
