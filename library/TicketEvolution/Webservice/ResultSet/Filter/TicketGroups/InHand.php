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
 * @copyright   Copyright (c) 2014 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */


namespace TicketEvolution\Webservice\ResultSet\Filter\TicketGroups;
use TicketEvolution\Webservice\ResultSet\Filter\AbstractFilter;
use Iterator;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice\ResultSet
 * @subpackage  Filter
 * @copyright   Copyright (c) 2014 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */
class InHand extends AbstractFilter
{
    /**
     * The value to match against the 'in_hand' property
     *
     * @var bool
     */
    public $inHand;


    /**
     * @param Iterator $iterator
     * @param bool $inHand
     */
    public function __construct(Iterator $iterator, $inHand=true)
    {
        parent::__construct($iterator);

        $this->inHand = (bool) $inHand;
    }


    /**
     * Only return certain ticketGroups
     */
    public function accept()
    {
        if (parent::current()->in_hand === $this->inHand) {
            return true;
        }

        return false;
    }
}
