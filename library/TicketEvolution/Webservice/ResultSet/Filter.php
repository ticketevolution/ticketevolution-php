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
 * @package     TicketEvolution_Webservice
 * @subpackage  Webservice
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


/**
 * @category    TicketEvolution
 * @package     TicketEvolution_Webservice
 * @subpackage  Webservice
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_Webservice_ResultSet_Filter
    extends FilterIterator
{
    /**
     * Ticket Evolution API Token
     *
     * @var string
     * @link http://exchange.ticketevolution.com/brokerage/credentials
     */
    public $filters = array();


    /**
     * @param  TicketEvolution_Webservice_ResultSet $resultSet
     * @return void
     */
    public function __construct($resultSet, $filter)
    {
        parent::__construct($resultSet);

        $this->filter = $filter;
    }


    /**
     * Number of results returned in this ResultSet
     */
    public function accept()
    {
        if ($this->filter == 1) {
            if (parent::current()->section > 199) {
                return true;
            } else {
                return false;
            }
        }
        if ($this->filter == 2) {
            if (parent::current()->section < 300) {
                return true;
            } else {
                return false;
            }
        }
    }
}
