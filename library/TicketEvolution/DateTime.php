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
 * @package     TicketEvolution_DateTime
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2012 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


/**
 * Extends PHP's DateTime with some handy constants and also allows for easy
 * handling of "TBA" event times.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution_DateTime
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_DateTime extends DateTime
{
    /**
     * These are here for convenience
     */
    const MYSQL_DATETIME = 'Y-m-d H:i:s';
    const MYSQL_DATE = 'Y-m-d';
    const MYSQL_TIME = 'H:i:s';

    const DATETIME_FULL_NOTZ = 'l, F j, Y g:i a';

    const DATETIME_EBAY = 'Y-m-dTH:i:s.u';

    const DATE_FULL_US = 'l, F j, Y';
    const DATE_LONG_US = 'F j, Y';

    const TIME_12_HOUR = 'g:i a';
    const TIME_24_HOUR = 'H:i';


    /**
     * Does exactly what DateTime does except that after setting the intial date/time
     * we check to see if it contains times that are known to be "TBA" for events
     * such as Event Inventory's '23:59:20' and '23:59:59' and if so it resets
     * the time to be '00:00:00' which is what we use as TBA.
     *
     * @param  string|null  $date    A date/time string. Enter NULL here to obtain the current time when using the $timezone parameter
     * @param  DateTimeZone $timezone $part    A DateTimeZone object representing the desired time zone. If $timezone is omitted, the current timezone will be used.
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function __construct($date = null, $timezone = null)
    {
        /**
         * Work around PHP bug 52063 that was fixed in 5.3.6
         *
         * @link https://bugs.php.net/bug.php?id=52063
         * @link http://www.php.net/ChangeLog-5.php#5.3.6
         */
        if (!is_null($timezone)) {
            parent::__construct($date, $timezone);
        } else {
            parent::__construct($date);
        }

        if ($this->format('H:i') === '23:59' || $this->format('H:i') === '03:30') {
            // 23:59 is an EventInventory TBA time
            // 03:30 is a TicketNetwork TBA time
            // Set the time to '00:00:00'
            $this->setTime(0, 0, 0);
        }
    }

    /**
     * A "replacement" for the standard "format()" method that attempts to strip
     * time information from the requested format if the time appears to be for
     * a TBA event and replace it with a 'TBA' string
     *
     * @param  string $format Format accepted by date().
     * @return string  Returns the formatted date string on success or FALSE on failure.
     */
    public function formatTbaSafe($format)
    {
        if ($this->format('H:i') === '00:00') {
            // This time is for a TBA event
            $patterns = array('/[aABgGhHisu:]+/', '/ $/');
            $replacements = array('');
            $partCleaned = preg_replace($patterns, $replacements, $part);
            return $this->format($partCleaned) . ' ' . self::TBA_DISPLAY;
        }
        return $this->format($format);
    }


    /**
     * Mimics the the basics of Zend_Date::compare().
     * Doesn't support comparing parts.
     *
     * Compares a date or datepart with the existing one.
     * Returns -1 if earlier, 0 if equal and 1 if later.
     *
     * @param  string|integer|array|DateTime  $date    Date or datepart to compare with the date object
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compare($date) {
        if ($this > $date) {
            return 1;
        } elseif ($this < $date) {
            return -1;
        }
        return 0;
    }


}
