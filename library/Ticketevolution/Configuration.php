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
 * @version     $Id: Configuration.php 74 2011-06-22 22:23:34Z jcobb $
 */


/**
 * @category    Ticketevolution
 * @package     Ticketevolution
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
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
            switch($prop) {
                case 'updated_at':
                    // This property is a date, convert it into a Ticketevolution_Date object
                    /**
                     * @see Ticketevolution_Date
                     */
                    require_once 'Ticketevolution/Date.php';
                    
                    $this->{$prop} = new Ticketevolution_Date($val, Ticketevolution_Date::ISO_8601);
                    break;
                    
                default:
                    $this->{$prop} = $val;
            }
        }
    }
}
