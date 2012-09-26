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
class TicketEvolution_DataLoader_Offices extends TicketEvolution_DataLoader_Abstract
{
    /**
     * Which endpoint we are hitting. This is used in the `dataLoaderStatus` table
     *
     * @var string
     */
    var $endpoint = 'offices';


    /**
     * The type of items to get [active|deleted]
     *
     * @var string
     */
    var $endpointState = 'active';


    /**
     * The class of the table
     *
     * @var Zend_Db_Table
     */
    protected $_tableClass = 'TicketEvolution_Db_Table_Offices';


    /**
     * Perform the API call
     *
     * @param array $options Options for the API call
     * @return TicketEvolution_Webservice_ResultSet
     */
    protected function _doApiCall(array $options)
    {
        try {
            return $this->_webService->listOffices($options);
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
            'officeId'          => (int)    $result->id,
            'brokerageId'       => (int)    $result->brokerage->id,
            'officeName'        => (string) $result->name,
            'phone'             => (string) $result->phone,
            'fax'               => (string) $result->fax,
            'timezone'          => (string) $result->time_zone,
            'isMain'            => (int)    $result->main,
            'officeUrl'         => (string) $result->url,
            'updated_at'        => (string) $result->updated_at,
            'officeStatus'      => (int)    1,
        );
        if (isset($result->address)) {
            $this->_data['streetAddress']   = (string) $result->address->street_address;
            $this->_data['extendedAddress'] = (string) $result->address->extended_address;
            $this->_data['locality']        = (string) $result->address->locality;
            $this->_data['regionCode']      = (string) $result->address->region;
            $this->_data['postalCode']      = (string) $result->address->postal_code;
            $this->_data['countryCode']     = (string) $result->address->country_code;
            $this->_data['latitude']        = (float)  $result->address->latitude;
            $this->_data['longitude']       = (float)  $result->address->longitude;
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
        // Save any email addresses to a separate table
        if (isset($result->email[0])) {
            // Create an object for the `tevoOfficeEmails` table
            $emailsTable = new TicketEvolution_Db_Table_OfficeEmails();

            // Initialize an array of emails
            $emailArray = array();

            // Loop through the emails and add them to the `tevoOfficeEmails` table
            foreach ($result->email as $email) {
                $data = array(
                    'officeId'          => (int)    $result->id,
                    'email'             => strtolower((string)$email),
                    'officeEmailStatus' => (int)    1,
                    'lastModifiedDate'  => (string) $this->_startTime->format('c'),
                );

                if ($row = $emailsTable->fetchRow($emailsTable->select()->where("`officeId` = ?", $data['officeId'])->where("`officeEmailStatus` = ?", (int) 0)->where("`email` = ?", $data['email']))) {
                    $row->setFromArray($data);
                } else {
                    $row = $emailsTable->createRow($data);
                }
                $row->save();

                unset($row);
                $emailArray[] = $data['email'];
                unset($data);
            } // End loop through emails for this office

            if ($this->_showProgress) {
                echo '<p>Saved ' . implode(', ', $emailArray) . ' to `tevoOfficeEmails` for this office</p>' . PHP_EOL;
            }

            // Now set `officeEmailStatus` = 0 for any `tevoOfficeEmails` entries for
            // this office that are not in $emailArray. This will set any emails that
            // were attached to this office but are no longer to false/inactive
            if (isset($emailArray)) {
                $data = array(
                    'officeEmailStatus' => (int)    0,
                    'lastModifiedDate'  => (string) $this->_startTime->format('c'),
                );
                $where = $emailsTable->getAdapter()->quoteInto("`officeId` = ?", $result->id);
                $where .= $emailsTable->getAdapter()->quoteInto(" AND `officeEmailStatus` = ?", (int) 1);
                $where .= $emailsTable->getAdapter()->quoteInto(" AND `email` NOT IN (?)", $emailArray);
                $emailsTable->update($data, $where);
                unset($data);
                unset($where);
                unset($emailArray);
            }
        }


        // Save any office hours to a separate table
        if (isset($result->hours[0])) {
            // Create an object for the `tevoOfficeHours` table
            $hoursTable = new TicketEvolution_Db_Table_OfficeHours();

            // Initialize an array of emails
            $hoursIdArray = array();

            // Loop through the emails and add them to the `tevoOfficeEmails` table
            foreach ($result->hours as $hour) {
                $openTime = new DateTime($hour->open);
                $closeTime = new DateTime($hour->close);

                $data = array(
                    'officeHoursId'     => (int)    $hour->id,
                    'officeId'          => (int)    $result->id,
                    'dayOfWeek'         => (int)    $hour->day_of_week,
                    'isClosed'          => (int)    $hour->closed,
                    'open'              => (string) $openTime->format(TicketEvolution_DateTime::MYSQL_TIME),
                    'close'             => (string) $closeTime->format(TicketEvolution_DateTime::MYSQL_TIME),
                    'officeHoursStatus' => (int)    1,
                    'lastModifiedDate'  => (string) $this->_startTime->format('c'),
                );

                if ($row = $hoursTable->fetchRow($hoursTable->select()->where("`officeHoursId` = ?", $data['officeHoursId']))) {
                    $row->setFromArray($data);
                } else {
                    $row = $hoursTable->createRow($data);
                }
                $row->save();

                unset($row);
                $hoursIdArray[] = $data['officeHoursId'];
                unset($data);
            } // End loop through hours for this office

            if ($this->_showProgress) {
                echo '<p>Saved ' . implode(', ', $hoursIdArray) . ' to `tevoOfficeHours` for this office</p>' . PHP_EOL;
            }

            // Now set `officeHoursStatus` = 0 for any `tevoOfficeHours` entries for
            // this office that are not in $hoursIdArray. This will set any hours that
            // were attached to this office but are no longer to false/inactive
            if (isset($hoursIdArray)) {
                $data = array(
                    'officeHoursStatus' => (int)    0,
                    'lastModifiedDate'  => (string) $this->_startTime->format('c'),
                );
                $where = $hoursTable->getAdapter()->quoteInto("`officeId` = ?", $result->id);
                $where .= $hoursTable->getAdapter()->quoteInto(" AND `officeHoursStatus` = ?", (int) 1);
                $where .= $hoursTable->getAdapter()->quoteInto(" AND `officeHoursId` NOT IN (?)", $hoursIdArray);
                $hoursTable->update($data, $where);
                unset($data);
                unset($where);
                unset($hoursIdArray);
            }
        }
    }


}
