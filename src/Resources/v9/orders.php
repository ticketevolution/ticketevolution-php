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
         *    Orders / Index
         */
        'listOrders'                   => [
            'extends'              => null,
            'httpMethod'           => 'GET',
            'uri'                  => '/v9/orders',
            'summary'              => 'Get a list of orders.',
            'notes'                => '',
            'documentationUrl'     => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751395',
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
                    'description' => 'The number of items for each page of results.',
                    'required'    => false,
                    'default'     => 100,
                ],
                'state'    => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'The state of the order.',
                    'required'    => false,
                    'enum'        => ['pending', 'accepted', 'rejected', 'completed', 'pending_substitution'],
                ],
                'type'     => [
                    'location'    => 'query',
                    'type'        => 'string',
                    'description' => 'Indicates whether the order is a sale (Order) or a purchase (PurchaseOrder).',
                    'required'    => false,
                    'enum'        => ['Order', 'PurchaseOrder'],
                ],
            ],
            'additionalParameters' => ['location' => 'query'],
        ],


        /**
         *    Orders / Show
         */
        'showOrder'                    => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/orders/{order_id}',
            'summary'          => 'Get a single order.',
            'notes'            => 'Ticket Evolution uses order numbers that are actually in the format "order_group"-"order_id". The "order_id" required is only right-side portion. e.g.: To show the order details for 199401-473339 use /v9/orders/473339 as your URL.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129639',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Order.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Create
         */
        'createOrders'                 => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders',
            'summary'          => 'Create one or more Orders.',
            'notes'            => 'Note that this takes an array of Orders even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994275',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'orders' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Orders to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Background
         */
        'createBackroundOrders'        => [
            'extends'          => 'createOrders',
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/background',
            'summary'          => 'Submit orders to be processed without waiting for response.',
            'notes'            => 'Note that this takes an array of Orders even if only creating one. Identical to Orders / Create, but the order will be queued for creation instead of processed immediately. Useful if creating orders with a large number of items. In most cases, orders should be processed within a couple of minutes.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994273',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'orders' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Orders to create.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Accept
         */
        'acceptOrder'                  => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/accept',
            'summary'          => 'Accept an order that belongs to you and is in the pending state.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470105',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id'    => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'reviewer_id' => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'ID of the User who is accepting this Order.',
                    'required'    => true,
                ],
                'seats'       => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'Array of the actual seat numbers you will be delivering.',
                    'required'    => false,
                ],
            ],
        ],


        /**
         *    Orders / Reject
         */
        'rejectOrder'                  => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/reject',
            'summary'          => 'Accept an order that belongs to you and is in the pending state.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470108',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id'    => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'reviewer_id' => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'ID of the User who is accepting this Order.',
                    'required'    => true,
                ],
                'reason'      => [
                    'location'        => 'json',
                    'type'            => 'string',
                    'description'     => 'The reason for which you are rejecting the order.',
                    'required'        => true,
                    'enum'            => [
                        'Tickets No Longer Available',
                        'Tickets Priced Incorrectly',
                        'Duplicate Order',
                        'Fraudulent Order',
                        'Test Order',
                        'Other'
                    ],
                    'rejection_notes' => [
                        'location'    => 'json',
                        'type'        => 'string',
                        'description' => 'Additional Notes about this rejection.',
                        'required'    => false,
                    ],
                ],
            ],
        ],


        /**
         *    Orders / Cancel
         */
        'cancelOrder'                  => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/cancel',
            'summary'          => 'Propose cancellation of an accepted broker-to-broker point-of-sale order.',
            'notes'            => 'Should be used to propose cancellation of an accepted broker-to-broker point-of-sale order. Both buyer and seller must cancel for the order to be completed.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129637',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id'    => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'reviewer_id' => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'ID of the User who is accepting this Order.',
                    'required'    => true,
                ],
                'reason'      => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The reason for which you are rejecting the order.',
                    'required'    => true,
                    'enum'        => [
                        'Tickets No Longer Available',
                        'Tickets Priced Incorrectly',
                        'Duplicate Order',
                        'Fraudulent Order',
                        'Test Order',
                        'Other'
                    ],
                ],
            ],
        ],


        /**
         *    Orders / Return
         */
        'returnOrder'                  => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/return',
            'summary'          => 'Create a return for an order.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470111',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id'     => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'amount'       => [
                    'location'    => 'json',
                    'type'        => 'number',
                    'description' => 'The amount you wish to return.',
                    'required'    => true,
                ],
                'cancel_order' => [
                    'location'    => 'json',
                    'type'        => 'boolean',
                    'description' => 'Transition the order state to canceled post-acceptance.',
                    'required'    => false,
                ],
                'reviewer_id'  => [
                    'location'    => 'json',
                    'type'        => 'integer',
                    'description' => 'ID of the User who is returning this Order. Required if cancel_order is true.',
                    'required'    => false,
                ],
                'reason'       => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The reason for the cancellation. Required if cancel_order is true.',
                    'required'    => false,
                    'enum'        => [
                        'Tickets No Longer Available',
                        'Tickets Priced Incorrectly',
                        'Duplicate Order',
                        'Fraudulent Order',
                        'Test Order',
                        'Other'
                    ],
                ],
                'notes'        => [
                    'location'    => 'json',
                    'type'        => 'string',
                    'description' => 'The reason for the cancellation.',
                    'required'    => false,
                ],
            ],
        ],


        /**
         *    Orders / Deliver Etickets
         */
        'uploadEticketsForAnOrder'     => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/deliver_etickets',
            'summary'          => 'Upload a PDF containing all of the eticket(s) to deliver to a buyer.',
            'notes'            => 'Use this action to upload etickets for an item in the order. The file must be encoded in Base64, as it will be decoded as such on the receiving end. The file must include all, and only all of the etickets for the item_id. If you wish to deliver the etickets as individual files for each ticket you need to use Items / Add Etickets',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470128',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'etickets' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'Array containing the eticket(s) file(s) and their item_id(s).',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Email
         */
        'emailOrderPDF'                => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/email',
            'summary'          => 'Email a PDF of the invoice or purchase order.',
            'notes'            => 'Generate and send a PDF of the order or purchase order as an attachment to a specified list of recipients.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994268',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id'   => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'recipients' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'Array of strings containing email address recipients.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Email Etickets Link
         */
        'emailEticketDownloadLink'     => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/email_etickets_link',
            'summary'          => 'Email a link to the etickets posted for this order.',
            'notes'            => 'Generate and send an email with a one-time use link to download etickets for an order to a specified list of recipients.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994270',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id'   => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'recipients' => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'Array of strings containing email address recipients.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Get Ticket Costs
         */
        'listTicketCostsForAnOrder'    => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/orders/{order_id}/email_etickets_link',
            'summary'          => 'Get the cost of each individual ticket within an order (consignment only).',
            'notes'            => 'For use with consignment orders, this action will display the state and cost value currently assigned to each individual ticket in every order item.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470191',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Pend to Seller
         */
        'pendOrderToSeller'            => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/pend_to_seller',
            'summary'          => 'Transition an open order to the pending state.',
            'notes'            => 'An order that is in the open state is invisible to the broker who is selling the tickets. Generally while the order is open, it is being reviewed for possible fraud on the buyer end before being passed on to the selling broker. Call this action to transition a purchase order that you own to the pending state, which will pass the order to the broker for acceptance.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994261',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id'         => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'payments'         => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'Any additional payments that should be applied to the Order.',
                    'required'    => false,
                ],
                'order_item_links' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'Change seat details about a specific item in the Order.',
                    'required'    => false,
                ],
            ],
        ],


        /**
         *    Orders / Print
         */
        'downloadOrderPDF'             => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/orders/{order_id}/print',
            'summary'          => 'Download a PDF of the order.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470113',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Print Etickets
         */
        'downloadEticketsForAnOrder'   => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/orders/{order_id}/print_etickets',
            'summary'          => 'Download the etickets that have been uploaded to this order.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470115',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'item_id'  => [
                    'location'    => 'query',
                    'type'        => 'integer',
                    'description' => 'Return only etickets for this order item ID.',
                    'required'    => false,
                ],
            ],
        ],


        /**
         *    Orders / Update Ticket Costs
         */
        'updateTicketCostsForOrder'    => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/update_ticket_costs',
            'summary'          => 'Create one or more Orders.',
            'notes'            => 'Note that this takes an array of Orders even if only creating one.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994275',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
                'tickets'  => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'Collection of tickets whose cost should be updated.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Items / Index
         */
        'listOrderItems'               => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/orders/{order_id}/items',
            'summary'          => 'List all items for a specific order.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=12550147',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'The ID of the Order.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Items / Show
         */
        'showOrderItem'                => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/orders/{order_id}/items/{item_id}',
            'summary'          => 'Get a single Order Item.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129639',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Order.',
                    'required'    => true,
                ],
                'item_id'  => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Item.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Items / Add Etickets
         */
        'uploadEticketsForOrderItem'   => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/items/{item_id}/add_etickets',
            'summary'          => 'Add Eticket files to Item.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=12550153',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Order.',
                    'required'    => true,
                ],
                'item_id'  => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Item.',
                    'required'    => true,
                ],
                'files'    => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'A collection of Base-64 encoded files.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Items / Remove Etickets
         */
        'removeEticketsForOrderItem'   => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/orders/{order_id}/items/{item_id}/remove_etickets',
            'summary'          => 'Remove Eticket files from an Item.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=12550157',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Order.',
                    'required'    => true,
                ],
                'item_id'  => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Item.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Items / Finalize Etickes
         */
        'finalizeEticketsForOrderItem' => [
            'extends'          => null,
            'httpMethod'       => 'POST',
            'uri'              => '/v9/orders/{order_id}/items/{item_id}/finalize_etickets',
            'summary'          => 'Finalize Eticket files for an Order Item.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=12550155',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id'        => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Order.',
                    'required'    => true,
                ],
                'item_id'         => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Item.',
                    'required'    => true,
                ],
                'keep_pages'      => [
                    'location'    => 'json',
                    'type'        => 'array',
                    'description' => 'Array of page numbers which will be kept in the final Eticket pack.',
                    'required'    => true,
                ],
                'ignore_quantity' => [
                    'location'    => 'json',
                    'type'        => 'boolean',
                    'description' => 'True if you would not like the API to verify that the number of final pages equal the quantity of the item.',
                    'required'    => false,
                ],
            ],
        ],


        /**
         *    Orders / Items / Print Etickets
         */
        'downloadEticketsForOrderItem' => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/orders/{order_id}/items/{item_id}/print_etickets',
            'summary'          => 'Download Eticket files for an Order Item.',
            'notes'            => '',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=12550151',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Order.',
                    'required'    => true,
                ],
                'item_id'  => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Item.',
                    'required'    => true,
                ],
            ],
        ],


        /**
         *    Orders / Items / Convert to Etickets
         */
        'convertOrderItemToEtickets'   => [
            'extends'          => null,
            'httpMethod'       => 'GET',
            'uri'              => '/v9/orders/{order_id}/items/{item_id}/convert_to_etickets',
            'summary'          => 'Convert an item to an eticket.',
            'notes'            => 'Convert an item that is not an eticket to an eticket. This will place the item in a new shipment and disassociate it with the old one. Note - if multiple items are in the same shipment, this will extract the single item from the delivery and place it in a new one, and the remaining items will remain in the original shipment.',
            'documentationUrl' => 'https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=12550159',
            'deprecated'       => false,
            'responseModel'    => 'defaultJsonResponse',
            'parameters'       => [
                'order_id' => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Order.',
                    'required'    => true,
                ],
                'item_id'  => [
                    'location'    => 'uri',
                    'type'        => 'integer',
                    'description' => 'ID of the Item.',
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
