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
 * @package     TicketEvolution_DataLoader
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2012 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


/**
 * Extends Zend_Date with some handy constants and also allows for easy handling
 * of "TBA" event times.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution_DataLoader
 * @copyright   Copyright (c) 2012 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_DataLoader_Runner
{
    /**
     * Set a variable to record when we started this script. This time
     * will be stored in the appropriate row of `dataLoaderStatus` so we know what
     * time to use the next time this script runs
     *
     * @var DateTime
     */
    protected $_startTime;


    /**
     * The class of the `dataLoaderStatus` table
     *
     * @var string
     */
    protected $_statusTableClass = 'TicketEvolution_Db_Table_DataLoaderStatus';


    /**
     * The `dataLoaderStatus` table object
     *
     * @var Zend_Db_Table
     */
    protected $_statusTable;


    /**
     * The `dataLoaderStatus` table row
     *
     * @var Zend_Db_Table_Row
     */
    protected $_statusTableRow;


    /**
     *
     *
     * @var DateTime
     */
    protected $_lastRun;


    /**
     * The page to start with when fetching API results
     *
     * @var int
     */
    protected $_startPage = 1;


    /**
     * The number of results to request per page from the API.
     * Maximum 100
     *
     * @var int
     */
    protected $_perPage = 100;


    /**
     * Set to true to have some memory statistics displayed during run
     *
     * @var bool
     */
    protected $_showMemory = false;


    /**
     * Set to true to have display progress during run
     *
     * @var bool
     */
    protected $_showProgress = true;


    /**
     * Reference to the data loader object
     *
     * @var TicketEvolution_DataLoader_Abstract
     */
    protected $_dataLoader;


    /**
     *
     *
     * @param  Zend_Config|array    $apiConfig    Configuration for the API
     * @param  TicketEvolution_DataLoader_Abstract  $className    Which individual class to use
     * @param  array  $runnerOptions    Array of options
     */
    public function __construct($apiConfig, $className, array $runnerOptions=array())
    {
        // Set the startTime to now
        $this->_startTime = new DateTime();


        /**
         * Load the class.  This throws an exception
         * if the specified class cannot be loaded.
         */
        if (!class_exists($className)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($className);
        }


        /**
         * Create an instance of the adapter class.
         */
        $this->_loader = new $className($apiConfig, $this->_startTime);


        /**
         * Set the status row to store the completed progress
         */
        $this->_setStatusRow();


        /**
         * Process the other $runnerOptions
         */
        if (!empty($runnerOptions['startPage'])) {
            $this->_startPage = $runnerOptions['startPage'];
        }

        if (!empty($runnerOptions['perPage'])) {
            $this->_perPage = $runnerOptions['perPage'];
        }

        if (!empty($runnerOptions['showMemory'])) {
            $this->_showMemory = $runnerOptions['showMemory'];
        }

        if (!empty($runnerOptions['showProgress'])) {
            $this->_showProgress = $runnerOptions['showProgress'];
        }

        /**
         * Set the date we last ran this script so we can get only entries that have
         * been added/changed/deleted since then
         */
        if (!empty($runnerOptions['lastRun'])) {
            if (!$this->_lastRun = new DateTime($runnerOptions['lastRun'])) {
                throw new TicketEvolution_DataLoader_Exception('The $lastRun date your provided appears to be malformed');
            }
        } else {
            // The table should have either a previously set value
            // OR a default date of 2010-010-01 for the column
            $this->_lastRun = new DateTime($this->_statusRow->lastRun);
        }

        /**
         * Convert $_lastRun to UTC because the API currently ignores the time if it is
         * not specified as UTC. This is not expected behavior and should be fixed soon.
         */
        $this->_lastRun->setTimezone(new DateTimeZone('UTC'));
    }


    /**
     * Gets a row from $_statusTable to record the completion time.
     * If one doesn't exist, create an empty one.
     *
     * @return
     * @throws
     */
    protected function _setStatusRow()
    {
        /**
         * See if we have a lastRun stored in the `dataLoaderStatus` table
         */
        $statusRow = $this->_getStatusTable()->find($this->_loader->endpoint, $this->_loader->itemStatus)->current();
        if (!empty($statusRow)) {
            $this->_statusRow = $statusRow;
        } else {
            // We didn't get a row from the table so create an empty one
            $this->_statusRow = $this->_getStatusTable()->createRow();
            $this->_statusRow->table = $this->_loader->endpoint;
            $this->_statusRow->type = $this->_loader->itemStatus;

        }
    }


    /**
     * Gets a row from $_statusTable to record the completion time.
     * If one doesn't exist, create an empty one.
     *
     * @return Zend_Db_Table
     */
    protected function _getStatusTable()
    {
        if ($this->_statusTable === null) {
            /**
             * Load the status table class.  This throws an exception
             * if the specified class cannot be loaded.
             */
            if (!class_exists($this->_statusTableClass)) {
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($this->_statusTableClass);
            }

            /**
             * Create an instance of the status table class.
             */
            $this->_statusTable = new $this->_statusTableClass();
        }

        return $this->_statusTable;
    }


    /**
     * Runs the data loader, looping through all pages and all results
     *
     * @return
     * @throws
     */
    public function loadAllData()
    {
        /**
         * Set the default options for the request(s)
         */
        $options = array(
            'page'              => $this->_startPage,
            'per_page'          => $this->_perPage,
            'updated_at.gte'    => $this->_lastRun->format('c'),
        );

        // "deleted" endpoints are more reliable using "deleted_at" instead of "updated_at"
        if ($this->_loader->itemStatus == 'deleted') {
            $options['deleted_at.gte'] = $options['updated_at.gte'];
            unset($options['updated_at.gte']);
        }


        if ($this->_showProgress) {
            echo '<h1>Updating `' . $this->_statusRow->table . '` ' . $this->_perPage . ' at a time with entries updated since ' . $this->_lastRun->format('r') . '</h1>' . PHP_EOL;
        }


   		// Outer loop through pages of API results
//    		try {
   			while ($this->_loader->totalPages == null || $options['page'] <= $this->_loader->totalPages) {
       			$this->_loader->processResults($options, $this->_showProgress, $this->_showMemory);
       			$options['page']++;
       		}
//    		} catch (Exception $e) {
//    		    var_dump($e);
//        		throw new TicketEvolution_DataLoader_Exception('Unable to process ' . $this->_statusRow->table . ' results for page ' . $options['page']);
//    		}


   		// Record the DataLoaderStatus
   		try {
            // Update `tevoDataLoaderStatus` with current info
            $this->_statusRow->lastRun = (string) $this->_startTime->format('c');

            try {
                $this->_statusRow->save();
            } catch (Exception $e) {
                throw new TicketEvolution_DataLoader_Status($e);
            }

            if ($this->_showProgress) {
                echo '<h1>Finished updating `tevo' . $this->_statusRow->table . '` table</h1>' . PHP_EOL;
            }

            if ($this->_showMemory) {
                $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
                $peakMem = new Zend_Measure_Binary(memory_get_peak_usage(true), Zend_Measure_Binary::BYTE);
                echo '<h1>Current memory usage at end of script: ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL
                   . '<h1>PEAK memory usage: ' . $peakMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL;
            }
   		} catch (Exception $e) {
       		throw new TicketEvolution_DataLoader_Exception('Unable to mark ' . $className . ' as completed');
   		}


    }


}
