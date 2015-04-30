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
         *    Reports / Sales
         */
        'showSalesReport'     => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/reports/sales',
            'summary'              => 'Generate a quick report of all sales.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994373',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'by_order_credential' => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Order results by credential.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'by_order_placer'     => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Order results by person who placed the order.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'buyer_id'            => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Filter results by buyer_id.',
                    'required'    => false,
                ],
                'order_placer_id'     => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Filter results by order_placer_id.',
                    'required'    => false,
                ],
                'order_promo_code'    => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Filter results by order_promo_code.',
                    'required'    => false,
                ],
                'order_credential_id' => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Filter results by order_credential_id.',
                    'required'    => false,
                ],
                'aggregates'          => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Provide count and sums of quantity, cost, price, and fee.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Reports / Inventory
         */
        'showInventoryReport' => [
            'extends' => 'massIndexTicketgroups',
            'uri'     => '/v9/reports/inventory',
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
