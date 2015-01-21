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
         *    Clients / Index
         */
        'listClients'                => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/clients',
            'summary'              => 'Get a list of all clients.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470168',
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
         *    Clients / Show
         */
        'showClient'                 => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/clients/{client_id}',
            'summary'          => 'Get a single client.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470174',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Create
         */
        'createClients'              => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/clients',
            'summary'          => 'Create one or more clients.',
            'notes'            => 'Note that this takes an array of clients even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470183',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'clients' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of clients to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Update
         */
        'updateClient'               => [
            'extends'              => null,
            'httpMethod'           => 'PUT',
            'uri'                  => '/v9/clients/{client_id}',
            'summary'              => 'Update a client.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470186',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'client_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The client number.',
                    'required'    => true,
                ],
                'name'      => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The client’s full name.',
                    'required'    => false,
                    'pattern'     => '/[^ ]+ [^ ]+/',
                ],
            ],
            'additionalParameters' => ['location' => 'json'],
        ],


        /**
         *    Clients / Email Addresses / Index
         */
        'listClientEmailAddresses'   => [
            'extends'              => 'listClients',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/clients/{client_id}/email_addresses',
            'summary'              => 'List Email Addresses for a specified Client.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394119',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'client_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client to return.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Clients / Email Addresses / Show
         */
        'showClientEmailAddress'     => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/clients/{client_id}/email_addresses/{email_address_id}',
            'summary'          => 'Get a single client Email Address.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394122',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id'        => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client to return.',
                    'required'    => true,
                ],
                'email_address_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Email Address to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Email Addresses / Create
         */
        'createClientEmailAddresses' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/clients/{client_id}/email_addresses',
            'summary'          => 'Create one or more Email Addresses for an existing client.',
            'notes'            => 'Note that this takes an array of Email Addresses even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=983146',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id'       => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client to return.',
                    'required'    => true,
                ],
                'email_addresses' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Email Addresses to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Email Addresses / Update
         */
        'updateClientEmailAddress'   => [
            'extends'          => null,
            'httpMethod'       => 'PUT',
            'uri'              => '/v9/clients/{client_id}/email_addresses/{email_address_id}',
            'summary'          => 'Update a client Email Address.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394200',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id'        => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client.',
                    'required'    => true,
                ],
                'email_address_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Email Address.',
                    'required'    => true,
                ],
                'label'            => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'A arbitrary label to associate with this address such as: home or work.',
                    'required'    => false,
                    'maxLength'   => 20,
                ],
                'address'          => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The client’s Email Address.',
                    'required'    => false,
                ],
            ],
        ],


        /**
         *    Clients / Addresses / Index
         */
        'listClientAddresses'        => [
            'extends'              => 'listClients',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/clients/{client_id}/addresses',
            'summary'              => 'List  Addresses for a specified Client.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129319',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'client_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client to return.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Clients / Addresses / Show
         */
        'showClientAddress'          => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/clients/{client_id}/addresses/{address_id}',
            'summary'          => 'Get a single client  Address.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129333',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id'  => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client.',
                    'required'    => true,
                ],
                'address_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Address.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Addresses / Create
         */
        'createClientAddresses'      => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/clients/{client_id}/addresses',
            'summary'          => 'Create one or more Addresses for an existing client.',
            'notes'            => 'Note that this takes an array of Addresses even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129337',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client to return.',
                    'required'    => true,
                ],
                'addresses' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of  Addresses to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Addresses / Update
         */
        'updateClientAddress'        => [
            'extends'              => null,
            'httpMethod'           => 'PUT',
            'uri'                  => '/v9/clients/{client_id}/addresses/{address_id}',
            'summary'              => 'Update a client Address.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129342',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'client_id'  => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client.',
                    'required'    => true,
                ],
                'address_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Address.',
                    'required'    => true,
                ],
                'label'      => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'A arbitrary label to associate with this address such as: home or work.',
                    'required'    => false,
                    'maxLength'   => 20,
                ],
            ],
            'additionalParameters' => ['location' => 'json'],
        ],


        /**
         *    Clients / Phone Numbers / Index
         */
        'listClientPhoneNumbers'     => [
            'extends'              => 'listClients',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/clients/{client_id}/phone_numbers',
            'summary'              => 'List Phone numbers for a specified Client.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394131',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'client_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Clients / Phone Numbers / Show
         */
        'showClientPhoneNumber'      => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/clients/{client_id}/phone_numbers/{phone_number_id}',
            'summary'          => 'Get a single client  Address.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394167',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id'       => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client.',
                    'required'    => true,
                ],
                'phone_number_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Phone Number.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Phone Numbers / Create
         */
        'createClientPhoneNumbers'   => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/clients/{client_id}/phone_numbers',
            'summary'          => 'Create one or more Phone Numbers for an existing client.',
            'notes'            => 'Note that this takes an array of Phone Numbers even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=983142',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id'     => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client to return.',
                    'required'    => true,
                ],
                'phone_numbers' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Phone Numbers to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Phone Numbers / Update
         */
        'updateClientPhoneNumber'    => [
            'extends'              => null,
            'httpMethod'           => 'PUT',
            'uri'                  => '/v9/clients/{client_id}/phone_numbers/{phone_number_id}',
            'summary'              => 'Update a client Address.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394170',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'client_id'       => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client.',
                    'required'    => true,
                ],
                'phone_number_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Phone Number.',
                    'required'    => true,
                ],
                'label'           => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'A arbitrary label to associate with this address such as: home, work or cell.',
                    'required'    => false,
                    'maxLength'   => 20,
                ],
            ],
            'additionalParameters' => ['location' => 'json'],
        ],


        /**
         *    Clients / Credit Cards / Index
         */
        'listClientCreditCards'      => [
            'extends'              => 'listClients',
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/clients/{client_id}/credit_cards',
            'summary'              => 'List Credit Card for a specified Client.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129301',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'client_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client.',
                    'required'    => true,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Clients / Credit Cards / Show
         */
        'showClientCreditCard'       => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/clients/{client_id}/credit_cards/{credit_card_id}',
            'summary'          => 'Get a single client Credit Card.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394167',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id'      => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client.',
                    'required'    => true,
                ],
                'credit_card_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit Card.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Credit Cards / Create
         */
        'createClientCreditCards'    => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/clients/{client_id}/credit_cards',
            'summary'          => 'Create one or more Credit Cards for an existing client.',
            'notes'            => 'Note that this takes an array of Credit Cards even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129312',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'client_id'    => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client to return.',
                    'required'    => true,
                ],
                'credit_cards' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Credit Cards to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Clients / Credit Cards / Update
         */
        'updateClientCreditCard'     => [
            'extends'              => null,
            'httpMethod'           => 'PUT',
            'uri'                  => '/v9/clients/{client_id}/credit_cards/{credit_card_id}',
            'summary'              => 'Update a client Credit Card.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394225',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'client_id'      => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Client.',
                    'required'    => true,
                ],
                'credit_card_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Credit Card.',
                    'required'    => true,
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
