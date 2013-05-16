<?php

/**
 * Ticket Evolution PHP Library for use with Zend Framework
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
 * @package     TicketEvolution\DataLoader
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


namespace TicketEvolution\DataLoader;


/**
 * DataLoader for a specific API endpoint to cache the data into local table(s)
 *
 * @category    TicketEvolution
 * @package     TicketEvolution\DataLoader
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class Configurations extends AbstractDataLoader
{
    /**
     * Which endpoint we are hitting. This is used in the `dataLoaderStatus` table
     *
     * @var string
     */
    var $endpoint = 'configurations';


    /**
     * The type of items to get [active|deleted]
     *
     * @var string
     */
    var $endpointState = 'active';


    /**
     * The class of the table
     *
     * @var \Zend_Db_Table
     */
    protected $_tableClass = '\TicketEvolution\Db\Table\Configurations';


    /**
     * Perform the API call
     *
     * @param array $options Options for the API call
     * @return \TicketEvolution\Webservice\ResultSet
     */
    protected function _doApiCall(array $options)
    {
        try {
            return $this->_webService->listConfigurations($options);
        } catch(Exceotion $e) {
            throw new namespace\Exception($e);
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
            'configurationId'           => (int)    $result->id,
            'configurationName'         => (string) $result->name,
            'isPrimary'                 => (int)    $result->primary,
            'ticketUtilsId'             => (string) $result->ticket_utils_id,
            'fanvenuesKey'              => (string) $result->fanvenues_key,
            'capacity'                  =>          $result->capacity,
            'isGeneralAdmission'        => (int)    $result->general_admission,
            'configurationUrl'          => (string) $result->url,
            'updated_at'                => (string) $result->updated_at,
            'configurationStatus'       => (int)    1,
        );

        if (!empty($result->created_at)) {
            $this->_data['created_at'] = (string) $result->created_at;
        }

        if (!empty($result->deleted_at)) {
            $this->_data['deleted_at'] = (string) $result->deleted_at;
        }

        if (!empty($result->venue->id)) {
            $this->_data['venueId'] = (int) $result->venue->id;
        }

        if (!empty($result->seating_chart->medium)) {
            $this->_data['urlSeatingChartMedium'] = (string) $result->seating_chart->medium;
        }
        if (!empty($result->seating_chart->large)) {
            $this->_data['urlSeatingChartLarge'] = (string) $result->seating_chart->large;
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
