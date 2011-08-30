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
 * @package     TicketEvolution_Db
 * @subpackage  Table
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 * @version     $Id: Events.php 84 2011-07-10 08:06:54Z jcobb $
 */

/**
 * @see TicketEvolution_Db_Table_Abstract
 */
require_once 'TicketEvolution/Db/Table/Abstract.php';

/**
 * @category    TicketEvolution
 * @package     TicketEvolution_Db
 * @subpackage  Table
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_Db_Table_Events extends TicketEvolution_Db_Table_Abstract
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $_name   = 'tevoEvents';

    /**
     * The primary key column or columns.
     * A compound key should be declared as an array.
     * You may declare a single-column primary key
     * as a string.
     *
     * @var mixed
     */
    protected $_primary   = 'eventId';
    
    /**
     * The column that we use to indicate status in boolean form
     *
     * @var string
     */
    protected $_statusColumn   = 'eventStatus';
    
    /**
     * Simple array of class names of tables that are "children" of the current
     * table, in other words tables that contain a foreign key to this one.
     * Array elements are not table names; they are class names of classes that
     * extend Zend_Db_Table_Abstract.
     *
     * @var array
     */
    protected $_dependentTables = array(
        'TicketEvolution_Db_Table_Eventperformers',
    );
    
    
    /**
     * Associative array map of declarative referential integrity rules.
     * This array has one entry per foreign key in the current table.
     * Each key is a mnemonic name for one reference rule.
     *
     * Each value is also an associative array, with the following keys:
     * - columns       = array of names of column(s) in the child table.
     * - refTableClass = class name of the parent table.
     * - refColumns    = array of names of column(s) in the parent table,
     *                   in the same order as those in the 'columns' entry.
     * - onDelete      = "cascade" means that a delete in the parent table also
     *                   causes a delete of referencing rows in the child table.
     * - onUpdate      = "cascade" means that an update of primary key values in
     *                   the parent table also causes an update of referencing
     *                   rows in the child table.
     *
     * @var array
     */
    protected $_referenceMap    = array(
        'tevoVenue'             => array(
            'columns'           => 'venueId',
            'refTableClass'     => 'TicketEvolution_Db_Table_Venues',
            'refColumns'        => 'venueId'
            ),
        'Venue'             => array(
            'columns'           => 'venueId',
            'refTableClass'     => 'TeamOne_Db_Table_Venues',
            'refColumns'        => 'venueId'
            ),
        'Configuration'     => array(
            'columns'           => 'configurationId',
            'refTableClass'     => 'TicketEvolution_Db_Table_Configurations',
            'refColumns'        => 'configurationId'
            ),
        'Category'          => array(
            'columns'           => 'categoryId',
            'refTableClass'     => 'TicketEvolution_Db_Table_Categories',
            'refColumns'        => 'categoryId'
            ),
    );
    
    
    /**
     * Get results via an array of parameters
     * (Jeff): I am overriding the abstract method here because I wanted to add the getPast option
     *
     * @param  mixed $params Options to use for the search query or a `uid`
     * @throws TicketEvolution_Models_Exception
     * @return TeamOne_Db_Events
     */
    public function getByParameters($params, $limit=null, $orderBy=null, $getPast=false)
    {
        /**
         * @see Zend_Date
         */
        require_once 'Zend/Date.php';

        if (!is_array($params) && !is_array($this->_primary)) {
            // Assume this is a single Id and find it
            $row = $this->find((int)$params);
            if (isset($row[0])) {
                return $row[0];
            } else {
                return false;
            }
        }
        
        // It appears that we have an array of search options
        $options = $this->_prepareOptions($params);
        
        $select = $this->select();
        foreach ($options as $column => $value) {
            // Some parameters may be like 'tevoPerformerId' 
            // We need to change those to just 'performerId'
            $column = lcfirst(preg_replace('/^tevo(\w{1})/i', "$1", $column));
            if (is_array($value)) {
                $select->where($column ." IN (?)", $value);
            } elseif ($value instanceof Zend_Date) {
                $select->where($column ." = ?", $value->get(TicketEvolution_Date::MYSQL_DATETIME));
            } else {
                $select->where($column ." = ?", $value);
            }
        }
        
        if (!isset($options['eventDate']) && !$getPast) {
            $curDate = new Zend_Date();
            $select->where("eventDate > ?", $curDate->get(TicketEvolution_Date::MYSQL_DATETIME));
        }
        
        if (!is_null($orderBy)) {
            if (is_array($orderBy)) {
                foreach ($orderBy as $order) {
                    $select->order($order);
                }
            } else {
                $select->order($orderBy);
            }
        }
        
        if (!is_null($limit)) {
            $select->limit($limit);
        }

        try{
            if ($limit == 1) {
                $results = $this->fetchRow($select);
            } else {
                $results = $this->fetchAll($select);
            }
        } catch(Exception $e) {
            throw new TicketEvolution_Db_Table_Exception($e);
        }
        return $results;
    }


}