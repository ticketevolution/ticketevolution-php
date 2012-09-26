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
 * @copyright   Copyright (c) 2012 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


/**
 * Display errors
 */
error_reporting (E_ALL);


/**
 * Increase the max_execution_time because some of these can take a while to run
 */
ini_set('max_execution_time', 2400);


/**
 * Get the configuration
 * Be sure to copy config.sample.php to config.php and enter your own information.
 */
require_once 'config.php';


/**
 * Set up autoloading
 */
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend_');
$autoloader->registerNamespace('TicketEvolution_');
$autoloader->setFallbackAutoloader(true);


/**
 * Put the config data into registry
 */
$config = new Zend_Config($dlConfig, true);
$registry = Zend_Registry::getInstance();
$registry->set('config', $config);


/**
 * Set up the Db adapter
 */
$regConfig = $registry->get('config');
$dbConfig = $regConfig->database;
$db = Zend_Db::factory($dbConfig);
Zend_Db_Table::setDefaultAdapter($db);
