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
class TicketEvolution_Webservice_ResultSet_Abstract
    implements SeekableIterator, Countable
{
    /**
     * An array of objects
     *
     * @var array
     */
    protected $_results = null;

    /**
     * Current index for SeekableIterator
     *
     * @var int
     */
    protected $_currentIndex = 0;


    /**
     * Create an instance of TicketEvolution_ResultSet and create the necessary data objects
     *
     * @param  object $result
     * @return void
     */
    public function __construct($result)
    {
        // Find the property that is an array
        // There will only be one
        foreach ($result as $key) {
            if (is_array($key)) {
                $this->_results =  $key;
                break; // Break out of looping to find the array
            }
        }

        if (isset($result->total_entries)) {
            $this->_total_entries = (int) $result->total_entries;
        }

        if (isset($result->per_page)) {
            $this->_per_page = (int) $result->per_page;
        } else {
            $this->_per_page = $this->_total_entries;
        }

    }

    /**
     * Number of results returned in this ResultSet
     *
     * @return int Total number of results returned
     */
    public function count()
    {
        return (int) count($this->_results);
    }

    /**
     * Total Number of results available
     *
     * @return int Total number of results available
     */
    public function totalResults()
    {
        if (!is_null($this->_total_entries)) {
            return (int) $this->_total_entries;
        } else {
            // total_entries was not passed in the JSON
            // This happens when using listTicketGroups()
            return $this->count();
        }
    }

    /**
     * Total Number of pages returned
     *
     * @return int Total number of pages returned
     */
    public function totalPages()
    {
        $totalPages = ceil($this->totalResults() / $this->_per_page);
        return (int) $totalPages;
    }

    /**
     * Implement SeekableIterator::current()
     *
     * @return mixed
     */
    public function current()
    {
        return $this->_results[$this->_currentIndex];
    }

    /**
     * Implement SeekableIterator::key()
     *
     * @return int
     */
    public function key()
    {
        return $this->_currentIndex;
    }

    /**
     * Implement SeekableIterator::next()
     *
     * @return void
     */
    public function next()
    {
        $this->_currentIndex += 1;
    }

    /**
     * Implement SeekableIterator::rewind()
     *
     * @return void
     */
    public function rewind()
    {
        $this->_currentIndex = 0;
    }

    /**
     * Implement SeekableIterator::seek()
     *
     * @param  int $index
     * @throws OutOfBoundsException
     * @return void
     */
    public function seek($index)
    {
        $indexInt = (int) $index;
        if ($indexInt >= 0 && (null === $this->_results || $indexInt < count($this->_results))) {
            $this->_currentIndex = $indexInt;
        } else {
            throw new OutOfBoundsException("Illegal index '$index'");
        }
    }

    /**
     * Implement SeekableIterator::valid()
     *
     * @return boolean
     */
    public function valid()
    {
        return null !== $this->_results && $this->_currentIndex < count($this->_results);
    }


    /**
     * Remove entries that are in the supplied array
     * This is mainly used after performing a listTicketGroups() and can be used
     * to pass in an array of brokerage IDs to filter out their inventory if
     * you do not want it to show.
     *
     * Usage: $results = $tevo->listTicketGroups($options);
     *        $excludeArray = array(1,3,5,7,9);
     *        $results->excludeResults($excludeArray, 'brokerage');
     *
     * @param array $exclude   An array of brokerage IDs to REMOVE
     * @return void
     */
    public function excludeResults(array $exclude, $type='brokerage')
    {
        if ($type == 'brokerage') {
            // In ticketGroups brokerage is now a nested property of office
            $this->_results = array_filter(
                $this->_results, function($v) use($exclude, $type) {
                    return !in_array($v->office->$type->id, $exclude);
                }
            );
        } else {
            $this->_results = array_filter(
                $this->_results, function($v) use($exclude, $type) {
                    return !in_array($v->$type->id, $exclude);
                }
            );
        }

        // Put the keys back in order, filling in any now-missing keys
        sort($this->_results);
    }


    /**
     * Remove entries that are NOT in the supplied array
     * This is mainly used after performing a listTicketGroups() and can be used
     * to pass in an array of brokerage IDs to show ONLY their inventory.
     *
     * Usage: $results = $tevo->listTicketGroups($options);
     *        $exclusiveArray = array(2,4,6,8,10);
     *        $results->filterResults($exclusiveArray, 'brokerage');
     *
     * @param array $exclusive   An array of brokerage IDs to KEEP
     * @return void
     */
    public function exclusiveResults(array $exclusive, $type='brokerage')
    {
        if ($type == 'brokerage') {
            // In ticketGroups brokerage is now a nested property of office
            $this->_results = array_filter(
                $this->_results, function($v) use($exclusive, $type) {
                    return in_array($v->office->$type->id, $exclusive);
                }
            );
        } else {
            $this->_results = array_filter(
                $this->_results, function($v) use($exclusive, $type) {
                    return in_array($v->$type->id, $exclusive);
                }
            );
        }

        // Put the keys back in order, filling in any now-missing keys
        sort($this->_results);
    }


    /**
     * Sort the resultSet.
     *
     * Usage: $results = $tevo->listTicketGroups($options);
     *        $sortOptions = array('section', // Defaults to SORT_ASC
     *                             'row' => SORT_DESC,
     *                             'retail_price' => SORT_ASC);
     *        $results->sortResults($sortOptions);
     *
     * @param array $sortOptions   An array of sorting instructions
     * @return void
     */
    public function sortResults(array $sortOptions)
    {
        usort($this->_results, $this->_usortByMultipleKeys($sortOptions));
    }


    /**
     * Used by sortResults()
     *
     * @link http://www.php.net/manual/en/function.usort.php#103722
     * @param array $sortOptions   An array of sorting instructions
     * @return void
     */
    protected function _usortByMultipleKeys($key, $direction=SORT_ASC)
    {
        $sortFlags = array(SORT_ASC, SORT_DESC);
        if (!in_array($direction, $sortFlags)) {
            throw new InvalidArgumentException('Sort flag only accepts SORT_ASC or SORT_DESC');
        }
        return function($a, $b) use ($key, $direction, $sortFlags) {
            if (!is_array($key)) { //just one key and sort direction
                if (!isset($a->$key) || !isset($b->$key)) {
                    throw new TicketEvolution_Webservice_Exception('Attempting to sort on non-existent keys');
                }
                if ($a->$key == $b->$key) {
                    return 0;
                }
                return ($direction==SORT_ASC xor $a->$key < $b->$key) ? 1 : -1;
            } else { //using multiple keys for sort and sub-sort
                foreach ($key as $subKey => $subAsc) {
                    //array can come as 'sort_key'=>SORT_ASC|SORT_DESC or just 'sort_key', so need to detect which
                    if (!in_array($subAsc, $sortFlags)) {
                        $subKey = $subAsc;
                        $subAsc = $direction;
                    }
                    //just like above, except 'continue' in place of return 0
                    if (!isset($a->$subKey) || !isset($b->$subKey)) {
                        throw new TicketEvolution_Webservice_Exception('Attempting to sort on non-existent keys');
                    }
                    if ($a->$subKey == $b->$subKey) {
                        continue;
                    }
                    return ($subAsc==SORT_ASC xor $a->$subKey < $b->$subKey) ? 1 : -1;
                }
                return 0;
            }
        };
    }


    /**
     * Returns the entire $_results array as an array.
     *
     * Tests show that when looping through all the results, such as when
     * displaying all the TicketGroups on your website you can actually loop
     * over the array returned by this faster than you can loop over the entire
     * object.
     *
     * In one test looping through 400 items went from .03 seconds down to .013.
     *
     * If you want the ultimate in over-optimization you can use this. Make sure
     * you use sortResults(), excludeResults() or exclusiveResults() first, as
     * they obviously will not be available in the array returned by this method.
     *
     * @return array
     */
    public function getResultsAsArray() {
        return $this->_results;
    }

}
