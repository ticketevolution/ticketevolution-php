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
         *    Payments / Status
         */
        'listPayments'           => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/payments/status',
            'summary'              => 'Gives a query-able list of payments for a brokerages.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751401',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'page'              => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Which page of results to return.',
                    'required'    => false,
                    'default'     => 1,
                ],
                'per_page'          => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'The number of items for each page of results.',
                    'required'    => false,
                    'default'     => 100,
                ],
                'order_type'        => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Type of order.',
                    'required'    => false,
                    'enum'        => ['purchase_order', 'order', 'all'],
                ],
                'transaction_type'  => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Type of Transaction.',
                    'required'    => false,
                    'enum'        => ['EvopayTransaction', 'CreditCardTransaction'],
                ],
                'transaction_state' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Type of Transaction.',
                    'required'    => false,
                    'enum'        => ['completed', 'pending'],
                ],
                'seller_type'       => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Type of the seller.',
                    'required'    => false,
                    'enum'        => ['Office', 'Client'],
                ],
                'seller_name'       => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Name of the seller.',
                    'required'    => false,
                ],
                'buyer_name'        => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Name of the buyer.',
                    'required'    => false,
                ],
                'seller_id'         => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'ID of the seller.',
                    'required'    => false,
                ],
                'buyer_id'          => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'ID of the buyer.',
                    'required'    => false,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Payments / Index
         */
        'listPaymentsForAnOrder' => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/payments',
            'summary'          => 'Get a list of all payments.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470146',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'The order ID containing the payments to list.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Payments / Show
         */
        'showPayment'            => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/payments/{payment_id}',
            'summary'          => 'Get a single payment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470136',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'payment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Payment to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Payments / Create
         */
        'createPayments'         => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/payments',
            'summary'          => 'Create one or more Payments.',
            'notes'            => 'Note that this takes an array of Payments even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470142',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'payments' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Payments to create.',
                    'required'    => true
                ],
            ],
        ],


        /**
         *    Payments / Cancel
         */
        'cancelPayment'          => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/payments/{payment_id}/cancel',
            'summary'          => 'Cancel a pending payment on an order.',
            'notes'            => 'Cancel a payment that is in the pending state. Pending payments do not affect the order balance, but they do affect the pending balance, which can prevent additional payments from being posted.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470155',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'payment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Payment.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Payments / Refund
         */
        'refundPayment'          => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/payments/{payment_id}/refund',
            'summary'          => 'Create a refund of an existing payment.',
            'notes'            => 'This action can be called on an existing payment to create a new reverse (refunded) payment of the same type. In the case of credit_card payments, it may void the existing transaction if it has not yet settled.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470151',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'payment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Payment.',
                    'required'    => true,
                ],
                'amount'     => [
                    'location'    => 'json',
                    'type'        => 'number',
                    'description' => 'Amount of the refund (only necessary if different from amount of original payment).',
                    'required'    => false,
                ],
            ],
        ],


        /**
         *    Payments / Apply
         */
        'applyPayment'           => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/payments/{payment_id}/apply',
            'summary'          => 'Apply a pending payment to an order, making it complete.',
            'notes'            => 'By default, a payment is created in the pending state. This means it has been prepared, but is not yet applied to the order. The order’s pending balance will reflect that it exists, but it will not affect the order balance. Applying the payment will attempt to transition it to the completed state, after which the order balance will be adjusted.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470132',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'payment_id'   => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Payment.',
                    'required'    => true,
                ],
                'check_number' => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Adds check number while applying (only relevant for Check Transactions).',
                    'required'    => false,
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
