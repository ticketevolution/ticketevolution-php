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


namespace TicketEvolution\Webservice\ResultSet\Filter\TicketGroups;
use TicketEvolution\Webservice\ResultSet\Filter\AbstractFilter;
use Iterator;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice\ResultSet
 * @subpackage  Filter
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class Wheelchair extends AbstractFilter
{
    /**
     * The value to match against the 'wheelchair' property
     *
     * @var bool
     */
    public $wheelchair;


    /**
     * @param Iterator $iterator
     * @param bool $wheelchair
     */
    public function __construct(Iterator $iterator, $wheelchair=true)
    {
        parent::__construct($iterator);

        $this->wheelchair = (bool) $wheelchair;
    }


    /**
     * Only return certain ticketGroups
     */
    public function accept()
    {
        if (parent::current()->wheelchair === $this->wheelchair) {
            return true;
        }

        return false;
    }
}
