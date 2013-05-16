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
 * @package     TicketEvolution\Db
 * @subpackage  Table
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


namespace TicketEvolution\Db\Table\Row;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Db
 * @subpackage  Table
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
abstract class AbstractRow extends \Zend_Db_Table_Row_Abstract
{
    /**
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * It is used here to ensure that we set the info for the created & modified
     * informational columns
     *
     * @return void
     */
    protected function _insert()
    {
        $this->_setModificationInfo();
        $this->_setCreatedInfo();
    }


    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * It is used here to ensure that we set the info for the modified
     * informational columns and do not mess with the created info column(s)
     *
     * @return void
     */
    protected function _update()
    {
        $this->_setModificationInfo();
        $this->_protectCreatedInfo();
    }


    /**
     * Allows pre-delete logic to be applied to row.
     * Subclasses may override this method.
     *
     * It is used here to ensure that we set the info for the modified
     * informational columns and do not mess with the created info column(s)
     * and to ensure that all we really do is set the status of this row to
     * inactive rather than actually deleting the row.
     *
     * @return void
     */
    protected function _delete()
    {
        $this->_setModificationInfo();
        $this->_protectCreatedInfo();

        // Get the "status" column and set it to 0 (false)
        $statusColumn = $this->_getTable()->getStatusColumn();
        $this->__set($statusColumn, 0);
    }


    /**
     * Ensures that created info is set
     *
     * @return void
     */
    protected function _setCreatedInfo()
    {
        // Make sure we don't try and set an empty createdDate
        // We don't need have to check if it exists because MySQL will add it
        // automatically but we will for consistency
        if (!isset($this->_data['createdDate'])
           || empty($this->_data['createdDate'])) {
            $this->__set('createdDate', date('c'));
        }
    }


    /**
     * Ensures that modification info is set
     *
     * @return void
     */
    protected function _setModificationInfo()
    {
        // Make sure we have a lastModifiedDate
        if (!isset($this->_data['lastModifiedDate'])
           || empty($this->_data['lastModifiedDate'])) {
            $this->__set('lastModifiedDate', date('c'));
        }
    }


    /**
     * Ensures that we do not change created information.
     *
     * @return void
     */
    protected function _protectCreatedInfo()
    {
        $this->__unset('createdDate');
    }

}
