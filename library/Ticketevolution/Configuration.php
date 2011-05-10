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
 * @package     Ticketevolution
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt     New BSD License
 * @version     $Id: Configuration.php 28 2011-05-09 22:53:01Z jcobb $
 */


/**
 * @category    Ticketevolution
 * @package     Ticketevolution
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt     New BSD License
 */
class Ticketevolution_Configuration
{
    /**
     * Constructs a new Ticket Evolution Configuration
     *
     * @param  object $object
     * @throws Ticketevolution_Exception
     * @return Ticketevolution_Configuration
     */
    public function __construct($object)
    {
        foreach($object as $prop => $val) {
            // If the value is an ISO 8601 date string make it into a Ticketevolution_Date object
            if(is_string($val) && preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/i', $val) === 1) {
                /**
                 * @see Ticketevolution_Date
                 */
                require_once 'Ticketevolution/Date.php';
                $this->{$prop} = new Ticketevolution_Date($val, Ticketevolution_Date::ISO_8601);
            } else {
                $this->{$prop} = $val;
            }
        }
    }
}
