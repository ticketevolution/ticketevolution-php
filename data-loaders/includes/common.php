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
 * @copyright   Copyright (c) 2012 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


/**
 * This file is just some code that was common to all the data-loaders
 */


/**
 * Set a $startTime variable to record when we started this script. This time
 * will be stored in the appropriate row of `dataLoaderStatus` so we know what
 * time to use the next time this script runs
 */
$startTime = new DateTime();


/**
 * Filter any input
 */
$filters = array(
    'showStats' => array(
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'lastRun' => array(
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'startPage' => array(
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
);
$validators = array(
    'showStats' => array(
        'Digits',
        'presence'      => 'optional',
        'allowEmpty'    => false
    ),
    'lastRun' => array(
        'presence'      => 'optional',
        'allowEmpty'    => false
    ),
    'startPage' => array(
        'presence'      => 'optional',
        'allowEmpty'    => false
    ),
);
$GET = new Zend_Filter_Input($filters, $validators, $_GET);

// Check if we want to show statistics while running
$showStats = (isset($GET->showStats)) ? $GET->showStats : false;

/**
 * Create an object for the `dataLoaderStatus` table
 */
$statusTable = new TicketEvolution_Db_Table_DataLoaderStatus();


/**
 * Set the date we last ran this script so we can get only entries that have
 * been added or changed since then
 *
 * Set a default lastRun date in case none was passed as a GET var and none is
 * available from `dataLoaderStatus`
 */
$lastRun = '2010-01-01';

/**
 * See if we have a lastRun stored in the `dataLoaderStatus` table
 */
$statusRow = $statusTable->find($statusData['table'], $statusData['type'])->current();
if (empty($statusRow)) {
    // We didn't get a row from the table so unset this
    unset($statusRow);
} else {
    $lastRun = $statusRow->lastRun;
}

/**
 * If a lastRun was passed as a GET var then use it instead of either of the above
 */
if (isset($GET->lastRun)) {
    $lastRun = $GET->lastRun;
}

if (!$lastRun = new DateTime($lastRun)) {
    throw new TicketEvolution_Webservice_Exception('The $lastRun date appears to be malformed');
}
/**
 * Convert $lastRun to UTC because the API currently ignores the time if it is
 * not specified as UTC. This is not expected behavior and it will be fixed soon.
 */
$lastRun->setTimezone(new DateTimeZone('UTC'));


/**
 * Get the Zend_Config object from the registry
 */
$registry = Zend_Registry::getInstance();


/**
 * Create the TEvo webservice object
 */
$tevo = new TicketEvolution_Webservice($registry->config->params);

/**
 * Set the default options for the request(s)
 */
$options = array(
    'page'              => 1,
    'per_page'          => 100,
    'updated_at.gte'    => $lastRun->format('c'),
    'performances[performer_id]' => 15677,
);

// "deleted" endpoints are more reliable using "deleted_at" instead of "updated_at"
if ($statusData['type'] == 'deleted') {
    $options['deleted_at.gte'] = $options['updated_at.gte'];
    unset($options['updated_at.gte']);
}

if (!empty($GET->startPage)) {
    $options['page'] = $GET->startPage;
}


/**
 * Because we page through the results incrementally we need to set a $maxPages
 * to go to. We don't actually know what this number really is until we've made
 * our first API call, so we will set it to a default and then adjust it to what it should
 * be after we've made an API call.
 */
$defaultMaxPages = ($options['page'] + 1);
$maxPages = $defaultMaxPages;


echo '<h1>Updating `' . $statusData['table'] . '` ' . $options['per_page'] . ' at a time with entries updated since ' . $lastRun->format('r') . '</h1>' . PHP_EOL;
