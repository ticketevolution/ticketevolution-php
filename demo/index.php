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
 * @package     Ticketevolution
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 * @version     $Id$
 */


/**
 * Make sure the Zend Framework library is in your include_path
 * You may need to uncomment and adjust this.
 */
set_include_path (get_include_path() . PATH_SEPARATOR . '../library');

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @see Ticketevolution_Webservice
 */
require_once 'Ticketevolution/Webservice.php';

/**
 * To avoid having to require those files you should set up autoloading.
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend_');
$autoloader->registerNamespace('Ticketevolution_');
$autoloader->setFallbackAutoloader(true);
 */


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
$cfg['params']['buyerId'] = 'YOUR_OFFICEID_HERE';

$cfg['params']['baseUri'] = (string) 'http://api.sandbox.ticketevolution.com'; // Sandbox
//$cfg['params']['baseUri'] = (string) 'http://api.ticketevolution.com'; // Production

$cfg['exclude']['brokerage'] = array(
    692, // Testing only
    67, // Testing only
    134, // Testing only
);
$cfg['exclusive']['brokerage'] = array(
    692, // Testing only
    67, // Testing only
    134, // Testing only
);


/**
 * You can initialize the Ticketevolution class with either a Zend_Config object
 * or with the above array.
 * 
 * Zend_Config method
 * $config = new Zend_Config($cfg);
 * $Tevo = new Ticketevolution($cfg->params);
 * 
 * Array method
 * $Tevo = new Ticketevolution($cfg['params']);
 */
 
// We'll use the Zend_Config method here
$config = new Zend_Config($cfg);

$Tevo = new Ticketevolution_Webservice($config->params);
 
// Set up some default query options
$options = array('page' => 1,
                 'per_page' => 10,
                 //'updated_at.gte' => '2011-04-13',
                 'event_id' => 38826,
                 //'price.gte' => 220,
                 //'price.lte' => 500,
                 );
//$options = 836;
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title>Ticket Evolution Framework Demo for PHP with Zend Framework</title>
	<meta name="description" content="Demonstration of the Ticket Evolution Framework for PHP with Zend Framework">
	<meta name="author" content="J Cobb <j+ticketevolution@teamonetickets.com>">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="stylesheet" href="css/style.css?v=2">

