<?php

/**
 * TicketEvolution Framework
 *
 * This file is just some code that was common to all the data-loaders
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
    'lastRun' => array(
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'startPage' => array(
        'Int',
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'perPage' => array(
        'Int',
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'showMemory' => array(
        new Zend_Filter_Boolean(array(
            'type'      => Zend_Filter_Boolean::ALL,
        )),
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'showProgress' => array(
        new Zend_Filter_Boolean(array(
            'type'      => Zend_Filter_Boolean::ALL,
        )),
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'fullRefresh' => array(
        new Zend_Filter_Boolean(array(
            'type'      => Zend_Filter_Boolean::ALL,
        )),
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
);
$validators = array(
    'lastRun' => array(
        'Date',
        'presence'      => 'optional',
        'allowEmpty'    => true,
        'default'       => null,
    ),
    'startPage' => array(
        'Int',
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => (int) 1,
    ),
    'perPage' => array(
        'Int',
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => (int) 100,
    ),
    'showMemory' => array(
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => false
    ),
    'showProgress' => array(
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => true
    ),
    'fullRefresh' => array(
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => false
    ),
);
$GET = new Zend_Filter_Input($filters, $validators, $_GET);

/**
 * Get the Zend_Config object from the registry
 */
$registry = Zend_Registry::getInstance();


/**
 * Set the default options for the request(s)
 */
$options = array(
    'lastRun'           => $GET->lastRun,
    'startPage'         => $GET->startPage,
    'perPage'           => $GET->perPage,
    'showMemory'        => $GET->showMemory,
    'showProgress'      => $GET->showProgress,
);


/**
 * If a "fullRefresh" was specified, overwrite the $lastRun date with one old
 * enough to grab everything.
 */
if ($GET->fullRefresh) {
    $options['lastRun'] = '2010-01-01';
}
