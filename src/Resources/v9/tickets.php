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
         *    Tickets / Index
         */
        'listTickets'         => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/tickets',
            'summary'          => 'Lists all tickets for a Ticket Group.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=28016763',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id' => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'ID of the Event for which you would like to list tickets.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Tickets / Show
         */
        'showTicket'          => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/tickets/{ticket_id}',
            'summary'          => 'Get a single Ticket.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=28475557',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Ticket to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Tickets / Set Properties
         */
        'setTicketProperties' => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/tickets/{ticket_id}',
            'summary'          => 'Add a PDF eticket and/or barcode to an individual ticket/seat.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=28016760',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Ticket to return.',
                    'required'    => true,
                ],
                'eticket'   => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'Base-64 encoded single page PDF.',
                    'required'    => false,
                ],
                'barcode'   => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'The ticket’s barcode.',
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
