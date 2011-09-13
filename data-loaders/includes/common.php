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
 * @package     TicketEvolution
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


/**
 * This file is just some code that was common to all the data-cachers
 */
$filters = array('*' => array('StringTrim' , 'StripTags', 'StripNewlines'));
$validators = array(
    'lastRun' => array(
        'presence'      => 'optional',
        'allowEmpty'    => false
    ),
);
$GET = new Zend_Filter_Input($filters, $validators, $_GET);

// Create an object for the `dataLoaderStatus` table
$statusTable = new TicketEvolution_Db_Table_DataLoaderStatus();

// Set the date we last ran this script so we can get only entries that have
// been added or changed since then
// See if a lastRun date was specified to override what is stored in `dataLoaderStatus`
if(isset($GET->lastRun)) {
    $lastRun = $GET->lastRun;
    $statusRow = $statusTable->fetchRow($statusTable->select()->where("`table` = ?", $statusData['table']));
    if(!isset($statusRow->lastRun)) {
        // We didn't get a row from the table so unset this
        unset($statusRow);
    }
} else {
    // Get the lastRun date from `dataLoaderStatus` of when this script last completed
    if($statusRow = $statusTable->fetchRow($statusTable->select()->where("`table` = ?", $statusData['table']))) {
        $lastRun = $statusRow->lastRun;
    } else {
        // No entry in table. This is odd OR the first run. Make up a date.
        $lastRun = '2010-01-01';
    }
}
if(!$lastRun = new TicketEvolution_Date($lastRun, TicketEvolution_Date::ISO_8601)) {
    throw new TicketEvolution_Webservice_Exception('The $lastRun date appears to be malformed');
}

// Get the Zend_Config object from the registry
$registry = Zend_Registry::getInstance(); 

// Create the TEvo object
$tevo = new TicketEvolution_Webservice($registry->config->params);

// Set the "now" time for use later
$now = new TicketEvolution_Date();

// Set the options for the query
$options = array(
    'page' => 1,
    'per_page' => 100,
    'updated_at.gte' => $lastRun->get(TicketEvolution_Date::ISO_8601)
);

// Because we page through the results incrementally we need to set a $maxPages
// to go to. We don't actually know what this number really is until we've made
// our first API call, so we will set it to a default and then adjust it to what it should
// be after we've made an API call.
$defaultMaxPages = ($options['page'] + 1);
$maxPages = $defaultMaxPages;

echo '<h1>Updating `' . $statusData['table'] . '` ' . $options['per_page'] . ' at a time with entries updated since ' . $lastRun->get(TicketEvolution_Date::DATETIME) . '</h1>' . PHP_EOL;
