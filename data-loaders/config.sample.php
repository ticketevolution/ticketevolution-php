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
 * You may need to adjust this.
 */
set_include_path (get_include_path() . PATH_SEPARATOR . '../library');


/**
 * Set your Ticket Evolution API information.
 * This is available from your account under Brokerage->API Keys
 *
 * NOTE: These are exclusive to your company and should NEVER be shared with
 *       anyone else. These should be protected just like your bank password.
 *
 * @link http://exchange.ticketevolution.com/brokerage/credentials
 */
$dlConfig['params']['apiToken']                 = (string) 'YOUR_API_TOKEN_HERE';
$dlConfig['params']['secretKey']                = (string) 'YOUR_SECRET_KEY_HERE';
$dlConfig['params']['buyerId']                  = (string) 'YOUR_OFFICEID_HERE';
$dlConfig['params']['apiVersion']               = (string) '9';
$dlConfig['params']['usePersistentConnections'] = (bool) true;

//$cfg['params']['baseUri']                       = (string) 'https://api.sandbox.ticketevolution.com'; // Sandbox
$dlConfig['params']['baseUri']                  = (string) 'https://api.ticketevolution.com'; // Production



/**
 * Database setup
 * Make sure you have created the database using the script
 * provided in scripts/create_tables.mysql
 * as well as applying any updates in chronological order
 *
 */
$dlConfig['database']['adapter']                = 'Mysqli';
$dlConfig['database']['params']['host']         = 'YOUR_MYSQL_HOST';
$dlConfig['database']['params']['dbname']       = 'YOUR_DATABASE_NAME';
$dlConfig['database']['params']['username']     = 'YOUR_DATABASE_USER';
$dlConfig['database']['params']['password']     = 'YOUR_DATABASE_PASSWORD';


/**
 * LOCALE SETTINGS
 * If this isn't set in your php.ini set it here.
 * @link http://www.php.net/manual/en/timezones.america.php
 */
//date_default_timezone_set('America/Phoenix');
Zend_Locale::setDefault('en_US');


