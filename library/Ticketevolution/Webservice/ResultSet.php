<?php
/**
 * Ticketevolution Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@teamonetickets.com so we can send you a copy immediately.
 *
 * @category    Ticketevolution
 * @package     Ticketevolution
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt     New BSD License
 * @version     $Id: ResultSet.php 28 2011-05-09 22:53:01Z jcobb $
 */


/**
 * @category    Ticketevolution
 * @package     Ticketevolution
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt     New BSD License
 */
class Ticketevolution_Webservice_ResultSet implements SeekableIterator, Countable
{
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
     * What type of results do we have
     *
     * @var string
     */
    protected $_resultSetType;

    /**
     * Create an instance of Ticketevolution_ResultSet and create the necessary data objects
     *
     * @param  object $result
     * @return void
     */
    public function __construct($result)
    {
        $this->_result = $result;
        
        // Find the property that is an array
        // There will only be one
        foreach($result as $property => $val) {
            //dump($val);
            if(is_array($val)) {
                //dump($val);
                $this->_results =  $val;
                //dump($this->_results);
                $this->_resultSetType = preg_replace('/ies$/i', 'y', $property);
                // Remove trailing 's' from this property name
                $this->_resultSetType = preg_replace('/s$/i', '', $this->_resultSetType);
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
        $className = 'Ticketevolution_' . ucwords($this->_resultSetType);

        /*
         * Load the item's class.  This throws an exception
         * if the specified class cannot be loaded.
         */
        if (!class_exists($className)) {
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
     * Remove entries attributed to certain brokerages.
     * This is mainly used after performing a listTicketgroups() and is be used 
     * to pass in an array of brokerage IDs to filter out their inventory
     *
     * @param array $omit   An array of brokerage IDs to remove
     * @return boolean
     */
    public function filterResults($omit, $type='brokerage')
    {
        $this->_results = array_filter($this->_results, function($v) use($omit, $type) { return !in_array($v->$type->id, $omit); });
        
        // Put the keys back in order, filling in any now-missing keys
        sort($this->_results);
    }


}
