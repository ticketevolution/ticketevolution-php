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
         *    Rate Options / Index
         */
        'listRateOptions' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/rate_options',
            'summary'          => 'Retrieve shipping rates via the FedEx API.',
            'notes'            => 'Note that this takes an array of Rate Options even if only requesting one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=34832455',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'rate_options' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Rate Options to retrieve.',
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