</head>
<body>
	<div id="container">
		<header>

		</header>

		<div id="main" role="main">
		    <h1>Demonstration of the Ticket Evolution Framework for PHP with Zend Framework</h1>
		    <p>This is a quick demo of the Ticket Evolution Framework for PHP with Zend Framework which is used to access the <a href="http://api.ticketevolution.com/">Ticket Evolution Web Services API</a>. <a href="http://framework.zend.com/">Zend Framework</a> is an easy-to-use PHP framework that can be used in whole or in parts regardless of whether you program in MVC or procedural style. Simply make sure that the Zend Framework <code>/library</code> folder is in your PHP <code>include_path</code>.</p>
		    <p>All of the <code>list*()</code> methods will return a <code>Ticketevolution_Webservice_ResultSet</code> object with can be easily iterated using simple loops. If you prefer PHP’s <a href="http://www.php.net/manual/en/spl.iterators.php">built-in SPL iterators</a> you will be hapy to know that <code>Ticketevolution_Webservice_ResultSet</code> implements <a href="http://www.php.net/manual/en/class.seekableiterator.php">SeekableIterator</a>.</p>
		    <p>When accessing a single element of a <code>Ticketevolution_Webservice_ResultSet</code> or when returning data via any of the <code>show*()</code> methods an object specific to that type of data such as a <code>Ticketevolution_Venue</code> will be returned.</p>
		    <p>Any dates within any of the objects will be in <a href="http://framework.zend.com/manual/en/zend.date.html">Zend_Date</a> format to allow easy manipulation and conversion.</p>
		    <?php
		        if(isset($_GET['apiMethod'])) {
		            // The form has been submitted. Demo the selected method.
		            $apiMethod = (string) strip_tags($_GET['apiMethod']);
		            
                    if(strpos($apiMethod, 'list') !== false) {
                        // We're using a list method
                        // Execute the API query using the above default options
                        $results = $Tevo->$apiMethod($options);
                        
                        // Testing only
                        if($apiMethod == 'listTicketgroups') {
                            //$results->excludeResults($cfg['exclude']['brokerage'], 'brokerage');
                            $results->exclusiveResults($cfg['exclusive']['brokerage'], 'brokerage');
                            $sortOptions = array(
                                'section', // Defaults to SORT_ASC
                                'row' => SORT_DESC,
                                'retail_price' => SORT_ASC
                            );
                            $results->sortResults($sortOptions);
                        }
                    } elseif(strpos($apiMethod, 'search') !== false) {
                        // We're using a search method
                        // Execute the API query
                        $results = $Tevo->$apiMethod((string) strip_tags($_GET['query']), $options);
                    } elseif(strpos($apiMethod, 'create') !== false) {
                        // We're using a create method
                        $order1 = array(
                            'items' => array(array(
                                'price' => '125.0',
                                'ticket_group_id' => '5333842',
                                'quantity' => 3,
                                ),
                            ),
                            'buyer_id' => $cfg['params']['buyerId'],
                            //'po_number' => 654321,
                            //'invoice_number' => 123456,
                            //'tax' => 3.28,
                            //'additional_expense' => 10.99,
                            //'instructions' => 'Will call for Homer Simpson 1 hour before. Emergency number: 480-555-1212',
                            
                        );
                        $order2 = array(
                            'items' => array(array(
                                'price' => '45.0',
                                'ticket_group_id' => '3017',
                                'quantity' => 2,
                                ),
                            ),
                            'buyer_id' => $cfg['params']['buyerId'],
                            //'po_number' => 654321,
                            //'invoice_number' => 123456,
                            //'tax' => 3.28,
                            //'additional_expense' => 10.99,
                            //'instructions' => 'Will call for Homer Simpson 1 hour before. Emergency number: 480-555-1212',
                            
                        );
                        $orderDetails[] = $order1;
                        //$orderDetails[] = $order2;
                        $results = $Tevo->$apiMethod($orderDetails);
                    } else {
                        // We're using a show method
                        // Execute the API query
                        $results = $Tevo->$apiMethod((int) strip_tags($_GET['id']));
                    }
                    
                    // Display the results
                    echo '<h2>Results of ' . $apiMethod . '() method</h2>' . PHP_EOL;
                    if($results instanceof Countable) {
                        echo '<p>There are ' . count($results) . ' results available.</p>' . PHP_EOL;
                    }
                    echo '<pre>'; print_r($results); echo '</pre>' . PHP_EOL;

		        }
		    ?>
		    <h2>Demonstration Options</h2>
		    <form action="index.php" method="get" target="_top" id="APItest">
		        <fieldset>
		        <legend>Ticket Evolution Framework Demo</legend>
		        <label for="apiMethod" accesskey="m">Framework Method</label>
		        <select id="apiMethod" name="apiMethod" size="1" tabindex="1" onchange="toggleOptions();">
		            <option label="Select a method&#8230;" value="">Select a method&#8230;</option>

		            <optgroup label="list*() Methods">
                        <option label="listBrokers" value="listBrokers">listBrokers</option>
                        <option label="listOffices" value="listOffices">listOffices</option>
                        <option label="listUsers" value="listUsers">listUsers</option>
                        <option label="listCategories" value="listCategories">listCategories</option>
                        <option label="listConfigurations" value="listConfigurations">listConfigurations</option>
                        <option label="listEvents" value="listEvents">listEvents</option>
                        <option label="listPerformers" value="listPerformers">listPerformers</option>
                        <option label="listVenues" value="listVenues">listVenues</option>
                        <option label="listTicketgroups" value="listTicketgroups">listTicketgroups</option>
                        <option label="listOrders" value="listOrders">listOrders</option>
                        <option label="listQuotes" value="listQuotes">listQuotes</option>
                        <option label="listEvopayaccounts" value="listEvopayaccounts">listEvopayaccounts</option>
                        <option label="listEvopaytransactions" value="listEvopaytransactions">listEvopaytransactions</option>
		            </optgroup>

		            <optgroup label="show*() Methods">
                        <option label="showBroker" value="showBroker">showBroker</option>
                        <option label="showOffice" value="showOffice">showOffice</option>
                        <option label="showUser" value="showUser">showUser</option>
                        <option label="showCategory" value="showCategory">showCategory</option>
                        <option label="showConfiguration" value="showConfiguration">showConfiguration</option>
                        <option label="showEvent" value="showEvent">showEvent</option>
                        <option label="showPerformer" value="showPerformer">showPerformer</option>
                        <option label="showVenue" value="showVenue">showVenue</option>
                        <option label="showTicketgroup" value="showTicketgroup">showTicketgroup</option>
                        <option label="showOrder" value="showOrder">showOrder</option>
                        <option label="showQuote" value="showQuote">showQuote</option>
                        <option label="showEvopayaccount" value="showEvopayaccount">showEvopayaccount</option>
                        <option label="showEvopaytransaction" value="showEvopaytransaction">showEvopaytransaction</option>
		            </optgroup>

		            <optgroup label="search*() Methods">
                        <option label="search" value="search">Performers & Venues</option>
                        <option label="searchBrokers" value="searchBrokers">searchBrokers</option>
                        <option label="searchOffices" value="searchOffices">searchOffices</option>
                        <option label="searchUsers" value="searchUsers">searchUsers</option>
                        <option label="searchPerformers" value="searchPerformers">searchPerformers</option>
                        <option label="searchVenues" value="searchVenues">searchVenues</option>
                        <option label="searchQuotes" value="searchQuotes">searchQuotes</option>
		            </optgroup>

		            <optgroup label="create*() Methods">
                        <option label="createOrder" value="createOrder">createOrder</option>
		            </optgroup>
		        </select>
		        
		        <div id="idOption">
                    <br />
                    <br />
                    <label for="id" accesskey="i">ID: </label>
                    <input name="id" id="id" type="text" value="48" tabindex="2" size="10" maxlength="7" />
		        </div>

		        <div id="searchOption">
                    <br />
                    <br />
                    <label for="query" accesskey="i">Query: </label>
                    <input name="query" id="query" type="text" value="front" tabindex="2" size="20" maxlength="50" />
		        </div>

		        <div id="options">
                    <br />
                    <br />
                    <label for="id" accesskey="i">Options: </label>
                    <?php
                        foreach($options as $key => $val) {
                            echo '<br />' . $key . ' = ' . $val . PHP_EOL;
                        }
                    ?>
		        </div>

		        <br />
		        <br />
		        <input id="submit" type="submit" tabindex="3" />
		        </fieldset>
		    </form>
		</div>

		<footer>

		</footer>
	</div>

    <script type="text/javascript">
    <!--
        window.onload=function(){
            toggleOptions();
        }

        function toggleOptions() {
            var selectedObj = document.getElementById("apiMethod");
            var selIndex = selectedObj.selectedIndex;
            var idDiv = document.getElementById("idOption");
            var searchDiv = document.getElementById("searchOption");
            var optionsDiv = document.getElementById("options");

            if(selIndex == 0) {
                idDiv.style.display = 'none';
                searchDiv.style.display = 'none';
                optionsDiv.style.display = 'none';
            } else if(selIndex > 25) {
                // Make the search div visible
                searchDiv.style.display = 'block';
                idDiv.style.display = 'none';
                optionsDiv.style.display = 'none';
            } else if(selIndex > 13) {
                // Make the ID div visible
                idDiv.style.display = 'block';
                searchDiv.style.display = 'none';
                optionsDiv.style.display = 'none';
            } else {
                // Make the deafult options visible
                idDiv.style.display = 'none';
                searchDiv.style.display = 'none';
                optionsDiv.style.display = 'block';
            }
        }
    //-->
    </script>
</body>
</html>