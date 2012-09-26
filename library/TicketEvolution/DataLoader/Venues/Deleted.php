<?php

/**
 * TicketEvolution Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@teamonetickets.com so we can send you a copy immediately.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution_DataLoader
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2012 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


/**
 * Extends Zend_Date with some handy constants and also allows for easy handling
 * of "TBA" event times.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution_DataLoader
 * @copyright   Copyright (c) 2012 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_DataLoader_Venues_Deleted extends TicketEvolution_DataLoader_Abstract
{
    /**
     * Which endpoint we are hitting. This is used in the `dataLoaderStatus` table
     *
     * @var string
     */
    var $endpoint = 'venues';


    /**
     * The type of items to get [active|deleted]
     *
     * @var string
     */
    var $endpointState = 'deleted';


    /**
     * The class of the table
     *
     * @var Zend_Db_Table
     */
    protected $_tableClass = 'TicketEvolution_Db_Table_Venues';


    /**
     * Perform the API call
     *
     * @param array $options Options for the API call
     * @return TicketEvolution_Webservice_ResultSet
     */
    protected function _doApiCall(array $options)
    {
        try {
            return $this->_webService->listVenuesDeleted($options);
        } catch(Exceotion $e) {
            throw new TicketEvolution_DataLoader_Exception($e);
        }
    }


    /**
     * Manipulates the $result data into an array to be passed to the table row
     *
     * @param object $result    The current result item
     * @return void
     */
    protected function _formatData($result)
    {
        $this->_data = array(
            'venueId'                   => (int)$result->id,
            'venueName'                 => (string) $result->name,

            'streetAddress'             => (string) $result->address->street_address,
            'extendedAddress'           => (string) $result->address->extended_address,
            'locality'                  => (string) $result->address->locality,
            'region'                    => (string) $result->address->region,
            'postalCode'                => (string) $result->address->postal_code,

            'venueUrl'                  => (string) $result->url,
            'venueKeywords'             => (string) $result->keywords,
            'updated_at'                => (string) $result->updated_at,

            'venueStatus'               => (int)    0,
        );

        if (!empty($result->created_at)) {
            $this->_data['created_at'] = (string) $result->created_at;
        }

        if (!empty($result->deleted_at)) {
            $this->_data['deleted_at'] = (string) $result->deleted_at;
        }


        /**
         * Currently country_code is not part of the address object. Not sure why.
         * Handle it properly wherever it is
         */
        if (isset($result->country_code)) {
            $this->_data['countryCode'] = $result->country_code;
        }
        if (!empty($result->address->country_code)) {
            $this->_data['countryCode'] = $result->address->country_code;
        }

        if (isset($result->address->latitude)) {
            $this->_data['latitude'] = $result->address->latitude;
        }
        if (isset($result->address->longitude)) {
            $this->_data['longitude'] = $result->address->longitude;
        }

        if (!empty($result->upcoming_events->first)) {
            // Ensure the timezone is not incorrectly adjusted
            $firstEvent = preg_replace('/[Z]/i', '', $result->upcoming_events->first);
            $this->_data['upcomingEventFirst'] = (string) $firstEvent;
        }
        if (!empty($result->upcoming_events->last)) {
            // Ensure the timezone is not incorrectly adjusted
            $lastEvent = preg_replace('/[Z]/i', '', $result->upcoming_events->last);
            $this->_data['upcomingEventLast'] = (string) $lastEvent;
        }
    }


    /**
     * Allows pre-save logic to be applied.
     * Subclasses may override this method.
     *
     * @param object $result    The current result item. Only passed to enable progress output
     * @return void
     */
    protected function _preSave($result)
    {
    }


    /**
     * Allows post-save logic to be applied.
     * Subclasses may override this method.
     *
     * @param object $result    The current result item
     * @return void
     */
    protected function _postSave($result)
    {
    }


}
