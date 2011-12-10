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
 * @package     TicketEvolution
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


/**
 * @category    TicketEvolution
 * @package     TicketEvolution
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_TicketGroup
{
    /**
     * Constructs a new Ticket Evolution TicketGroup
     *
     * @param  object $object
     * @throws TicketEvolution_Exception
     * @return TicketEvolution_TicketGroup
     */
    public function __construct($object)
    {
        foreach ($object as $prop => $val) {
            switch($prop) {
                case 'updated_at':
                case 'in_hand_on':
                    // This property is a date, convert it into a TicketEvolution_Date object
                    /**
                     * @see TicketEvolution_Date
                     */
                    require_once 'TicketEvolution/Date.php';

                    $this->{$prop} = new TicketEvolution_Date($val, TicketEvolution_Date::ISO_8601);
                    break;

                case 'retail_price':
                case 'wholesale_price':
                    // This property is a currency, convert it into a Zend_Currency object
                    /**
                     * @see Zend_Currency
                     */
                    require_once 'Zend/Currency.php';

                    $this->{$prop} = new Zend_Currency(
                        array(
                            'value'     => $val,
                            'currency'  => 'USD',
                            'locale'    => 'en_US',
                        )
                    );
                    break;

                default:
                    $this->{$prop} = $val;
            }
        }
    }
}
