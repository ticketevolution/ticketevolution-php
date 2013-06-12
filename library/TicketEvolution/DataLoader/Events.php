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
class Events extends AbstractDataLoader
{
    /**
     * Which endpoint we are hitting. This is used in the `dataLoaderStatus` table
     *
     * @var string
     */
    var $endpoint = 'events';


    /**
     * The type of items to get [active|deleted]
     *
     * @var string
     */
    var $endpointState = 'active';


    /**
     * The \TicketEvolution\Webservice method to use for the API request
     *
     * @var string
     */
    protected $_webServiceMethod = 'listEvents';


    /**
     * The class of the table
     *
     * @var \Zend_Db_Table
     */
    protected $_tableClass = '\TicketEvolution\Db\Table\Events';


    /**
     * Manipulates the $result data into an array to be passed to the table row
     *
     * @param object $result    The current result item
     * @return void
     */
    protected function _formatData($result)
    {
        // Ensure the timezone is not incorrectly adjusted
        $occursAt = preg_replace('/[Z]/i', '', $result->occurs_at);

        $this->_data = array(
            'eventId'           => (int)    $result->id,
            'eventName'         => (string) $result->name,
            'eventDate'         => (string) $occursAt,
            'venueId'           => (int)    $result->venue->id,
            'categoryId'        => (int)    $result->category->id,
            'eventNotes'        => (string) $result->notes,
            'productsCount'     => (int)    $result->products_count,
            'eventUrl'          => (string) $result->url,
            'popularityScore'   => (float)  $result->popularity_score,
            'updated_at'        => (string) $result->updated_at,
            'deleted_at'        =>          null,
            'eventsStatus'       => (int)    1,
            'eventState'        => (string) $result->state,
        );

        if (!empty($result->created_at)) {
            $this->_data['created_at'] = (string) $result->created_at;
        }

        if (!empty($result->deleted_at)) {
            $this->_data['deleted_at'] = (string) $result->deleted_at;
        }

        if (isset($result->configuration->id)) {
            $this->_data['configurationId'] = (int) $result->configuration->id;
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
     * Saves the performances to the eventPerformers table
     *
     * @param object $result    The current result item
     * @return void
     */
    protected function _postSave($result)
    {
        // Create an object for the `tevoEventPerformers` table too
        $epTable = new \TicketEvolution\Db\Table\EventPerformers();

        // Set arrays of performers we can append performerIds to
        $performerListPrimary = array();
        $performerListSecondary = array();

        // Loop through the performers and add them to the `tevoEventPerformers` table
        foreach ($result->performances as $performance) {
            if (isset($performance->performer->id)) {
                $data = array(
                    'eventId'               => (int)    $result->id,
                    'performerId'           => (int)    $performance->performer->id,
                    'isPrimary'             => (int)    $performance->primary,
                    'eventPerformersStatus' => (int)    1,
                    'lastModifiedDate'      => (string) $this->_startTime->format('c'),
                );

                try {
                    if ($row = $epTable->find($data['eventId'], $data['performerId'])->current()) {
                        $row->setFromArray($data);
                    } else {
                        $row = $epTable->createRow($data);
                    }

                    $row->save();
                    unset($row);

                    if ($data['isPrimary']) {
                        $performerListPrimary[] = $data['performerId'];
                    } else {
                        $performerListSecondary[] = $data['performerId'];
                    }
                    unset($data);
                } catch (Exception $e) {
                    if ($this->_showProgress && !empty($data['eventId']) && !empty($data['performerId'])) {
                        echo '<h1 class="error">'
                           . 'Error attempting to save performerId ' . $data['performerId']
                           . ' for eventId ' . $data['eventId']
                           . '</h1>' . PHP_EOL;
                    }

                    throw new namespace\Exception($e);
                }
            }
        } // End loop through performers for this event

        if ($this->_showProgress) {
            echo '<p>Saved ';
            if (!empty($performerListPrimary)) {
                echo '<b>' . implode(', ', $performerListPrimary) . '</b>';

                if (!empty($performerListSecondary)) {
                    echo ', ';
                }
            }
            if (!empty($performerListSecondary)) {
                echo implode(', ', $performerListSecondary);
            }
            echo ' to `tevoEventPerformers` for this event</p>' . PHP_EOL;
        }

        $performerIdArray = array_merge($performerListPrimary, $performerListSecondary);
        unset($performerListPrimary);
        unset($performerListSecondary);

        // Now set to inactive any `tevoEventPerformers` entries for any performers not in
        // $performerIdArray. This will remove any performers that were attached
        // to this event but are no longer
        if (!empty($performerIdArray)) {
            $where = $epTable->getAdapter()->quoteInto("`eventId` = ?", $result->id);
            $where .= $epTable->getAdapter()->quoteInto(" AND `performerId` NOT IN (?)", $performerIdArray);
            $epTable->delete($where);
            unset($performerIdArray);
        }
    }


}
