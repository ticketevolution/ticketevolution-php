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
         *    Settings / Service Fees
         */
        'listServiceFeesSettings' => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/settings/service_fees',
            'summary'              => 'Service Fees Settings by Credential.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=22347806',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Settings / Shipping
         */
        'listShippingSettings'    => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/settings/shipping',
            'summary'              => 'Shipping Settings by Credential.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470044',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [],
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
