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
         *    Ticket Groups / Index
         */
        'listTicketGroups'       => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/ticket_groups',
            'summary'              => 'Lists all ticket groups for an event.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469962',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'event_id'          => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'ID of the Event for which you would like to list tickets.',
                    'required'    => true,
                ],
                'type'              => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by Ticket Group Type.',
                    'required'    => false,
                    'enum'        => [
                        'Event',
                        'Parking',
                    ]
                ],
                'quantity'          => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Filter by number of tickets greater than passed value.',
                    'required'    => false,
                ],
                'office_id'         => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Show only tickets owned by a specific office.',
                    'required'    => false,
                ],
                'exclude_office_id' => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Exclude tickets owned by a specific office.',
                    'required'    => false,
                ],
                'wheelchair'        => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Filter by wheelchair-accesssible tickets.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
//                'format' => [
//                    'location'    => 'query',
//                    'type'        => 'string',
//                    'description' => 'Display only the specified formats.',
//                    'required'    => false,
//                    'enum'        => [
//                        'Eticket',
//                        'Flash_seats',
//                        'Guest_list',
//                        'Paperless',
//                        'Physical',
//                    ]
//                ],
                'eticket'           => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Display only Etickets.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'instant_delivery'  => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Display only tickets available for “Instant Delivery”. Only etickets are available for Instant Delivery.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'lightweight'       => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Return a significantly smaller response with less details for greater speed.',
                    'required'    => false,
                    'default'     => true,
                    'format'      => 'boolean-string',
                ],
                'ticket_list'       => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Omits the seat-level detail from the response for greater speed.',
                    'required'    => false,
                    'default'     => false,
                    'format'      => 'boolean-string',
                ],
                'state'             => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by ticket state.',
                    'required'    => false,
                ],
                'order_by'          => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Sort results by any return parameter.',
                    'required'    => false,
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Ticket Groups / Show
         */
        'showTicketGroup'        => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}',
            'summary'          => 'Get a single Ticket Group.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469964',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the TicketGroup to return.',
                    'required'    => true,
                ],
                'ticket_list'     => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Whether or not to include a ticket_list array with details of the individual tickets in the group.',
                    'required'    => false,
                    'default'     => false,
                    'format'      => 'boolean-string',
                ],
            ],
        ],


        /**
         *    Ticket Groups / Mass Index
         */
        'massIndexTicketGroups'  => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/ticket_groups/mass_index',
            'summary'              => 'List ticket groups for inventory view.',
            'notes'                => 'Alternate index endpoint for listing Ticket Groups. The normal ticket group index will display tickets for one event only, whereas this one will list all inventory for your office across all events.',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994263',
            'deprecated'           => false,
            'responseModel'        => 'defaultJsonResponse',
            'parameters'           => [
                'performers_search'           => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Full-text search.',
                    'required'    => false,
                ],
                'event_id'                    => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'ID of the Event for which you would like to list tickets.',
                    'required'    => false,
                ],
                'venue_id'                    => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'ID of the Venue for which you would like to list tickets.',
                    'required'    => false,
                ],
                'venue_name'                  => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by venue names containing.',
                    'required'    => false,
                ],
                'venue_address_locality'      => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by Venue Locality (city) name contains.',
                    'required'    => false,
                ],
                'venue_address_region'        => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by Venue Region (state) name contains.',
                    'required'    => false,
                ],
                'ticket_group_section'        => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by Ticket Group Section.',
                    'required'    => false,
                ],
                'ticket_group_row'            => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by Ticket Group Row.',
                    'required'    => false,
                ],
                'ticket_group_in_hand'        => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Filter by Ticket Group In-Hand Status.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'ticket_group_type'           => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Filter by Ticket Group Type.',
                    'required'    => false,
                    'enum'        => [
                        'Event',
                        'Parking',
                    ]
                ],
                'ticket_group_consignment'    => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Filter by consignment tickets.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'event_occurs_at.gte'         => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Range of date values at which event occurs.',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
                'event_occurs_at.lte'         => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Range of date values at which event occurs.',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
                'event_occurs_local.gte'      => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Range of date values at which event occurs.',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
                'event_occurs_local.lte'      => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Range of date values at which event occurs.',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
                'ticket_group_in_hand_on.gte' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Range of date values at which event occurs.',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
                'ticket_group_in_hand_on.lte' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Range of date values at which event occurs.',
                    'required'    => false,
                    'format'      => 'date-time',
                ],
                'lightweight'                 => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Return a significantly smaller response with less details for greater speed.',
                    'required'    => false,
                    'default'     => true,
                    'format'      => 'boolean-string',
                ],
                'available_only'              => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Return only information about available tickets.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'show_unavailable'            => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'If true will include ticket groups that have no available tickets.',
                    'required'    => false,
                    'default'     => false,
                    'format'      => 'boolean-string',
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Ticket Groups / Export
         */
        'exportTicketGroups'     => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups/export',
            'summary'          => 'Export an Office’s Ticket Groups to a CSV or text file.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=10911748',
            'deprecated'       => false,
            'responseModel'    => null,
            'parameters'       => [
                'format'               => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The format you would like the file to be in. Either txt or csv.',
                    'required'    => true,
                    'default'     => 'csv',
                    'enum'        => ['csv', 'txt'],
                ],
                'include_spec'         => [
                    'location'    => 'query',
                    'type'        => 'boolean',
                    'description' => 'Indicates whether or not to include ticket groups marked as “spec”.',
                    'required'    => false,
                    'default'     => true,
                    'format'      => 'boolean-string',
                ],
                'marking_rule[method]' => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Rule to be applied to the price of all ticket groups in the file. If included you must define both a method and an amount.',
                    'required'    => false,
                ],
                'marking_rule[amount]' => [
                    'location'    => 'query',
                    'type'        => 'number',
                    'description' => 'Rule to be applied to the price of all ticket groups in the file. If included you must define both a method and an amount.',
                    'required'    => false,
                ],
            ],
        ],


        /**
         *    Ticket Groups / Create
         */
        'createTicketGroups'     => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups',
            'summary'          => 'Create one or more TicketGroups.',
            'notes'            => 'Note that this takes an array of TicketGroups even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469980',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_groups' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of TicketGroups to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Ticket Groups / Delete
         */
        'deleteTicketGroup'      => [
            'extends'          => null,
            'httpMethod'       => 'DELETE',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}',
            'summary'          => 'Delete a specified Ticket Group.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=28475429',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Ticket Group to delete.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Ticket Groups / Take
         */
        'takeTicketGroup'        => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}/take',
            'summary'          => 'Hold tickets for a prospective buyer indefinitely.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469976',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Ticket Group.',
                    'required'    => true,
                ],
                'quantity'        => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'Number of seats to hold.',
                    'required'    => true,
                ],
                'price'           => [
                    'location'    => 'json',
                    'type'        => 'number',
                    'description' => 'Price for which tickets will be held.',
                    'required'    => true,
                ],
                'low_seat'        => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'Lowest seat in the group you wish to hold.',
                    'required'    => true,
                ],
                'held_for_type'   => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'Type of entity for which to take the tickets.',
                    'required'    => true,
                    'enum'        => ['Office', 'Client'],
                ],
                'held_for_id'     => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'ID of Office or Client for which to take the tickets.',
                    'required'    => true,
                ],
                'notes'           => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'Notes regarding the take.',
                    'required'    => false,
                ],
                'ticket_list'     => [
                    'location'    => 'json',
                    'type'        => 'boolean',
                    'description' => 'Display individual tickets in the return.',
                    'required'    => false,
                    'default'     => false,
                    'format'      => 'boolean-string',
                ],
            ],
        ],


        /**
         *    Ticket Groups / Take
         */
        'releaseTicketGroupTake' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}/release_take/{ticket_group_take_id}',
            'summary'          => 'Releases a ticket in the taken state back to the available state.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469973',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id'      => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Ticket Group.',
                    'required'    => true,
                ],
                'ticket_group_take_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the take you wish to release.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Ticket Groups / Hold
         */
        'holdTicketGroup'        => [
            'extends'          => 'takeTicketGroup',
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}/hold',
            'summary'          => 'Hold tickets for a prospective buyer temporarily.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469967',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'hold_until' => [
                    'location'    => 'json',
                    'type'        => 'strong',
                    'description' => 'Date/Time at which tickets will revert to available status.',
                    'required'    => true,
                    'format'      => 'date-time',
                ],

            ],
        ],


        /**
         *    Ticket Groups / Update Hold
         */
        'updateTicketGroupHold'  => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}/hold/{ticket_group_hold_id}',
            'summary'          => 'Hold tickets for a prospective buyer temporarily.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469967',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id'      => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Ticket Group.',
                    'required'    => true,
                ],
                'ticket_group_hold_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Hold.',
                    'required'    => true,
                ],
                'hold_until'           => [
                    'location'    => 'json',
                    'type'        => 'strong',
                    'description' => 'Date/Time at which tickets will revert to available status.',
                    'required'    => true,
                    'format'      => 'date-time',
                ],

            ],
        ],


        /**
         *    Ticket Groups / Release Hold
         */
        'releaseTicketGroupHold' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}/hold/{ticket_group_hold_id}',
            'summary'          => 'Releases a ticket in the held state back to the available state.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469959',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id'      => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Ticket Group.',
                    'required'    => true,
                ],
                'ticket_group_hold_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Hold.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Ticket Groups / Waste
         */
        'wasteTicketGroup'       => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}/waste',
            'summary'          => 'Permanently changes the state of tickets to wasted.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469978',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Ticket Group.',
                    'required'    => true,
                ],
                'wasted_reason'   => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'Reason why the tickets were wasted.',
                    'required'    => true,
                    'enum'        => [
                        'Did not sell',
                        'Lost by shipper',
                        'Lost before shipment',
                    ],
                ],
            ],
        ],


        /**
         *    Ticket Groups / Update
         */
        'updateTicketGroup'      => [
            'extends'          => null,
            'httpMethod'       => 'PUT',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}',
            'summary'          => 'Used to update ticket groups in the system.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=3014931',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id'       => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Ticket Group.',
                    'required'    => true,
                ],
                'price'                 => [
                    'location'    => 'json',
                    'type'        => 'number',
                    'description' => 'The new price for the ticket.',
                    'required'    => false,
                ],
                'retail_price_override' => [
                    'location'    => 'json',
                    'type'        => 'number',
                    'description' => 'The new retail price for the ticket.',
                    'required'    => false,
                ],
                'type'                  => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The type of ticket.',
                    'required'    => false,
                    'enum'        => ['event', 'parking'],
                ],
                'section'               => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The section of ticket.',
                    'required'    => false,
                ],
                'row'                   => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The row of ticket.',
                    'required'    => false,
                ],
                'view_type'             => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The view type of ticket.',
                    'required'    => false,
                    'enum'        => ['Full', 'Obstructed', 'Partially Obstructed'],
                ],
                'tag_list'              => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'Space-delimited tags for the ticket group.',
                    'required'    => false,
                ],
                'in_hand'               => [
                    'location'    => 'json',
                    'type'        => 'boolean',
                    'description' => 'In hand or not.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'in_hand_on'            => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'A date for when the Ticket Group will be available to ship/deliver.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'wheelchair'            => [
                    'location'    => 'json',
                    'type'        => 'boolean',
                    'description' => 'Wheelchair or not.',
                    'required'    => false,
                    'format'      => 'boolean-string',
                ],
                'internal_notes'        => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'Notes that are only visibile to Users of the owning brokerage.',
                    'required'    => false,
                ],
                'broker_notes'          => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'Notes that are visible to all brokers but not customers.',
                    'required'    => false,
                ],
                'external_notes'        => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'Notes that are visible to all brokers and customers.',
                    'required'    => false,
                ],
            ],
        ],


        /**
         *    Ticket Groups / Mass Update
         */
        'massUpdateTicketGroups' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups/mass_update',
            'summary'          => 'Change attributes on a large number of ticket groups simultaneously.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994266',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ids'   => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'Array of Ticket Group IDs that you would like to update.',
                    'required'    => true,
                ],
                'attrs' => [
                    'location'    => 'json',
                    'type'        => 'number',
                    'description' => 'Object with the attributes you would like to update. Support values are:
                                        type:String
                                        format:String
                                        view_type:String
                                        section:String
                                        row:String
                                        price:Numeric(8,2)
                                        retail_price_override:Numeric(8,2)
                                        wheelchair:Boolean
                                        split_override:String
                                        in_hand:Boolean
                                        in_hand_on:Timestamp
                                        external_notes:Text
                                        internal_notes:Text
                                        broker_notes:Text
                                        eticket:Boolean',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Ticket Groups / Broadcast
         */
        'broadcastTicketGroup'   => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/ticket_groups/{ticket_group_id}/broadcast',
            'summary'          => 'Change the broadcast status of a ticket group.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469971',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'ticket_group_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the specific Ticket Group.',
                    'required'    => true,
                ],
                'broadcast'       => [
                    'location'    => 'json',
                    'type'        => 'boolean',
                    'description' => 'New state for the broadcast parameter.',
                    'required'    => true,
                    'format'      => 'boolean-string',
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
