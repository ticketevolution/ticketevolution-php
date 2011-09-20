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
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @see TicketEvolution_Webservice
 */
require_once 'TicketEvolution/Webservice.php';

/**
 * To avoid having to require those files you should set up autoloading.
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend_');
$autoloader->registerNamespace('TicketEvolution_');
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
$cfg['params']['apiVersion'] = (string) '8';
$cfg['params']['buyerId'] = 'YOUR_OFFICEID_HERE';

$cfg['params']['baseUri'] = (string) 'https://api.sandbox.ticketevolution.com'; // Sandbox
//$cfg['params']['baseUri'] = (string) 'https://api.ticketevolution.com'; // Production

$cfg['exclude']['brokerage'] = array(
    389, // Testing only
    691, // Testing only
    117, // Testing only
);
$cfg['exclusive']['brokerage'] = array(
    223, // Testing only
    154, // Testing only
);


/**
 * You can initialize the TicketEvolution class with either a Zend_Config object
 * or with the above array.
 * 
 * Zend_Config method
 * $config = new Zend_Config($cfg);
 * $tevo = new TicketEvolution($cfg->params);
 * 
 * Array method
 * $tevo = new TicketEvolution($cfg['params']);
 */
 
// We'll use the Zend_Config method here
$config = new Zend_Config($cfg);

$tevo = new TicketEvolution_Webservice($config->params);
 
// Set up some default query options
$options = array(
    'page' => 1,
    'per_page' => 10,
    //'brokerage_id' => 613,
    //'updated_at.gte' => '2011-04-13',
    //'event_id' => 136957,
    //'price.gte' => 220,
    //'price.lte' => 500,
    //'name' => 'Main Office',
    //'address[locality]' => 'Scottsdale',
    //'performances[performer_id]' => 15532,
);


/**
 * If the form has been submitted filter & validate the input for safety.
 * This is just good practice.
 */
if(isset($_GET['apiMethod'])) {
    /**
     * Filter/validate the input
     *
     * @see Zend_Filter_Input
     */
    require_once 'Zend/Filter/Input.php';
    
    $filters = array('*' => array('StringTrim' , 'StripTags', 'StripNewlines'));
    $validators = array(
        'apiMethod' => array(
            'Alpha',
            'presence'          => 'required',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => false,
        ),
        'id' => array(
            'Digits',
            'presence'          => 'optional',
            'allowEmpty'        => false,
        ),
        'eventId' => array(
            'Digits',
            'presence'          => 'optional',
            'allowEmpty'        => false,
        ),
        'userId' => array(
            'Digits',
            'presence'          => 'optional',
            'allowEmpty'        => false,
        ),
        'accountId' => array(
            'Digits',
            'presence'          => 'optional',
            'allowEmpty'        => false,
        ),
        'clientId' => array(
            'Digits',
            'presence'          => 'optional',
            'allowEmpty'        => false,
        ),
        'addressId' => array(
            'Digits',
            'presence'          => 'optional',
            'allowEmpty'        => false,
        ),
        'phoneNumberId' => array(
            'Digits',
            'presence'          => 'optional',
            'allowEmpty'        => false,
        ),
        'query' => array(
            'Alnum',
            'presence'          => 'optional',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => true,
        ),
        'rejectionReason' => array(
            'presence'          => 'optional',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => true,
        ),
    );
    $input = new Zend_Filter_Input($filters, $validators, $_GET);
    if ($input->hasInvalid() || $input->hasMissing()) {
        foreach ($input->getMessages() as $messageId => $message) {
            echo '<div class="error">'
               . current($message)
               . '</div>' . PHP_EOL
            ;
        }
        die();
    }
}

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
	<?php
	    if (strpos ($cfg['params']['baseUri'], 'sandbox') === false) {
	        // We're in PRODUCTION. Include a stylesheet highlighting that fact
	        echo '<link rel="stylesheet" href="css/production.css?v=2">' . PHP_EOL;
            // Set a variable containing HTML to disable certain API methods if we're not in the sandbox
            $disabled = ' disabled="disabled"';
	    } else {
	        // We're in the sandbox. Include a stylesheet highlighting that fact
	        echo '<link rel="stylesheet" href="css/sandbox.css?v=2">' . PHP_EOL;
    	    $disabled = null;
	    }
	?>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

