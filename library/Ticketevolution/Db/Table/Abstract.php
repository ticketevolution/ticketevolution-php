<?php
/**
 * Ticketevolution Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@teamonetickets.com so we can send you a copy immediately.
 *
 * @category    Ticketevolution
 * @package     Ticketevolution_Db
 * @subpackage  Table
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt     New BSD License
 * @version     $Id: Abstract.php 28 2011-05-09 22:53:01Z jcobb $
 */

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @category    Ticketevolution
 * @package     Ticketevolution_Db
 * @subpackage  Table
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt     New BSD License
 */
class Ticketevolution_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * Override the default insert() method to ensure certain data integrity
     */
    public function insert(array $data)
    {
        // Make sure we don't try and set an empty createdDate
        // We don't need to check if it exists because MySQL qill add it automatically
        if(empty($data['createdDate'])) {
            $data['createdDate'] = date('c');
        }
        // Make sure we have a lastModifiedDate
        if(!isset($data['lastModifiedDate']) || empty($data['lastModifiedDate'])) {
            $data['lastModifiedDate'] = date('c');
        }
        return parent::insert($data);
    }
 

    /**
     * Override the default update() method to ensure certain data integrity
     */
    public function update(array $data, $where)
    {
        // Make sure we don't mess with createdDate
        if(isset($data['createdDate'])) {
            unset($data['createdDate']);
        }
        // Make sure we have a lastModifiedDate
        if(!isset($data['lastModifiedDate']) || empty($data['lastModifiedDate'])) {
            $data['lastModifiedDate'] = date('c');
        }
        return parent::update($data, $where);
    }

}