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
 * Make sure the Zend Framework library is in your include_path
 * You may need to uncomment and adjust this.
 */
set_include_path (get_include_path() . PATH_SEPARATOR . '../library');

/**
 * Set up autoloading
 */
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend_');
$autoloader->registerNamespace('TicketEvolution_');
$autoloader->setFallbackAutoloader(true);


/**
 * Set your Ticket Evolution API information.
 * This is available from your account under Brokerage->API Keys
 *
 * NOTE: These are exclusive to your company and should NEVER be shared with
 *       anyone else. These should be protected just like your bank password.
 *
 * @link http://exchange.ticketevolution.com/brokerage/credentials
 */
$cfg['params']['apiToken'] = (string) 'YOUR_API_TOKEN_HERE';
$cfg['params']['secretKey'] = (string) 'YOUR_SECRET_KEY_HERE';

//$cfg['params']['baseUri'] = (string) 'http://api.sandbox.ticketevolution.com'; // Sandbox
$cfg['params']['baseUri'] = (string) 'http://api.ticketevolution.com'; // Production


/**
 * Database setup
 * Make sure you have created the database using the script
 * provided in scripts/create_tables.mysql
 * 
 */
$cfg['database']['adapter']             = 'Mysqli';
$cfg['database']['params']['host']      = 'YOUR_MYSQL_HOST';
$cfg['database']['params']['dbname']    = 'YOUR_DATABASE_NAME';
$cfg['database']['params']['username']  = 'YOUR_DATABASE_USER';
$cfg['database']['params']['password']  = 'YOUR_DATABASE_PASSWORD';

/**
 * Put cfg data into registry
 */
$config = new Zend_Config($cfg, true);
$registry = Zend_Registry::getInstance(); 
$registry->set('config', $config);

/**
 * Set up Db adapter
 */
$regConfig = $registry->get('config');
$dbConfig = $regConfig->database;    
$db = Zend_Db::factory( $dbConfig );
Zend_Db_Table::setDefaultAdapter( $db );


/**
 * LOCALE SETTINGS
 * If this isn't set in your php.ini set it here.
 * @link http://www.php.net/manual/en/timezones.america.php
 */
//date_default_timezone_set('America/Phoenix');

Zend_Locale::setDefault('en_US');