</head>
<body>
	<div id="container">
		<header>

		</header>

		<div id="main" role="main">
		    <h1>Demonstration of the Ticket Evolution Framework for PHP with Zend Framework</h1>
		    <p>This is a quick demo of the Ticket Evolution Framework for PHP with Zend Framework which is used to access the <a href="http://api.ticketevolution.com/">Ticket Evolution Web Services API</a>. <a href="http://framework.zend.com/">Zend Framework</a> is an easy-to-use PHP framework that can be used in whole or in parts regardless of whether you program in MVC or procedural style. Simply make sure that the Zend Framework <code>/library</code> folder is in your PHP <code>include_path</code>.</p>
		    <p>All of the <code>list*()</code> methods will return a <code>TicketEvolution_Webservice_ResultSet</code> object with can be easily iterated using simple loops. If you prefer PHP’s <a href="http://www.php.net/manual/en/spl.iterators.php">built-in SPL iterators</a> you will be hapy to know that <code>TicketEvolution_Webservice_ResultSet</code> implements <a href="http://www.php.net/manual/en/class.seekableiterator.php">SeekableIterator</a>.</p>
		    <p>When accessing a single element of a <code>TicketEvolution_Webservice_ResultSet</code> or when returning data via any of the <code>show*()</code> methods an object specific to that type of data such as a <code>TicketEvolution_Venue</code> will be returned.</p>
		    <p>Any dates within any of the objects will be in <a href="http://framework.zend.com/manual/en/zend.date.html">Zend_Date</a> format to allow easy manipulation and conversion.</p>
		    <?php
                echo '<h2>Current configuration code</h2>' . PHP_EOL
                   . '<pre>' . PHP_EOL
                   . '/**' . PHP_EOL
                   . ' * Setup configuration' . PHP_EOL
                   . ' */' . PHP_EOL
                   . '$cfg[\'params\'][\'apiToken\'] = (string) \'' . $cfg['params']['apiToken'] . '\';' . PHP_EOL
                   . '$cfg[\'params\'][\'secretKey\'] = (string) \'' . $cfg['params']['secretKey'] . '\';' . PHP_EOL
                   . '$cfg[\'params\'][\'apiVersion\'] = (string) \'' . $cfg['params']['apiVersion'] . '\';' . PHP_EOL
                   . '$cfg[\'params\'][\'buyerId\'] = \'' . $cfg['params']['buyerId'] . '\';' . PHP_EOL
                   . '$cfg[\'params\'][\'baseUri\'] = (string) \'' . $cfg['params']['baseUri'] . '\';' . PHP_EOL
                   . PHP_EOL
                   . '/**' . PHP_EOL
                   . ' * Optional array of brokerageIds you want to remove from Ticket Group listings.' . PHP_EOL
                   . ' * You can use officeIds if you change the type in the excludeResults() call.' . PHP_EOL
                   . ' * ' . PHP_EOL
                   . ' * You can even use this to filter results by section/row/type/etc.' . PHP_EOL
                   . ' */' . PHP_EOL
                   . '$cfg[\'exclude\'][\'brokerage\'] = array(' . PHP_EOL
                ;
                foreach ($cfg['exclude']['brokerage'] as $brokerage) {
                    echo '    ' . $brokerage . ',' . PHP_EOL;
                }
                echo ');' . PHP_EOL
                   . PHP_EOL
                   . '/**' . PHP_EOL
                   . ' * Optional array of brokerageIds you ONLY want to show in Ticket Group listings.' . PHP_EOL
                   . ' * You can use officeIds if you change the type in the exclusiveResults() call.' . PHP_EOL
                   . ' * ' . PHP_EOL
                   . ' * You can even use this to filter results by section/row/type/etc.' . PHP_EOL
                   . ' */' . PHP_EOL
                   . '$cfg[\'exclusive\'][\'brokerage\'] = array(' . PHP_EOL
                ;
                foreach ($cfg['exclusive']['brokerage'] as $brokerage) {
                    echo '    ' . $brokerage . ',' . PHP_EOL;
                }
                echo ');' . PHP_EOL
                   . '</pre>' . PHP_EOL
                ;


		        if(isset($input)) {
		            // The form has been submitted. Demo the selected method.
		            $apiMethod = (string) $input->apiMethod;

		            /**
		             * This section documents the actual code used for the call.
		             * The final bit of code is added below because it is specific
		             * to each call.
		             */
		            echo '<h2>Code used for ' . $apiMethod . '() method</h2>' . PHP_EOL
		               . '<pre>' . PHP_EOL
		               . PHP_EOL
		               . PHP_EOL
		               . '/**' . PHP_EOL
		               . ' * Finished setting up configuration.' . PHP_EOL
		               . ' * Initialize a TicketEvolution_Webservice object.' . PHP_EOL
		               . ' */' . PHP_EOL
		               . '$tevo = new TicketEvolution_Webservice($config->params);' . PHP_EOL
		               . PHP_EOL
		               . PHP_EOL
		               . '/**' . PHP_EOL
		               . ' * Below here is where all the method-specific stuff is.' . PHP_EOL
		               . ' */' . PHP_EOL
		            ;
		            
                    /**
                     * Setup any necessary vars and execute the call
                     */
                    switch ($apiMethod) {
                        case 'listBrokers' :
                        case 'listClients' :
                        case 'listOffices' :
                        case 'listUsers' :
                        case 'listCategories' :
                        case 'listConfigurations' :
                        case 'listEvents' :
                        case 'listPerformers' :
                        case 'listSearch' :
                        case 'listVenues' :
                        case 'listOrders' :
                        case 'listQuotes' :
                        case 'listShipments' :
                        case 'listEvoPayAccounts' :
                            // Display the code
                            echo '$options = array(' . PHP_EOL;
                            foreach( $options as $key => $val) {
                                echo '    \'' . $key . '\' => ' . $val . ',' . PHP_EOL;
                            }
                            echo ');' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($options);' . PHP_EOL
                            ;

                            // Execute the call
                            $results = $tevo->$apiMethod($options);
                            
                            break;


                        case 'listTicketGroups' :
                            $options['event_id'] = $input->eventId;
                            unset($options['page']);
                            unset($options['per_page']);

                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($options);' . PHP_EOL;

                            // Execute the call
                            $results = $tevo->$apiMethod($options);
                            
                            // Uncomment to test filtering out brokers
                            //$results->excludeResults($cfg['exclude']['brokerage'], 'brokerage');

                            // Uncomment to test showing only specific brokers
                            //$results->exclusiveResults($cfg['exclusive']['brokerage'], 'brokerage');

                            // Display the code for sorting
                            echo '$sortOptions = array(' . PHP_EOL
                               . '    \'section\', // Defaults to SORT_ASC' . PHP_EOL
                               . '    \'row\' => SORT_DESC,' . PHP_EOL
                               . '    \'retail_price\' => SORT_ASC' . PHP_EOL
                               . ');' . PHP_EOL
                               . '$results->sortResults($sortOptions);' . PHP_EOL
                            ;

                            // Sort the results
                            $sortOptions = array(
                                'section', // Defaults to SORT_ASC
                                'row' => SORT_DESC,
                                'retail_price' => SORT_ASC
                            );
                            $results->sortResults($sortOptions);
                            break;


                        case 'listClientAddresses' :
                        case 'listClientPhoneNumbers' :
                        case 'listClientEmailAddresses' :
                        case 'listClientCreditCards' :
                            $clientId = $input->clientId;
                            
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($clientId, $options);' . PHP_EOL;

                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $options);
                            break;


                        case 'showClientAddress' :
                        case 'showClientPhoneNumber' :
                        case 'showClientEmailAddress' :
                            $id = $input->id;
                            $clientId = $input->clientId;
                            
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($clientId, $id, $options);' . PHP_EOL;

                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $id, $options);
                            break;


                        case 'listEvoPayTransactions' :
                            $evoPayAccountId = $input->accountId;
                            
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($evoPayAccountId, $options);' . PHP_EOL;

                            // Execute the call
                            $results = $tevo->$apiMethod($evoPayAccountId, $options);
                            break;


                        case 'showBroker' :
                        case 'showClient' :
                        case 'showOffice' :
                        case 'showUser' :
                        case 'showCategory' :
                        case 'showConfiguration' :
                        case 'showEvent' :
                        case 'showPerformer' :
                        case 'showSearch' :
                        case 'showVenue' :
                        case 'showTicketGroup' :
                        case 'showOrder' :
                        case 'showQuote' :
                        case 'showShipment' :
                        case 'showEvoPayAccount' :
                        case 'showEvoPayTransaction' :
                        default :
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($id);' . PHP_EOL;

                            // Execute the call
                            $results = $tevo->$apiMethod($input->id);
                            break;


                        case 'searchBrokers' :
                        //case 'searchClients' :
                        case 'searchOffices' :
                        case 'searchUsers' :
                        case 'searchPerformers' :
                        case 'search' :
                        case 'searchVenues' :
                        //case 'searchOrders' :
                        case 'searchQuotes' :
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '((string) $input->query, $options);' . PHP_EOL;
                            
                            // Execute the call
                            $results = $tevo->$apiMethod((string) $input->query, $options);
                            break;


                        case 'createClient' :
                            // Create the properly formatted client
                            $client = new stdClass;
                            $client->name = 'Morris “Moe” Szyslak';
                            
                            // Display the code
                            echo '$client = new stdClass;' . PHP_EOL
                               . '$client->name = \'Morris “Moe” Szyslak\';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($client);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($client);
                            break;


                        case 'updateClient' :
                            $clientId = $input->clientId;

                            // Create the properly formatted client
                            $client = new stdClass;
                            $client->name = 'Momar “Moe” Szyslak';
                            
                            // Display the code
                            echo '$client = new stdClass;' . PHP_EOL
                               . '$client->name = \'Momar “Moe” Szyslak\';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clientId, $client);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $client);
                            break;


                        case 'createClientAddress' :
                            $clientId = $input->clientId;

                            // Create the properly formatted client address
                            $address1 = new stdClass;
                            $address1->company = 'Moe’s Tavern';
                            $address1->street_address = '555 Evergreen Terrace';
                            $address1->locality = 'Springfield';
                            $address1->region = 'MG';
                            $address1->postal_code = '58008-0000';
                            $address1->country_code = 'US';
                            $address1->label = 'Work';
                            
                            // Create another properly formatted client address
                            $address2 = new stdClass;
                            $address2->street_address = '744 Evergreen Terrace';
                            $address2->locality = 'Springfield';
                            $address2->region = 'MG';
                            $address2->postal_code = '58008-1111';
                            $address2->country_code = 'US';
                            $address2->label = 'Ned’s House';
                            
                            $addresses[] = $address1;
                            $addresses[] = $address2;
                            
                            // Display the code
                            echo '$address1 = new stdClass;' . PHP_EOL
                               . '$address1->company = \'Moe’s Tavern\';' . PHP_EOL
                               . '$address1->street_address = \'555 Evergreen Terrace\';' . PHP_EOL
                               . '$address1->locality = \'Springfield\';' . PHP_EOL
                               . '$address1->region = \'MG\';' . PHP_EOL
                               . '$address1->postal_code = \'58008-0000\';' . PHP_EOL
                               . '$address1->country_code = \'US\';' . PHP_EOL
                               . '$address1->label = \'Work\';' . PHP_EOL
                               . PHP_EOL

                               . '$address2 = new stdClass;' . PHP_EOL
                               . '$address2->street_address = \'744 Evergreen Terrace\';' . PHP_EOL
                               . '$address2->locality = \'Springfield\';' . PHP_EOL
                               . '$address2->region = \'MG\';' . PHP_EOL
                               . '$address2->postal_code = \'58008-0000\';' . PHP_EOL
                               . '$address2->country_code = \'US\';' . PHP_EOL
                               . '$address2->label = \'Ned’s House\';' . PHP_EOL
                               . PHP_EOL
                               . '$addresses[] = $address1;' . PHP_EOL
                               . '$addresses[] = $address2;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clientId, $addresses);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $addresses);
                            break;


                        case 'updateClientAddress' :
                            $clientId = $input->clientId;
                            $addressId = $input->id;

                            // Create the properly formatted client address
                            $address = new stdClass;
                            $address->company = 'Uncle Moe’s Family Feed-Bag';
                            $address->extended_address = 'Next to King Toot’s';
                            $address->label = 'Work (and home)';
                            $address->name = 'Moe Szyslak';
                            
                            // Display the code
                            echo '$address = new stdClass;' . PHP_EOL
                               . '$address->company = \'Uncle Moe’s Family Feed-Bag\';' . PHP_EOL
                               . '$address->extended_address = \'Next to King Toot’s\';' . PHP_EOL
                               . '$address->label = \'Work (and home)\';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clientId, $addressId, $address);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $addressId, $address);
                            break;


                        case 'createClientPhoneNumber' :
                            $clientId = $input->clientId;

                            // Create the properly formatted client phone numbers
                            $phoneNumber1 = new stdClass;
                            $phoneNumber1->number = '243-6697';
                            $phoneNumber1->label = 'work';
                            
                            $phoneNumber2 = new stdClass;
                            $phoneNumber2->number = '243-6698';
                            $phoneNumber2->label = 'work fax';
                            
                            $phoneNumbers[] = $phoneNumber1;
                            $phoneNumbers[] = $phoneNumber2;
                            
                            // Display the code
                            echo '$phoneNumber1 = new stdClass;' . PHP_EOL
                               . '$phoneNumber1->number = \'243-6697\';' . PHP_EOL
                               . '$phoneNumber1->label = \'work\';' . PHP_EOL
                               . PHP_EOL
                               . '$phoneNumber2 = new stdClass;' . PHP_EOL
                               . '$phoneNumber2->number = \'243-6698\';' . PHP_EOL
                               . '$phoneNumber2->label = \'work fax\';' . PHP_EOL
                               . PHP_EOL
                               . '$phoneNumbers[] = $phoneNumber1;' . PHP_EOL
                               . '$phoneNumbers[] = $phoneNumber2;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clientId, $phoneNumbers);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $phoneNumbers);
                            break;


                        case 'updateClientPhoneNumber' :
                            $clientId = $input->clientId;
                            $phoneNumberId = $input->id;

                            // Create the properly formatted client phone number
                            $phoneNumber = new stdClass;
                            $phoneNumber->extension = '101';
                            $phoneNumber->country_code = '+1';
                            
                            // Display the code
                            echo '$phoneNumber = new stdClass;' . PHP_EOL
                               . '$phoneNumber->extension = \'101\';' . PHP_EOL
                               . '$phoneNumber->country_code = \'+1\';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clientId, $phoneNumberId, $phoneNumber);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $phoneNumberId, $phoneNumber);
                            break;


                        case 'createClientEmailAddress' :
                            $clientId = $input->clientId;

                            // Create the properly formatted client email address
                            $emailAddress1 = new stdClass;
                            $emailAddress1->address = 'moeissexxxy69@compuserve.com';
                            $emailAddress1->label = 'home';

                            $emailAddress2 = new stdClass;
                            $emailAddress2->address = 'moe.szyslak@moestavern.com';
                            $emailAddress2->label = 'work';

                            $emailAddresses[] = $emailAddress1;
                            $emailAddresses[] = $emailAddress2;
                            
                            // Display the code
                            echo '$emailAddress1 = new stdClass;' . PHP_EOL
                               . '$emailAddress1->address = \'moeissexxxy69@compuserve.com\';' . PHP_EOL
                               . '$emailAddress1->label = \'home\';' . PHP_EOL
                               . PHP_EOL
                               . '$emailAddress2 = new stdClass;' . PHP_EOL
                               . '$emailAddress2->number = \'moe.szyslak@moestavern.com\';' . PHP_EOL
                               . '$emailAddress2->label = \'work\';' . PHP_EOL
                               . PHP_EOL
                               . '$emailAddresses[] = $emailAddress1;' . PHP_EOL
                               . '$emailAddresses[] = $emailAddress2;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clientId, $emailAddresses);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $emailAddresses);
                            break;


                        case 'updateClientEmailAddress' :
                            $clientId = $input->clientId;
                            $emailAddressId = $input->id;

                            // Create the properly formatted client address
                            $emailAddress = new stdClass;
                            $emailAddress->address = 'moethetroll@eworld.net';
                            
                            // Display the code
                            echo '$emailAddress = new stdClass;' . PHP_EOL
                               . '$emailAddress->address = \'moethetroll@eworld.net\';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clientId, $address);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $emailAddressId, $emailAddress);
                            break;


                        case 'createClientCreditCard' :
                            /**
                             * It is not currently in the API documentation, but
                             * a name is required in order to create a credit card.
                             * The name can be passed as a credit card parameter
                             * or tied to the address passed.
                             *
                             * @link http://api.ticketevolution.com/#credit-cards
                             */
                            $clientId = $input->clientId;
                            $addressId = $input->addressId;
                            $phoneNumberId = $input->phoneNumberId;

                            // Create the properly formatted client credit card
                            $creditCard1 = new stdClass;
                            $creditCard1->name = 'Moe Szyslak';
                            $creditCard1->number = '4111111111111111';
                            $creditCard1->verification_code = '666';
                            $creditCard1->expiration_month = '12';
                            $creditCard1->expiration_year = '2013';
                            $creditCard1->address_id = (int) $addressId;
                            $creditCard1->phone_number_id = (int) $phoneNumberId;
                            $creditCard1->ip_address = '37.235.140.72';
                            $creditCards[] = $creditCard1;

                            /**
                             * API doesn't currently support adding multiples
                            $creditCard2 = new stdClass;
                            $creditCard2->number = '311111111111111';
                            $creditCard2->verification_code = '777';
                            $creditCard2->expiration_month = '01';
                            $creditCard2->expiration_year = '2014';
                            $creditCard2->address_id = $addressId;
                            $creditCard2->phone_number_id = $phoneNumberId;
                            $creditCards[] = $creditCard2;
                            */

                            // Display the code
                            echo '$creditCard1 = new stdClass;' . PHP_EOL
                               . '$creditCard1->number = \'4111111111111111\';' . PHP_EOL
                               . '$creditCard1->verification_code = \'666\';' . PHP_EOL
                               . '$creditCard1->expiration_month = \'12\';' . PHP_EOL
                               . '$creditCard1->expiration_year = \'2013\';' . PHP_EOL
                               . '$creditCard1->address_id = $addressId;' . PHP_EOL
                               . '$creditCard1->phone_number_id = $phoneNumberId;' . PHP_EOL
                               . '$creditCards[] = $creditCard1;' . PHP_EOL

                            /**
                             * API doesn't currently support adding multiples
                               . PHP_EOL
                               . '$creditCard2 = new stdClass;' . PHP_EOL
                               . '$creditCard2->number = \'311111111111111\';' . PHP_EOL
                               . '$creditCard2->verification_code = \'777\';' . PHP_EOL
                               . '$creditCard2->expiration_month = \'01\';' . PHP_EOL
                               . '$creditCard2->expiration_year = \'2014\';' . PHP_EOL
                               . '$creditCard2->address_id = $addressId;' . PHP_EOL
                               . '$creditCard2->phone_number_id = $phoneNumberId;' . PHP_EOL
                               . '$creditCards[] = $creditCard2;' . PHP_EOL
                             */
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clientId, $creditCards);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $creditCards);
                            break;


                        case 'updateClientCreditCard' :
                            $clientId = $input->clientId;
                            $creditCardId = $input->creditCardId;

                            // Create the properly formatted client address
                            $creditCard = new stdClass;
                            $creditCard->expiration_month = '08';
                            $creditCard->expiration_year = '15';
                            
                            // Display the code
                            echo '$creditCard = new stdClass;' . PHP_EOL
                               . '$creditCard->expiration_month = \'08\'' . PHP_EOL
                               . '$creditCard->expiration_year = \'15\';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clientId, $creditCardId, $creditCard);' . PHP_EOL
                            ;
    
                            // Execute the call
                            $results = $tevo->$apiMethod($clientId, $creditCardId, $creditCard);
                            break;


                        case 'createShipment' :
                            // Create the proper format
                            $item1 = new stdClass;
                            $item1->id = '1003';

                            $item2 = new stdClass;
                            $item2->id = '1004';

                            $items[] = $item1;
                            $items[] = $item2;
                            
                            $shipment1 = new stdClass;
                            $shipment1->items = $items;
                            $shipment1->airbill = "JVBERi0xLjMKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwgL0xlbmd0aCA1IDAg\nUiAvRmlsdGVyIC9GbGF0ZURlY29kZSA+PgpzdHJlYW0KeAErVAgEAAHnAOMK\nZW5kc3RyZWFtCmVuZG9iago1IDAgb2JqCjExCmVuZG9iagoyIDAgb2JqCjw8\nIC9UeXBlIC9QYWdlIC9QYXJlbnQgMyAwIFIgL1Jlc291cmNlcyA2IDAgUiAv\nQ29udGVudHMgNCAwIFIgL01lZGlhQm94IFswIDAgNjEyIDc5Ml0KPj4KZW5k\nb2JqCjYgMCBvYmoKPDwgL1Byb2NTZXQgWyAvUERGIF0gPj4KZW5kb2JqCjMg\nMCBvYmoKPDwgL1R5cGUgL1BhZ2VzIC9NZWRpYUJveCBbMCAwIDYxMiA3OTJd\nIC9Db3VudCAxIC9LaWRzIFsgMiAwIFIgXSA+PgplbmRvYmoKNyAwIG9iago8\nPCAvVHlwZSAvQ2F0YWxvZyAvUGFnZXMgMyAwIFIgPj4KZW5kb2JqCjggMCBv\nYmoKKFVudGl0bGVkKQplbmRvYmoKOSAwIG9iagooTWFjIE9TIFggMTAuNyBR\ndWFydHogUERGQ29udGV4dCkKZW5kb2JqCjEwIDAgb2JqCihUeWxlciBIdW50\nKQplbmRvYmoKMTEgMCBvYmoKKCkKZW5kb2JqCjEyIDAgb2JqCihUZXh0RWRp\ndCkKZW5kb2JqCjEzIDAgb2JqCihEOjIwMTEwODAzMDI0NDI1WjAwJzAwJykK\nZW5kb2JqCjE0IDAgb2JqCigpCmVuZG9iagoxNSAwIG9iagpbICgpIF0KZW5k\nb2JqCjEgMCBvYmoKPDwgL1RpdGxlIDggMCBSIC9BdXRob3IgMTAgMCBSIC9T\ndWJqZWN0IDExIDAgUiAvUHJvZHVjZXIgOSAwIFIgL0NyZWF0b3IgMTIgMCBS\nCi9DcmVhdGlvbkRhdGUgMTMgMCBSIC9Nb2REYXRlIDEzIDAgUiAvS2V5d29y\nZHMgMTQgMCBSIC9BQVBMOktleXdvcmRzIDE1IDAgUgo+PgplbmRvYmoKeHJl\nZgowIDE2CjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDYzNCAwMDAwMCBu\nIAowMDAwMDAwMTI1IDAwMDAwIG4gCjAwMDAwMDAyNjggMDAwMDAgbiAKMDAw\nMDAwMDAyMiAwMDAwMCBuIAowMDAwMDAwMTA3IDAwMDAwIG4gCjAwMDAwMDAy\nMjkgMDAwMDAgbiAKMDAwMDAwMDM1MSAwMDAwMCBuIAowMDAwMDAwNDAwIDAw\nMDAwIG4gCjAwMDAwMDA0MjYgMDAwMDAgbiAKMDAwMDAwMDQ3NSAwMDAwMCBu\nIAowMDAwMDAwNTA0IDAwMDAwIG4gCjAwMDAwMDA1MjMgMDAwMDAgbiAKMDAw\nMDAwMDU1MCAwMDAwMCBuIAowMDAwMDAwNTkyIDAwMDAwIG4gCjAwMDAwMDA2\nMTEgMDAwMDAgbiAKdHJhaWxlcgo8PCAvU2l6ZSAxNiAvUm9vdCA3IDAgUiAv\nSW5mbyAxIDAgUiAvSUQgWyA8NTk3YjAzMWY1ZTQxNWVlY2ZiMDUxZDVhZmQz\nOTk1NDI+Cjw1OTdiMDMxZjVlNDE1ZWVjZmIwNTFkNWFmZDM5OTU0Mj4gXSA+\nPgpzdGFydHhyZWYKODA3CiUlRU9GCg==\n";
                            $shipment1->order_id = '922';
                            $shipment1->tracking_number = '7987 6668 4568';
                            $shipment1->type = 'FedEx';
    
                            $shipments[] = $shipment1;
                            
                            // Display the code
                            echo '$item1 = new stdClass;' . PHP_EOL
                               . '$item1->id = \'1003\';' . PHP_EOL
                               . PHP_EOL
                               . '$item2 = new stdClass;' . PHP_EOL
                               . '$item2->id = \'1004\';' . PHP_EOL
                               . PHP_EOL
                               . '$items[] = $item1;' . PHP_EOL
                               . '$items[] = $item2;' . PHP_EOL
                               . PHP_EOL
                               . '$shipment1 = new stdClass;' . PHP_EOL
                               . '$shipment1->items = $items;' . PHP_EOL
                               . '$shipment1->airbill = "JVBERi0xLjMKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwgL0xlbmd0aCA1IDAg…";' . PHP_EOL
                               . '$shipment1->order_id = \'922\';' . PHP_EOL
                               . '$shipment1->tracking_number = \'7987 6668 4568\';' . PHP_EOL
                               . '$shipment1->type = \'FedEx\';' . PHP_EOL
                               . PHP_EOL
                               . '$shipments[] = $shipment1;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($shipments);' . PHP_EOL
                            ;

                            // Execute the call
                            $results = $tevo->$apiMethod($shipments);
                            break;


                        case 'createOrderEvoPay' :
                            // Create the proper format
                            $item = new stdClass;
                            $item->price = '295.0';
                            $item->ticket_group_id = '5276516';
                            $item->quantity = 2;
    
                            $order1 = new stdClass;
                            $order1->items[] = $item;
                            $order1->buyer_id = $cfg['params']['buyerId'];
                            
                            $orderDetails[] = $order1;
                            
                            // Display the code
                            echo '$item = new stdClass;' . PHP_EOL
                               . '$item->price = \'295.00\';' . PHP_EOL
                               . '$item->ticket_group_id = \'5276516\';' . PHP_EOL
                               . '$item->quantity = 2;' . PHP_EOL
                               . PHP_EOL
                               . '$order1 = new stdClass;' . PHP_EOL
                               . '$order1->items[] = $item;' . PHP_EOL
                               . '$order1->buyer_id = $cfg[\'params\'][\'buyerId\'];' . PHP_EOL
                               . PHP_EOL
                               . '$orderDetails[] = $order1;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->createOrder($orderDetails);' . PHP_EOL
                            ;

                            // Execute the call
                            $results = $tevo->createOrder($orderDetails);
                            break;


                        case 'createFulfillmentOrder' :
                            $fulfillment = true;
                            // Purposely "flow" into createOrderCustomer
                            
                        case 'createOrderCustomer' :
                            $fulfillment = (isset($fulfillment)) ? $fulfillment : false;
                            $clientId = $input->clientId;

                            // Create the proper format
                            $item = new stdClass;
                            $item->price = '295.0';
                            $item->ticket_group_id = '5276516';
                            $item->quantity = 2;
    
                            $shippingAddress = new stdClass;
                            $shippingAddress->street_address = '742 Evergreen Terrace';
                            $shippingAddress->locality = 'Springfield';
                            $shippingAddress->region = 'MG';
                            $shippingAddress->postal_code = '58008-6072';
                            $shippingAddress->country_code = 'US';
                            
                            $billingAddress = new stdClass;
                            $billingAddress->street_address = '744 Evergreen Terrace';
                            $billingAddress->extended_address = 'Flander’s Residence';
                            $billingAddress->locality = 'Springfield';
                            $billingAddress->region = 'MG';
                            $billingAddress->postal_code = '58008-6072';
                            $billingAddress->country_code = 'US';
                            
                            $order1 = new stdClass;
                            $order1->items[] = $item;
                            $order1->shipping_address = $shippingAddress;
                            $order1->billing_address = $billingAddress;
                            $order1->seller_id = $cfg['params']['buyerId'];
                            $order1->client_id = $clientId;
                            
                            $orderDetails[] = $order1;
                            
                            // Display the code
                            echo '$item = new stdClass;' . PHP_EOL
                               . '$item->price = \'295.00\';' . PHP_EOL
                               . '$item->ticket_group_id = \'5276516\';' . PHP_EOL
                               . '$item->quantity = 2;' . PHP_EOL
                               . PHP_EOL
                               . '$shippingAddress = new stdClass;' . PHP_EOL
                               . '$shippingAddress->street_address = \'742 Evergreen Terrace\';' . PHP_EOL
                               . '$shippingAddress->locality = \'Springfield\';' . PHP_EOL
                               . '$shippingAddress->region = \'MG\';' . PHP_EOL
                               . '$shippingAddress->postal_code = \'58008-6072\';' . PHP_EOL
                               . '$shippingAddress->country_code = \'US\';' . PHP_EOL
                               . PHP_EOL
                               . '$billingAddress = new stdClass;' . PHP_EOL
                               . '$billingAddress->street_address = \'744 Evergreen Terrace\';' . PHP_EOL
                               . '$billingAddress->extended_address = \'Flander’s Residence\';' . PHP_EOL
                               . '$billingAddress->locality = \'Springfield\';' . PHP_EOL
                               . '$billingAddress->region = \'MG\';' . PHP_EOL
                               . '$billingAddress->postal_code = \'58008-6072\';' . PHP_EOL
                               . '$billingAddress->country_code = \'US\';' . PHP_EOL
                               . PHP_EOL
                               . '$order1 = new stdClass;' . PHP_EOL
                               . '$order1->items[] = $item;' . PHP_EOL
                               . '$order1->shipping_address = $shippingAddress;' . PHP_EOL
                               . '$order1->billing_address = $billingAddress;' . PHP_EOL
                               . '$order1->seller_id = $cfg[\'params\'][\'buyerId\'];' . PHP_EOL
                               . '$order1->client_id = $clientId;' . PHP_EOL
                               . PHP_EOL
                               . '$orderDetails[] = $order1;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->createOrder($orderDetails, ' . (bool) $fulfillment . ');' . PHP_EOL
                            ;

                            // Execute the call
                            $results = $tevo->createOrder($orderDetails, (bool) $fulfillment);
                            break;


                        case 'acceptOrder' :
                            $orderId = $input->id;
                            $userId = $input->userId;
                            
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($orderId, $userId);' . PHP_EOL;

                            // Execute the call
                            $results = $tevo->$apiMethod($orderId, $userId);
                            break;


                        case 'rejectOrder' :
                            $orderId = $input->id;
                            $userId = $input->userId;
                            $rejectionReason = $input->rejectionReason;
                            
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($orderId, $userId, $rejectionReason);' . PHP_EOL;

                            // Execute the call
                            $results = $tevo->$apiMethod($orderId, $userId, $rejectionReason);
                            break;


                        case 'completeOrder' :
                            $orderId = $input->id;
                            
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($orderId);' . PHP_EOL;

                            // Execute the call
                            $results = $tevo->$apiMethod($orderId);
                            break;




                    }

                    echo '</pre>' . PHP_EOL; // Close up the echoing of the code used
                    
                    // Display the results
                    if ($apiMethod == 'acceptOrder') {
                        if ($results) {
                            echo '<h2>Order Successfully Confirmed</h2>' . PHP_EOL;
                        } else {
                            echo '<h2>Error Confirming Order</h2>' . PHP_EOL;
                        }
                    } elseif ($apiMethod == 'rejectOrder') {
                        if ($results) {
                            echo '<h2>Order Successfully Rejected</h2>' . PHP_EOL;
                        } else {
                            echo '<h2>Error Confirming Order</h2>' . PHP_EOL;
                        }
                    } else {
                        echo '<h2>Results of ' . $apiMethod . '() method</h2>' . PHP_EOL;
                        if($results instanceof Countable) {
                            echo '<p>There are ' . count($results) . ' results available.</p>' . PHP_EOL;
                            foreach ($results as $result) {
                                echo '<pre>';
                                print_r ($result);
                                echo '</pre><br />' . PHP_EOL;
                            }
                        } else {
                            echo '<pre>';
                            print_r ($results);
                            echo '</pre><br />' . PHP_EOL;
                        }
                        echo '<h2>print_r() of ' . get_class ($results) . ' result object</h2>' . PHP_EOL
                           . '<p>This shows all the public and protected properties of the full ' 
                           . '<strong>' . get_class ($results) . '</strong> object that is returned from the ' 
                           . '<strong>' . $apiMethod . '()</strong> call. Each method will return different '
                           . 'types of objects depending on what the data returned is.</p>'
                           
                           . '<pre>'; print_r($results); echo '</pre>' . PHP_EOL
                        ;
                    }
		        }
		    ?>
		    <h2>Demonstration Options</h2>
		    <form action="index.php" method="get" target="_top" id="APItest" onsubmit="checkForm();">
		        <fieldset>
		        <legend>Ticket Evolution Framework Demo</legend>
		        <label for="apiMethod" accesskey="m">Framework Method</label>
		        <select id="apiMethod" name="apiMethod" size="1" onchange="toggleOptions();">
		            <option label="Select a method&#8230;" value="">Select a method&#8230;</option>

		            <optgroup label="Brokerage Resources">
		            </optgroup>
		                <optgroup label="Brokerages Methods">
                            <option label="listBrokers" value="listBrokers">listBrokers</option>
                            <option label="showBroker" value="showBroker">showBroker</option>
                            <option label="searchBrokers" value="searchBrokers">searchBrokers</option>
                        </optgroup>
    
                        <optgroup label="Clients Methods">
                            <option label="listClients" value="listClients">listClients</option>
                            <option label="showClient" value="showClient">showClient</option>
                            <option label="createClient" value="createClient"<?php echo $disabled;?>>createClient</option>
                            <option label="updateClient" value="updateClient"<?php echo $disabled;?>>updateClient</option>

                            <option label="listClientAddresses" value="listClientAddresses">listClientAddresses</option>
                            <option label="showClientAddress" value="showClientAddress">showClientAddress</option>
                            <option label="createClientAddress" value="createClientAddress"<?php echo $disabled;?>>createClientAddress</option>
                            <option label="updateClientAddress" value="updateClientAddress"<?php echo $disabled;?>>updateClientAddress</option>

                            <option label="listClientPhoneNumbers" value="listClientPhoneNumbers">listClientPhoneNumbers</option>
                            <option label="showClientPhoneNumber" value="showClientPhoneNumber">showClientPhoneNumber</option>
                            <option label="createClientPhoneNumber" value="createClientPhoneNumber"<?php echo $disabled;?>>createClientPhoneNumber</option>
                            <option label="updateClientPhoneNumber" value="updateClientPhoneNumber"<?php echo $disabled;?>>updateClientPhoneNumber</option>

                            <option label="listClientEmailAddresses" value="listClientEmailAddresses">listClientEmailAddresses</option>
                            <option label="showClientEmailAddress" value="showClientEmailAddress">showClientEmailAddress</option>
                            <option label="createClientEmailAddress" value="createClientEmailAddress"<?php echo $disabled;?>>createClientEmailAddress</option>
                            <option label="updateClientEmailAddress" value="updateClientEmailAddress"<?php echo $disabled;?>>updateClientEmailAddress</option>

                            <option label="listClientCreditCards" value="listClientCreditCards">listClientCreditCards</option>
                            <option label="showClientCreditCard" value="showClientCreditCard">showClientCreditCard</option>
                            <option label="createClientCreditCard" value="createClientCreditCard"<?php echo $disabled;?>>createClientCreditCard</option>
                            <option label="updateClientCreditCard" value="updateClientCreditCard"<?php echo $disabled;?>>updateClientCreditCard</option>
                        </optgroup>
    
                        <optgroup label="Offices Methods">
                            <option label="listOffices" value="listOffices">listOffices</option>
                            <option label="showOffice" value="showOffice">showOffice</option>
                            <option label="searchOffices" value="searchOffices">searchOffices</option>
                        </optgroup>
    
                        <optgroup label="Users Methods">
                            <option label="listUsers" value="listUsers">listUsers</option>
                            <option label="showUser" value="showUser">showUser</option>
                            <option label="searchUsers" value="searchUsers">searchUsers</option>
                        </optgroup>

		            <optgroup label="Catalog Resources">
                    </optgroup>
                        <optgroup label="Categories Methods">
                            <option label="listCategories" value="listCategories">listCategories</option>
                            <option label="showCategory" value="showCategory">showCategory</option>
                        </optgroup>

                        <optgroup label="Configurations Methods">
                            <option label="listConfigurations" value="listConfigurations">listConfigurations</option>
                            <option label="showConfiguration" value="showConfiguration">showConfiguration</option>
                        </optgroup>

                        <optgroup label="Events Methods">
                            <option label="listEvents" value="listEvents">listEvents</option>
                            <option label="showEvent" value="showEvent">showEvent</option>
                        </optgroup>
    
                        <optgroup label="Performers Methods">
                            <option label="listPerformers" value="listPerformers">listPerformers</option>
                            <option label="showPerformer" value="showPerformer">showPerformer</option>
                            <option label="searchPerformers" value="searchPerformers">searchPerformers</option>
                        </optgroup>
    
                        <optgroup label="Search Methods">
                            <option label="search" value="search">Performers & Venues</option>
                        </optgroup>
    
                        <optgroup label="Venues Methods">
                            <option label="listVenues" value="listVenues">listVenues</option>
                            <option label="showVenue" value="showVenue">showVenue</option>
                            <option label="searchVenues" value="searchVenues">searchVenues</option>
                        </optgroup>

		            <optgroup label="Inventory Resources">
                    </optgroup>
                        <optgroup label="Ticket Groups">
                            <option label="listTicketGroups" value="listTicketGroups">listTicketGroups</option>
                            <option label="showTicketGroup" value="showTicketGroup">showTicketGroup</option>
                        </optgroup>
    
                        <optgroup label="Orders Methods">
                            <option label="listOrders" value="listOrders">listOrders</option>
                            <option label="showOrder" value="showOrder">showOrder</option>
                            <option label="createOrder (EvoPay)" value="createOrderEvoPay"<?php echo $disabled;?>>createOrder (EvoPay)</option>
                            <option label="createOrder (Customer)" value="createOrderCustomer"<?php echo $disabled;?>>createOrder (Customer)</option>
                            <option label="createFulfillmentOrder" value="createFulfillmentOrder"<?php echo $disabled;?>>createFulfillmentOrder</option>
                            <option label="acceptOrder" value="acceptOrder"<?php echo $disabled;?>>acceptOrder</option>
                            <option label="rejectOrder" value="rejectOrder"<?php echo $disabled;?>>rejectOrder</option>
                            <option label="completeOrder" value="completeOrder"<?php echo $disabled;?>>completeOrder</option>
                        </optgroup>
    
                        <optgroup label="Quotes Methods">
                            <option label="listQuotes" value="listQuotes">listQuotes</option>
                            <option label="showQuote" value="showQuote">showQuote</option>
                            <option label="searchQuotes" value="searchQuotes">searchQuotes</option>
                        </optgroup>
    
                        <optgroup label="Shipments Methods">
                            <option label="listShipments" value="listShipments">listShipments</option>
                            <option label="showShipment" value="showShipment">showShipment</option>
                            <option label="createShipment" value="createShipment"<?php echo $disabled;?>>createShipment</option>
                            <option label="updateShipment" value="createShipment"<?php echo $disabled;?>>createShipment</option>
                        </optgroup>

		            <optgroup label="EvoPay Resources">
		            </optgroup>
                        <optgroup label="Accounts Methods">
                            <option label="listEvoPayAccounts" value="listEvoPayAccounts">listEvoPayAccounts</option>
                            <option label="showEvopayaccount" value="showEvopayaccount">showEvopayaccount</option>
                        </optgroup>

                        <optgroup label="Transactions Methods">
                            <option label="listEvoPayTransacations" value="listEvoPayTransacations">listEvoPayTransacations</option>
                            <option label="showEvoPayTransacation" value="showEvoPayTransacation">showEvoPayTransacation</option>
                        </optgroup>

		        </select>
		        
		        <div id="idOption" class="options">
                    <br />
                    <br />
                    <label for="id">ID: </label>
                    <input name="id" id="id" type="text" value="49" size="10" maxlength="7" />
		        </div>

		        <div id="eventIdOption" class="options">
                    <br />
                    <br />
                    <label for="eventId">event_id: </label>
                    <input name="eventId" id="eventId" type="text" value="136957" size="10" maxlength="7" />
		        </div>

		        <div id="userIdOption" class="options">
                    <br />
                    <br />
                    <label for="userId">User ID: </label>
                    <input name="userId" id="userId" type="text" value="" size="10" maxlength="7" />
		        </div>

		        <div id="clientIdOption" class="options">
                    <br />
                    <br />
                    <label for="clientId">Client ID: </label>
                    <input name="clientId" id="clientId" type="text" value="" size="10" maxlength="7" />
		        </div>

		        <div id="accountIdOption" class="options">
                    <br />
                    <br />
                    <label for="accountId">Your EvoPay AccountID: </label>
                    <input name="accountId" id="accountId" type="text" value="" size="10" maxlength="7" />
		        </div>

		        <div id="addressIdOption" class="options">
                    <br />
                    <br />
                    <label for="addressId">Address ID: </label>
                    <input name="addressId" id="addressId" type="text" value="" size="10" maxlength="7" />
		        </div>

		        <div id="phoneNumberIdOption" class="options">
                    <br />
                    <br />
                    <label for="phoneNumberId">Phone Number ID: </label>
                    <input name="phoneNumberId" id="phoneNumberId" type="text" value="" size="10" maxlength="7" />
		        </div>

		        <div id="searchOption" class="options">
                    <br />
                    <br />
                    <label for="query">Query: </label>
                    <input name="query" id="query" type="text" value="front" size="20" maxlength="50" />
		        </div>

		        <div id="rejectionOption" class="options">
                    <br />
                    <br />
                    <label for="query">Rejection Reason: </label>
                    <select name="rejectionReason" id="rejectionReason">
                        <option label="Tickets No Longer Available" value="Tickets No Longer Available">Tickets No Longer Available</option>
                        <option label="Tickets Priced Incorrectly" value="Tickets Priced Incorrectly">Tickets Priced Incorrectly</option>
                        <option label="Duplicate Order" value="Duplicate Order">Duplicate Order</option>
                        <option label="Fraudulent Order" value="Fraudulent Order">Fraudulent Order</option>
                        <option label="This Reason Is Invalid" value="This Reason Is Invalid">This Reason Is Invalid</option>
                    </select>
		        </div>

		        <div id="listOptions" class="options">
                    <br />
                    <br />
                    <label for="">Options: </label>
                    <?php
                        foreach($options as $key => $val) {
                            echo '<br />' . $key . ' = ' . $val . PHP_EOL;
                        }
                    ?>
		        </div>

		        <br />
		        <br />
		        <input id="submit" type="submit" />
		        </fieldset>
		    </form>
		</div>

		<footer>

		</footer>
	</div>

    <script type="text/javascript">
    //<![CDATA[

        function toggleOptions()
        {
            hideAllOptions();

            var selValue = $('#apiMethod').val();


            //alert(selValue);
            switch (selValue) {
                case 'listTicketGroups':
                    $('#eventIdOption').fadeIn();
                    break;

                case 'listEvoPayTransacations':
                    $('#listOptions').fadeIn();
                    $('#accountIdOption').fadeIn();
                    break;

                case 'createOrderCustomer':
                    $('#clientIdOption').fadeIn();
                    break;

                case 'createFulfillmentOrder':
                case 'completeOrder':
                case 'createFulfillmentOrder':
                    $('#idOption').fadeIn();
                    break;

                case 'acceptOrder':
                    $('#idOption').fadeIn();
                    $('#userIdOption').fadeIn();
                    break;

                case 'rejectOrder':
                    $('#idOption').fadeIn();
                    $('#userIdOption').fadeIn();
                    $('#rejectionOption').fadeIn();
                    break;

                case 'rejectOrder':
                    $('#idOption').fadeIn();
                    $('#userIdOption').fadeIn();
                    $('#rejectionOption').fadeIn();
                    break;

                case 'updateClient':
                case 'listClientAddresses':
                case 'listClientPhoneNumbers':
                case 'listClientEmailAddresses':
                case 'listClientCreditCards':
                case 'createClientAddress':
                case 'createClientPhoneNumber':
                case 'createClientEmailAddress':
                    $('#clientIdOption').fadeIn();
                    break;

                case 'createClientCreditCard':
                    $('#addressIdOption').fadeIn();
                    $('#clientIdOption').fadeIn();
                    $('#phoneNumberIdOption').fadeIn();
                    break;

                case 'showClientAddress':
                case 'showClientPhoneNumber':
                case 'showClientEmailAddress':
                case 'updateClientAddress':
                case 'updateClientPhoneNumber':
                case 'updateClientEmailAddress':
                case 'updateClientCreditCard':
                    $('#clientIdOption').fadeIn();
                    $('#idOption').fadeIn();
                    break;

                default:
                    if(selValue == 'completeOrder') {
                        $('#idOption').fadeIn();
                    } else if(selValue.substring(0,4) == 'list') {
                        $('#listOptions').fadeIn();
                    } else if(selValue.substring(0,4) == 'show') {
                        $('#idOption').fadeIn();
                    } else if(selValue.substring(0,6) == 'search') {
                        $('#searchOption').fadeIn();
                    } else if(selValue.substring(0,6) == 'create') {
                    } else if(selValue.substring(0,6) == 'update') {
                        $('#idOption').fadeIn();
                    }
            }
        }
        
        function hideAllOptions()
        {
            $('.options').fadeOut();
        }
        
        function checkForm()
        {
            // Set any hidden inputs to inactive to keep them from being submitted.
            // Keeps the URL cleaner
            $('.options:hidden :input').attr('disabled', true);
            return true;
        }
    //]]>
    </script>
</body>
</html>
