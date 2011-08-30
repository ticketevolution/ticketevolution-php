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
 * @version     $Id: ResultSet.php 79 2011-07-05 19:12:54Z jcobb $
 */


/**
 * @category    TicketEvolution
 * @package     TicketEvolution_Webservice
 * @subpackage  Webservice
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_Webservice_ResultSet_Abstract implements SeekableIterator, Countable
{
    /**
     * Name of the class for the items in this set
     *
     * @var string
     */
    protected $_itemClass = 'stdClass';

    /**
     * An array of objects
     *
     * @var array
     */
    protected $_results = null;

    /**
     * An object that has a single element which is an array of other objects
     *
     * @var stdObject
     */
    protected $_result;

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
    public function __construct($result, $itemClass=null)
    {
        $this->_result = $result;
        
        if (!is_null($itemClass)) {
            $this->_itemClass = $itemClass;
        }
        
        // Find the property that is an array
        // There will only be one
        foreach ($result as $property => $val) {
            if (is_array($val)) {
                $this->_results =  $val;
                break; // Break out of looping to find the array
            }
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
        return (int) $this->_result->total_entries;
    }

    /**
     * Total Number of pages returned
     *
     * @return int Total number of pages returned
     */
    public function totalPages()
    {
        $totalPages = ceil($this->totalResults() / $this->_result->per_page);
        return (int) $totalPages;
    }

    /**
     * Implement SeekableIterator::current()
     *
     * @return mixed
     */
    public function current()
    {
        $className = $this->_itemClass;

        /*
         * Load the item's class.  This throws an exception
         * if the specified class cannot be loaded.
         */
        if (!class_exists($this->_itemClass)) {
            /**
             * @see Zend_Loader
             */
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($className);
        }

        /*
         * Create an instance of the item's class.
         */
        return new $className($this->_results[$this->_currentIndex]);
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
     * This is mainly used after performing a listTicketgroups() and can be used 
     * to pass in an array of brokerage IDs to filter out their inventory if
     * you do not want it to show.
     *
     * Usage: $results = $tevo->listTicketgroups($options);
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
     * This is mainly used after performing a listTicketgroups() and can be used 
     * to pass in an array of brokerage IDs to show ONLY their inventory.
     *
     * Usage: $results = $tevo->listTicketgroups($options);
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
     * Usage: $results = $tevo->listTicketgroups($options);
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


}
