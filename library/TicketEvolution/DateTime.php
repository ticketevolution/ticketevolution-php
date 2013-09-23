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
 * @package     TicketEvolution\DateTime
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2013 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */


namespace TicketEvolution;


/**
 * Extends PHP's DateTime with some handy constants and also allows for easy
 * handling of "TBA" event times.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution\DateTime
 * @copyright   Copyright (c) 2013 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */
class DateTime extends \DateTime
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

    const TBA_DISPLAY = 'TBA';


    /**
     * Does exactly what DateTime does except that after setting the intial date/time
     * we check to see if it contains times that are known to be "TBA" for events
     * such as Event Inventory's '23:59:20' and '23:59:59' and if so it resets
     * the time to be '00:00:00' which is what we use as TBA.
     *
     * @param  string|null  $date    A date/time string. Enter NULL here to obtain the current time when using the $timezone parameter
     * @param  DateTimeZone $timezone $part    A DateTimeZone object representing the desired time zone. If $timezone is omitted, the current timezone will be used.
     * @return TicketEvolution\DateTime
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
     * Checks to see if the time would be considered "TBA"
     *
     * @return bool
     */
    public function isTba()
    {
        if ($this->format('H:i') === '23:59' // EventInventory
         || $this->format('H:i') === '03:30' // TicketNetwork
         || $this->format('H:i') === '00:00' // Team One/Ticket Evolution
        ) {
            return true;
        }
        return false;
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
        if ($this->isTba()) {
            // This time is for a TBA event
            $patterns = array('/[aABgGhHisu:]+/', '/ $/');
            $replacements = array('');
            $partCleaned = preg_replace($patterns, $replacements, $format);
            return $this->format($partCleaned) . ' ' . self::TBA_DISPLAY;
        }
        return $this->format($format);
    }


}
