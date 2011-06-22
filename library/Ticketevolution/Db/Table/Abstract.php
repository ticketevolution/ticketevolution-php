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

}