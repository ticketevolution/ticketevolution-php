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
         *    Shipments / Index
         */
        'listShipments'              => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/shipments',
            'summary'              => 'Get a list of all shipments.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994290',
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
         *    Shipments / Show
         */
        'showShipment'               => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/shipments/{shipment_id}',
            'summary'          => 'Get a single shipment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994292',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'shipment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Shipment to return.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Shipments / Create
         */
        'createShipments'            => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/shipments',
            'summary'          => 'Create one or more Shipments.',
            'notes'            => 'Note that this takes an array of Shipments even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994308',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'shipments' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Shipments to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Shipments / Pend
         */
        'pendShipment'               => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/shipments/{shipment_id}/pend',
            'summary'          => 'Transition a shipment to the pending state.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994298',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'shipment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The shipment number.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Shipments / Deliver
         */
        'deliverShipment'            => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/shipments/{shipment_id}/deliver',
            'summary'          => 'Transition a shipment to the delivered state.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994296',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'shipment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The shipment number.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Shipments / Cancel
         */
        'cancelShipment'             => [
            'extends'          => null,
            'httpMethod'       => 'PUT',
            'uri'              => '/v9/shipments/{shipment_id}/cancel',
            'summary'          => 'Transition a shipment to the canceled state.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994324',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'shipment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The shipment number.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Shipments / Status
         */
        'listShipmentsStatus'        => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/shipments/status',
            'summary'          => 'Gives a query-able list of shipments for a brokerages.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751368',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'type'                      => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Type of the shipment.',
                    'required'    => false,
                    'enum'        => [
                        'FedEx',
                        'UPS',
                        'Eticket',
                        'InstantDelivery',
                        'Email',
                        'Offline',
                        'Courier',
                        'WillCall',
                        'PickupAtOffice',
                        'LocalPickup',
                        'Custom',
                        'Tbd',
                    ],
                ],
                'state'                     => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'State of the shipment.',
                    'required'    => false,
                    'enum'        => [
                        'pending',
                        'delivered',
                        'in_transit',
                    ],
                ],
                'order_group_id'            => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'The Order Group the shipment belongs to.',
                    'required'    => false,
                ],
                'order_link_id'             => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'The Order Link for the brokerage.',
                    'required'    => false,
                ],
                'tracking_number'           => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The tracking number for the shipment.',
                    'required'    => false,
                ],
                'ship_from_name'            => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The name of the shipment sender.',
                    'required'    => false,
                ],
                'ship_to_name'              => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The name of the shipment recipient.',
                    'required'    => false,
                ],
                'first_event_occurs_at.gte' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The ISO-8601 date for when the first event occurs.',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
                'first_event_occurs_at.lte' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The ISO-8601 date for when the first event occurs.',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
                'order_by'                  => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The column to order results by. Only columns that are filterable can also be order-able.',
                    'required'    => false,
                ],
                'order_direction'           => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The way results should be ordered.',
                    'required'    => false,
                    'enum'        => [
                        'asc',
                        'desc',
                    ],
                ],
            ],
        ],


        /**
         *    Shipments / Airbill
         */
        'generateAirbillForShipment' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/shipments/{shipment_id}/airbill',
            'summary'          => 'Generate an airbill for FedEx shipments.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994306',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'shipment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The shipment number.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Shipments / Email Airbill
         */
        'emailAirbillForShipment'    => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/shipments/{shipment_id}/email_airbill',
            'summary'          => 'Email an airbill that has been generated for a shipment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994300',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'shipment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The shipment number.',
                    'required'    => true,
                ],
                'recipients'  => [
                    'location'    => 'uri',
                    'type'        => 'array',
                    'description' => 'List of recipients that should receive the airbill.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Shipments / Download
         */
        'downloadAirbillForShipment' => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/shipments/{shipment_id}/get_airbill',
            'summary'          => 'Download an airbill that has been generated for a shipment.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994300',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'shipment_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The shipment number.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Shipments / Suggestion
         */
        'showShipmentSugestion'      => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/shipments/suggestion',
            'summary'          => 'Get the suggested shipping method for a ticket group.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=24674319',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id'    => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'ID of the ticket group.',
                    'required'    => true,
                ],
                'address_id'         => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'ID of an existing address.',
                    'required'    => false,
                ],
                'address_attributes' => [
                    'location'    => 'json',
                    'type'        => 'object',
                    'description' => 'Object of full address attributes.',
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
