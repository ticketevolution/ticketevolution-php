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
 * @package     TicketEvolution_DateInterval
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */

/**
 * @category    TicketEvolution
 * @package     TicketEvolution_DateInterval
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_DateInterval extends DateInterval
{
    /**
     * Creates a TicketEvolution_DateInterval from a DateInterval instance.
     * Makes it easy to pass in a standard PHP DateInterval to create an TicketEvolution_DateInterval
     *
     * NOTE: Even if the $dateInterval you pass in has the 'days' property set
     *       it will be set to boolean false. Apparently you cannot extend
     *       DateInterval and still use the 'days' property. This also means
     *       that you cannot use format('%a') as it will return '(unknown)'
     *
     * @param DateInterval The DateInterval to create from
     * @return Onyx_DateInterval
     */
    public function __construct($dateInterval) {
        if ($dateInterval InstanceOf DateInterval) {
            $period = 'P';
            $time = 'T';

            if ($dateInterval->y > 0) {
                $period .= $dateInterval->y . 'Y';
            }
            if ($dateInterval->m > 0) {
                $period .= $dateInterval->m . 'M';
            }
            if ($dateInterval->d > 0) {
                $period .= $dateInterval->d . 'D';
            }
            if ($dateInterval->h > 0) {
                $time .= $dateInterval->h . 'H';
            }
            if ($dateInterval->i > 0) {
                $time .= $dateInterval->i . 'M';
            }
            if ($dateInterval->s > 0) {
                $time .= $dateInterval->s . 'S';
            }

            if ($time != 'T') {
                $period .= $time;
            }

            parent::__construct($period);
            $this->invert = $dateInterval->invert;
            //$this->days = $dateInterval->days;
        } else {
            parent::__construct($dateInterval);
        }
    }


    /**
     * Relative formatting of time as some are now calling "like Facebook".
     * Inspired by a comment in the PHP manual for DateInterval::format()
     *
     * @param bool $full true to show the ENTIRE difference
     * @return string
     * @link http://us2.php.net/manual/en/dateinterval.format.php#96768
     */
    function formatRelative($full=true) {
        // Adds an 's' to plural values
        $doPlural = function($num, $str) {return $num > 1 ? $str . 's' : $str;};

        $format = array();
        if($this->y !== 0) {
            $format[] = '%y ' . $doPlural($this->y, 'year');
        }
        if($this->m !== 0) {
            $format[] = '%m ' . $doPlural($this->m, 'month');
        }
        if($this->d !== 0) {
            $format[] = '%d ' . $doPlural($this->d, 'day');
        }
        if($this->h !== 0) {
            $format[] = '%h ' . $doPlural($this->h, 'hour');
        }
        if($this->i !== 0) {
            $format[] = '%i ' . $doPlural($this->i, 'minute');
        }
        if($this->s !== 0) {
            if(!count($format)) {
                return 'less than a minute ago';
            } else {
                $format[] = '%s ' . $doPlural($this->s, 'second');
            }
        }


        if ($full) {
            $end = ' and ' . array_pop($format);
            $format = implode (', ', $format) . $end;
        } else {
            if (count($format) > 1) {
                $format = array_shift($format) . ' and ' . array_shift($format);
            } else {
                $format = array_pop($format);
            }
        }

        if ($this->invert) {
            return $this->format($format) . ' from now';
        } else {
            return $this->format($format) . ' ago';
        }
        return $this->format($format);
    }


}
