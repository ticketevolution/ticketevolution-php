<?php
/**
 * Ticketevolution Framework
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
 * @category    Ticketevolution
 * @package     Ticketevolution_Db
 * @subpackage  Table
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 * @version     $Id: Abstract.php 61 2011-06-09 03:43:48Z jcobb $
 */

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @category    Ticketevolution
 * @package     Ticketevolution_Db
 * @subpackage  Table
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class Ticketevolution_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * The column that we use to indicate status in boolean form
     *
     * @var string
     */
    protected $_statusColumn   = 'status';
    
    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Ticketevolution_Db_Table_Row';

    /**
     * Returns the name of the column we are using to track status
     *
     * @return string
     */
    public function getStatusColumn() {
        // If a _statusColumn is explicitly set the return the column name
        if(isset($this->_statusColumn)) {
            return $this->_statusColumn;
        }
        
        // If _statusColumn is not set find a column with 'status' in the name
        foreach($this->_getCols() as $column) {
            if(stripos($column, 'status') !== false) {
                return $column;
            }
        }
        
        return false;
    }


    /**
     * Run trim() on all the values of $data
     *
     * @param array $data
     * @return void
     */
    protected function _trimAllFields(array &$data) {
        array_map('trim', $data);
    }


    /**
     * Uses the table metadata to see which fields are NULLable and if the value
     * for that field is currently empty it will change it from an 
     * empty string '' to NULL
     *
     * @param array $data
     * @return void
     */
    protected function _setEmptyFieldsToNull(array &$data) {
        array_walk($data, array('Ticketevolution_Db_Table_Abstract', '_emptyFieldsToNull'));
    }


    /**
     * Used as the callback function for _setEmptyFieldsToNull()
     *
     * @param mixed $field
     * @param string $key
     * @return void
     */
    protected function _emptyFieldsToNull(&$field, $key) {
        if($this->_metadata[$key]['NULLABLE'] && empty($field)) {
            $field = null;
        }
    }


    /**
     * Override the default insert() method to ensure certain data integrity
     *
     * @param  array  $data  Column-value pairs.
     * @return mixed         The primary key of the row inserted.
     */
    public function insert(array $data)
    {
        $this->_trimAllFields($data);
        $this->_setEmptyFieldsToNull($data);
        
        // Make sure we don't try and set an empty createdDate
        // We don't need have to check if it exists because MySQL will add it 
        // automatically but we will for consistency
        if(!isset($data['createdDate']) || empty($data['createdDate'])) {
            $data['createdDate'] = date('c');
        }

        // Make sure we have a lastModifiedDate
        if(!isset($data['lastModifiedDate']) || empty($data['lastModifiedDate'])) {
            $data['lastModifiedDate'] = date('c');
        }

        return parent::insert($data);
    }


    /**
     * Override the default update() method to ensure certain data integrity
     *
     * @param  array        $data  Column-value pairs.
     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @return int          The number of rows updated.
     */
    public function update(array $data, $where)
    {
        $this->_trimAllFields($data);

        // Make sure we don't mess with createdDate
        if(isset($data['createdDate'])) {
            unset($data['createdDate']);
        }
        // Make sure we have a lastModifiedDate
        if(!isset($data['lastModifiedDate']) || empty($data['lastModifiedDate'])) {
            $data['lastModifiedDate'] = date('c');
        }
        
        return parent::update($data, $where);
    }


    /**
     * Override the default delete() because we never delete, we just change status
     */
    public function delete($where)
    {
        $data = array();
        $data[$this->_statusColumn] = 0;

        // Make sure we have a lastModifiedDate
        if(!isset($data['lastModifiedDate']) || empty($data['lastModifiedDate'])) {
            $data['lastModifiedDate'] = date('c');
        }
        
        return parent::update($data, $where);
    }


    /**
     * Get results via an array of parameters
     *
     * @param  mixed $params Options to use for the search query or a `uid`
     * @throws Ticketevolution_Models_Exception
     * @return TeamOne_Db_Events
     */
    public function getByParameters($params, $limit=null, $orderBy=null)
    {
        /**
         * @see Zend_Date
         */
        require_once 'Zend/Date.php';

        if(!is_array($params) && !is_array($this->_primary)) {
            // Assume this is a single Id and find it
            $row = $this->find((int)$params);
            if(isset($row[0])) {
                return $row[0];
            } else {
                return false;
            }
        }
        
        // It appears that we have an array of search options
        $options = $this->_prepareOptions($params);
        //dump($options);
        
        $select = $this->select();
        foreach($options as $column => $value) {
            // Some parameters may be like 'tevoPerformerId' 
            // We need to change those to just 'performerId'
            $column = lcfirst(preg_replace('/^tevo(\w{1})/i', "$1", $column));
            if(is_array($value)) {
                $select->where($column ." IN (?)", $value);
            } elseif($value instanceof Zend_Date) {
                $select->where($column ." = ?", $value->get(Ticketevolution_Date::MYSQL_DATETIME));
            } else {
                $select->where($column ." = ?", $value);
            }
        }

        if(!is_null($orderBy)) {
            if(is_array($orderBy)) {
                foreach($orderBy as $order) {
                    $select->order($order);
                }
            } else {
                $select->order($orderBy);
            }
        }
        
        if(!is_null($limit)) {
            $select->limit($limit);
        }
        //dump($select->__toString());
        try{
             if($limit == 1) {
                $results = $this->fetchRow($select);
                return $results;
            } else {
                $results = $this->fetchAll($select);
                return $results;
            }
        } catch(Exception $e) {
            // rethrow to be caught again and displayed if not live site
            throw $e;
        }
    }


    /**
     * Prepare options for queries
     * Mostly just make sure if we passed a Zend_Date object that it gets converted to a string
     *
     * @param  array $params Parameters to use for the search query
     * @return array
     */
    protected function _prepareOptions(array $params)
    {
        // Verify that parameters are in an array.
        if (!is_array($params)) {
            /**
             * @see Ticketevolution_Db_Exception
             */
            require_once 'Ticketevolution/Db/Exception.php';
            throw new Ticketevolution_Db_Exception('Query parameters must be in an array');
        }
        
        /**
         * Commented out because I decided to do the conversion in getByParameters() for now
         // Set an array of parameters that might be Zend_Date objects
        $possibleDateFields = array(
            'eventDate',
            'updated_at',
            'createdDate',
            'lastModifiedDate');
        
        // If any of our $params might be date objects convert them to strings
        $dateFieldsUsed = array_intersect(array_keys($params), array_values($possibleDateFields));
        //dump($dateFieldsUsed);
        $options = array();
        foreach($dateFieldsUsed as $dateField) {
            if($params[$dateField] instanceof Zend_Date) {
                //$options[$dateField] = $params[$dateField]->get(Zend_Date::DATETIME);
            }
        }
        //dump($options);
        //dump($params);
        $cleanOptions = array_merge($params, $options);
        return $cleanOptions;
        */
        return $params;
    }
}