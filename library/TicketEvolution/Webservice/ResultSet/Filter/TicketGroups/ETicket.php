<?php

/**
 * Ticket Evolution PHP Client Library
 *
 * LICENSE
 *
 * This source file is subject to the new BSD (3-Clause) License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://choosealicense.com/licenses/bsd-3-clause/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@ticketevolution.com so we can send you a copy immediately.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice\ResultSet
 * @subpackage  Filter
 * @copyright   Copyright (c) 2013 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */


namespace TicketEvolution\Webservice\ResultSet\Filter\TicketGroups;
use TicketEvolution\Webservice\ResultSet\Filter\AbstractFilter;
use Iterator;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice\ResultSet
 * @subpackage  Filter
 * @copyright   Copyright (c) 2013 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */
class ETicket extends AbstractFilter
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
    public function __construct(Iterator $iterator, $eTicket=true)
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
