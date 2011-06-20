<?php
/**
 * Ticketevolution Framework
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
 * @category    Ticketevolution
 * @package     Ticketevolution
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 * @version     $Id: Venue.php 70 2011-06-14 22:13:59Z jcobb $
 */


/**
 * @see Ticketevolution_Date
 */
require_once 'Ticketevolution/Date.php';


/**
 * @category    Ticketevolution
 * @package     Ticketevolution
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class Ticketevolution_Venue
{
    /**
     * Constructs a new Ticket Evolution Venue
     *
     * @param  object $object
     * @throws Ticketevolution_Exception
     * @return Ticketevolution_Venue
     */
    public function __construct($object)
    {
        foreach($object as $prop => $val) {
            // If the value is an ISO 8601 date string make it into a Ticketevolution_Date object
            if(is_string($val) && preg_match('/\d{4}-\d{2}-\d{2}([T ]\d{2}:\d{2}:\d{2})?/i', $val) === 1) {
                $this->{$prop} = new Ticketevolution_Date($val, Ticketevolution_Date::ISO_8601);
            } else {
                $this->{$prop} = $val;
            }
        }
        
        // Loop above only catches dates at the root level. Set these too.
        if(!empty($object->upcoming_events->first)) {
            $this->upcoming_events->first = new Ticketevolution_Date($object->upcoming_events->first, Ticketevolution_Date::ISO_8601);
        }
        if(!empty($object->upcoming_events->last)) {
            $this->upcoming_events->last = new Ticketevolution_Date($object->upcoming_events->last, Ticketevolution_Date::ISO_8601);
        }
    }
}
