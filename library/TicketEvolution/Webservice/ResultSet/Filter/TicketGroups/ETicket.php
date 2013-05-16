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
 * @package     TicketEvolution\Webservice\ResultSet
 * @subpackage  Filter
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


namespace TicketEvolution\Webservice\ResultSet\Filter;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice\ResultSet
 * @subpackage  Filter
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketGroups\ETicket extends AbstractFilter
{
    /**
     * The value to match against the 'eticket' property
     *
     * @var bool
     */
    public $eTicket;


    /**
     * @param Iterator $iterator
     * @param bool $inHand
     */
    public function __construct($iterator, $eTicket=true)
    {
        parent::__construct($iterator);

        $this->eTicket = (bool) $eTicket;
    }


    /**
     * Only return certain ticketGroups
     */
    public function accept()
    {
        if (parent::current()->eticket === $this->eTicket) {
            return true;
        }

        return false;
    }
}
