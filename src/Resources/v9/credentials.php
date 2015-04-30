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
         *    Credentials / Index
         */
        'listCredentials' => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/credentials',
            'summary'              => 'Get a list of all credentials.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994366',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Credentials / Show
         */
//        'showCredential'    => [
//            'extends'          => null,
//            'httpMethod'       => 'GET',
//            'uri'              => '/v9/credentials/{credential_id}',
//            'summary'          => 'Get a single credential.',
//            'notes'            => '',
//            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=25002003',
//            'deprecated'       => false,
//            'responseModel'    => 'defaultJsonResponse',
//            'parameters'       => [
//                'credential_id' => [
//                    'location'    => 'uri',
//                    'type'        => 'integer',
//                    'description' => 'ID of the Credential.',
//                    'required'    => true,
//                ],
//            ],
//        ],


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
