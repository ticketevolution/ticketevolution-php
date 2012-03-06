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
 * Get the configuration
 * Be sure to copy config.sample.php to config.php and enter your own information.
 */
require_once 'config.php';


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


// Set up some default query options
$options = array(
    'page'                          => 1,
    'per_page'                      => 10,
    //'updated_at.gte'                => '2012-01-31T19:55:38Z',
    //'updated_at.lte'                => '2010-07-30T17:41:48Z',
    //'brokerage_id'                  => 613,
    //'updated_at.gte'                => '2012-01-08',
    //'last_event_occurs_at.gte'      => '2012-01-08',
    //'event_id'                      => 136957,
    //'price.gte'                     => 220,
    //'price.lte'                     => 500,
    //'name'                          => 'Main Office',
    //'address[locality]'             => 'Scottsdale',
    //'performances[performer_id]'    => 16419,
    //'category_id'                   => 55,
    //'only_with_upcoming_events'     => 'true',
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
        'itemId' => array(
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
            'presence'          => 'optional',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => true,
        ),
        'rejectionReason' => array(
            'presence'          => 'optional',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => true,
        ),
        'environment' => array(
            'presence'          => 'required',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => false,
        ),
        'apiToken' => array(
            'presence'          => 'required',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => false,
        ),
        'apiVersion' => array(
            'Digits',
            'presence'          => 'required',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => false,
        ),
        'secretKey' => array(
            'presence'          => 'required',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => false,
        ),
        'buyerId' => array(
            'presence'          => 'required',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => false,
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

    $cfg['params']['apiToken'] = $input->apiToken;
    $cfg['params']['secretKey'] = $input->secretKey;
    $cfg['params']['buyerId'] = $input->buyerId;

    switch ($input->environment)
    {
        case 'production':
            $cfg['params']['baseUri'] = 'https://api.ticketevolution.com';
            break;

        case 'staging':
            $cfg['params']['baseUri'] = 'https://api.staging.ticketevolution.com';
            break;

        case 'sandbox':
        default:
            $cfg['params']['baseUri'] = 'https://api.sandbox.ticketevolution.com';
    }
    $cfg['params']['apiVersion'] = $input->apiVersion;

    /**
     * You can initialize the TicketEvolution class with either a Zend_Config object
     * or with the above $cfg array.
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
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

</head>
<body>
	<div id="container">
		<header>

		</header>

		<div id="main" role="main">
		    <h1>Demonstration of the Ticket Evolution Framework for PHP with Zend Framework</h1>
		    <p>This is a quick demo of the Ticket Evolution Framework for PHP with Zend Framework which is used to access the <a href="http://developer.ticketevolution.com/overview">Ticket Evolution Web Services API</a>. <a href="http://framework.zend.com/">Zend Framework</a> is an easy-to-use PHP framework that can be used in whole or in parts regardless of whether you program in MVC or procedural style. Simply make sure that the Zend Framework <code>/library</code> folder is in your PHP <code>include_path</code>.</p>
		    <p>All of the <code>list*()</code> methods will return a <code>TicketEvolution_Webservice_ResultSet</code> object with can be easily iterated using simple loops. If you prefer PHP’s <a href="http://www.php.net/manual/en/spl.iterators.php">built-in SPL iterators</a> you will be hapy to know that <code>TicketEvolution_Webservice_ResultSet</code> implements <a href="http://www.php.net/manual/en/class.seekableiterator.php">SeekableIterator</a>.</p>
		    <p>When accessing a single element of a <code>TicketEvolution_Webservice_ResultSet</code> or when returning data via any of the <code>show*()</code> methods an object specific to that type of data such as a <code>TicketEvolution_Venue</code> will be returned.</p>
		    <p>Any dates within any of the objects will be in <a href="http://framework.zend.com/manual/en/zend.date.html">Zend_Date</a> format to allow easy manipulation and conversion.</p>
		    <?php
		        if(isset($input)) {
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
                        case 'listOrders' :
                            $options['state'] = 'pending';
                        case 'listBrokers' :
                        case 'listClients' :
                        case 'listOffices' :
                        case 'listUsers' :
                        case 'listCategories' :
                        case 'listCategoriesDeleted' :
                        case 'listConfigurations' :
                        case 'listEvents' :
                        case 'listEventsDeleted' :
                        case 'listPerformers' :
                        case 'listPerformersDeleted' :
                        case 'listSearch' :
                        case 'listVenues' :
                        case 'listVenuesDeleted' :
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
                            try {
                                $results = $tevo->$apiMethod($options);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'listTicketGroups' :
                            $options['event_id'] = $input->eventId;
                            unset($options['page']);
                            unset($options['per_page']);

                            // Display the code
                            echo '$options = array(' . PHP_EOL;
                            foreach( $options as $key => $val) {
                                echo '    \'' . $key . '\' => ' . $val . ',' . PHP_EOL;
                            }
                            echo ');' . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($options);' . PHP_EOL;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($options);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }

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
                            try {
                                $results = $tevo->$apiMethod($clientId, $options);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'showClientAddress' :
                        case 'showClientPhoneNumber' :
                        case 'showClientEmailAddress' :
                            $id = $input->id;
                            $clientId = $input->clientId;

                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($clientId, $id, $options);' . PHP_EOL;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($clientId, $id, $options);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'listEvoPayTransactions' :
                            $evoPayAccountId = $input->accountId;

                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($evoPayAccountId, $options);' . PHP_EOL;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($evoPayAccountId, $options);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                        default :
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($id);' . PHP_EOL;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($input->id, $options);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'showEvoPayTransaction' :
                            $accountId = $input->accountId;
                            $transactionId = $input->id;
                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($accountId, $transactionId);' . PHP_EOL;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($accountId, $transactionId);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            try {
                                $results = $tevo->$apiMethod((string) $input->query, $options);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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

                            /**
                             * Test code for creating client with ALL data
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

                            $client->addresses[] = $address1;
                            $client->addresses[] = $address2;


                            $phoneNumber1 = new stdClass;
                            $phoneNumber1->number = '243-6697';
                            $phoneNumber1->label = 'work';

                            $phoneNumber2 = new stdClass;
                            $phoneNumber2->number = '243-6698';
                            $phoneNumber2->label = 'work fax';

                            $client->phone_numbers[] = $phoneNumber1;
                            $client->phone_numbers[] = $phoneNumber2;


                            $creditCard1 = new stdClass;
                            $creditCard1->name = 'Moe Szyslak';
                            $creditCard1->number = '4111111111111111';
                            $creditCard1->verification_code = '666';
                            $creditCard1->expiration_month = '12';
                            $creditCard1->expiration_year = '2013';
                            $creditCard1->address = $address1;
                            $creditCard1->phone_number_id = $phoneNumber1;
                            $creditCard1->ip_address = '37.235.140.72';

                            $creditCard2 = new stdClass;
                            $creditCard2->number = '311111111111111';
                            $creditCard2->verification_code = '777';
                            $creditCard2->expiration_month = '01';
                            $creditCard2->expiration_year = '2014';
                            $creditCard2->address = $address2;
                            $creditCard2->phone_number = $phoneNumber2;

                            $client->credit_cards[] = $creditCard1;
                            $client->credit_cards[] = $creditCard2;

                            $emailAddress1 = new stdClass;
                            $emailAddress1->address = 'moeissexxxy69@compuserve.com';
                            $emailAddress1->label = 'home';

                            $emailAddress2 = new stdClass;
                            $emailAddress2->address = 'moe.szyslak@moestavern.com';
                            $emailAddress2->label = 'work';

                            $client->email_addresses[] = $emailAddress1;
                            $client->email_addresses[] = $emailAddress2;
                             */

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($client);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            try {
                                $results = $tevo->$apiMethod($clientId, $client);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            //$addresses[] = $address2;

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
                            try {
                                $results = $tevo->$apiMethod($clientId, $addresses);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            try {
                                $results = $tevo->$apiMethod($clientId, $addressId, $address);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            try {
                                $results = $tevo->$apiMethod($clientId, $phoneNumbers);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            try {
                                $results = $tevo->$apiMethod($clientId, $phoneNumberId, $phoneNumber);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            try {
                                $results = $tevo->$apiMethod($clientId, $emailAddresses);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            try {
                                $results = $tevo->$apiMethod($clientId, $emailAddressId, $emailAddress);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                               . '$creditCard1->ip_address = \'37.235.140.72\';' . PHP_EOL
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
                            try {
                                $results = $tevo->$apiMethod($clientId, $creditCards);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            try {
                                $results = $tevo->$apiMethod($clientId, $creditCardId, $creditCard);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'createShipment' :
                            $orderId = $input->id;
                            $itemId = $input->itemId;

                            // Create the proper format
                            $item1 = new stdClass;
                            $item1->id = $itemId;

                            //$item2 = new stdClass;
                            //$item2->id = '1004';

                            $items[] = $item1;
                            //$items[] = $item2;

                            $shipment1 = new stdClass;
                            $shipment1->items = $items;
                            $shipment1->airbill = "JVBERi0xLjINCg0KMyAwIG9iag0KPDwNCi9FIDc0OTA5DQovSCBbIDEwMTEgMTQ2IF0NCi9MIDc1MTQxDQovTGluZWFyaXplZCAxDQovTiAxDQovTyA2DQovVCA3NTAzMQ0KPj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgDQplbmRvYmoNCg0KeHJlZg0KMyA4DQowMDAwMDAwMDEyIDAwMDAwIG4NCjAwMDAwMDA4ODggMDAwMDAgbg0KMDAwMDAwMTAxMSAwMDAwMCBuDQowMDAwMDAxMTU4IDAwMDAwIG4NCjAwMDAwMDE0MDcgMDAwMDAgbg0KMDAwMDAwMTUxNiAwMDAwMCBuDQowMDAwMDAxNjI0IDAwMDAwIG4NCjAwMDAwNzI5NTMgMDAwMDAgbg0KdHJhaWxlcg0KPDwNCi9BQkNwZGYgNzAyOQ0KL0lEIFsgPEIwOTA0Q0EzRDhEQzczNTNFQzdCNkVGOEU4NDEwRUYzPg0KPEVDNDExRDE5QzA4NkYxMzk0OTY0NzYzOTgxQzczMzQ0PiBdDQovUHJldiA3NTAyMQ0KL1Jvb3QgNCAwIFINCi9TaXplIDExDQo+PiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICANCnN0YXJ0eHJlZg0KMA0KJSVFT0YNCg0KNCAwIG9iag0KPDwNCi9PcGVuQWN0aW9uIFsgNiAwIFINCi9GaXQgXQ0KL091dGxpbmVzIDEgMCBSDQovUGFnZU1vZGUgL1VzZU5vbmUNCi9QYWdlcyAyIDAgUg0KL1R5cGUgL0NhdGFsb2cNCj4+DQplbmRvYmoNCg0KNSAwIG9iag0KPDwNCi9GaWx0ZXIgL0ZsYXRlRGVjb2RlDQovTGVuZ3RoIDU3DQovUyA1Mg0KPj4NCnN0cmVhbQ0KeJxjYGBgZWBg/sKgwMCoIA4kEUABClMYsALmzwxgvWDMwJAD1tsLxIxgPqOYKQMDuwWICQAIlwT+DQplbmRzdHJlYW0NCmVuZG9iag0KDQogNiAwIG9iag0KPDwNCi9Db250ZW50cyBbIDEwIDAgUiBdDQovTWVkaWFCb3ggWyAwIDAgNjEyIDc5MiBdDQovUGFyZW50IDIgMCBSDQovUmVzb3VyY2VzIDw8DQovRm9udCA8PA0KL0ZhYmM2IDcgMCBSDQovRmFiYzcgOCAwIFINCj4+DQovUHJvY1NldCBbIC9QREYNCi9UZXh0DQovSW1hZ2VCDQovSW1hZ2VDDQovSW1hZ2VJIF0NCi9YT2JqZWN0IDw8DQovSWFiYzggOSAwIFINCj4+DQo+Pg0KL1R5cGUgL1BhZ2UNCj4+DQplbmRvYmoNCg0KNyAwIG9iag0KPDwNCi9CYXNlRm9udCAvVGltZXMtUm9tYW4NCi9FbmNvZGluZyAvV2luQW5zaUVuY29kaW5nDQovU3VidHlwZSAvVHlwZTENCi9UeXBlIC9Gb250DQo+Pg0KZW5kb2JqDQoNCjggMCBvYmoNCjw8DQovQmFzZUZvbnQgL1RpbWVzLUJvbGQNCi9FbmNvZGluZyAvV2luQW5zaUVuY29kaW5nDQovU3VidHlwZSAvVHlwZTENCi9UeXBlIC9Gb250DQo+Pg0KZW5kb2JqDQoNCjkgMCBvYmoNCjw8DQovQml0c1BlckNvbXBvbmVudCA4DQovQ29sb3JTcGFjZSAvRGV2aWNlUkdCDQovRmlsdGVyIC9GbGF0ZURlY29kZQ0KL0hlaWdodCA5NTANCi9MZW5ndGggNzExNDANCi9TdWJ0eXBlIC9JbWFnZQ0KL1R5cGUgL1hPYmplY3QNCi9XaWR0aCAxNDAwDQo+Pg0Kc3RyZWFtDQp4nOzc25LrPI5tYb//S7sjelVlZZrE5ARAybI8vosdKyUIB1q2Ke+/6/kEAACvHv/v3V0AAAAAAIA74HcGAAAAAACwC78zAAAAAACAXfidAQAAAAAA7LL8neHxV+ry8o8Yj0Ah1Ua6k+is6DybqplwesnyLAAAAAAAPucJVDzvn/w7Qzbbxsdn3clyiczHf2dqsQ5+If8sAAAAAAC+5UOrfsg96BF1mjb7U8Pe3n5n08vy0qf/O4NYcOenBl0l9SoDAAAAAFDT/P8cP/N3hmy5jb05Pyzos84vA/7U/u8MovryLAAA2O4Re3drCXrXER33dyP+gnS2OtnFP67Wh94GV1Z7cfdGigsfgXJaJ4MIi/oRkYVW8VXEfaI/CZeXn9xVJ7KQSi/C9KwOEw0vL1yG6Xg+KwAAOI2/n78svWOZ7kaiMcVZZ03KyZdnz6yV7QTauJ7+6+vnLzemm8xmNpMsY/x+an3iC4n7JPWWnN6N47vp6d2cqa6m74XoDaLfXH4/fvzL4MswpwHxrn85ouOd0gAAYKMbfO2KfUi075r++ZR7oeVC6eSd0sup99bKdgJhXPzo4PmWL3S2TzFsNsys+xP23vV8lFy/wyt0vpFufjmgXo3x3+aiLSvqBsbjUXBqHcwOp4OL5rc0UF7P5VkAALDRp3/tRv2b+yu9+dm4PWuWPq1WthNo+v48v5+ohy19mkmmYS/vVrPuT9h71/NRcv0Or9D5Rsvm9aQvB6d/OpF+V1H+/llBp9LBoqjfz7KB7OX+WQAAsNGnf+1O+/85uNyf+Fu1qNaW5Kmt3aG1sp1Ai1bvCqtq7vz9Vp1hH/9V7md6yXvX81FyhQ5reY7o8ASpm2pcovHy6I7VkX5Xy1NRxVQDy6LRtcsl8tcn1UD2cv8sAADYaPm1+xMw/a6PNgDjWR2/t/9lz+JPkdzZwOjk5dJLu2r1O8EP54Z5+Xd06z7/vo7RS/YSUGigHLY868cUwt57lz5KrtBhLc8RHZ4g2/zLKo2XR2d1pN9VlGd8BV8iOy+32fw057QN/e9CA9nL/bMAAGCj5deu2NU4xx/DU4+/36g1r/NHnYjgZUUneb+0Nk1YqNXvBD+cu+X3v3+/WMsXdPqSTcOiHvTrng0bG1hmW+ZJxbzxRo2WXbtCh7U8R3R4gkLz+uaPzupIv6vpHf5zUFTsvNxm8+ZBp9tU5uUgheQAAOAIyw3Jcjsx/nu64dEJO82LI1GAOalzxEzeLy1srNXsBL85d0sUKV61l9d0eSrqQTdgThHFT+89P9uYJLVEJ4u61a7QYS3PER2eQDQfnfp9XN+BfmSqdKG6LrfkNO8s18sRs8Pl8eVoOuCjb2AAAD7LZOu52g/4ewnzSLN5caS5N8s2L5I7pV/Omqs0DS6P2ekELw66W8TZ1B2rLzSniDz+ymZ7BERjb7xRo261K3RYm+XMzjcSzevViGKiszqyUDpVPdvAsp9lcn1qeueYU4/Hl4PogI++gQEA+CyFb21xJBXcl91cpbZq2eabyR8z4/Fp0VTb5U6QpZfOf1H8s+U71gwreLmFzGxO2PS+LffZMX3LXOpd43cYRV5zrhTd/PK9owPGf5v3/PTsuNTTP0UJ0c/SMnNqlqiB6IjIIEose3DOAgCAjQrf2uJIKrgv2npNLZtpNr8r+Uur4xTLfg7qBFl66fwXxT9bvmNTN3ZWodCy6CPWabXmOp1E/A6v1vlGerTlEo2XR3ds6mYI7+PgM3kao48sB1m2lGpDJPQnMpNEYbWzAABgI3PLYR5JBfeZWyCzvWbz/eT+WR1zfieImK9R5yUrv7j6wt/HzRvAGVYkdPoZGxs5re51nU4ifodX63wjZzSxOOPB6I79+bdzJ2RfmmnFaa0oW2cddLci83IovyUnrHYWAABsZO6CzCOp4L5s87qZZvOd5NOzYq5LdQIhepk23pnZe9gssbzcD14Oa8Ysa6W63egRaKbqNFPu8F1reILTRrvxGgIAAJj6+3n9tOIcKcs2/7K7HjfbehZdUScv/LmcXU9dG7PQCbToLRC9ItMj5t2yzBOdEnd76gZwGojG1wuyLORcdZBHoJ/BTLW80E+77MRxxIx951Q5sxAAAMBlLXdE04BoizgG6yP9/ZjOEFWPNrfO2WUzneTTU06hl6tO6wRLyxfrabxxRIbo39GR6amoST+bP6wexylkDnUasXrLBSlc0q8ucnayicyFTra7XyEAAIDLWu6InO2oCNZH+vsxnWG5440uERc6/RSS/z6rSzy9p7lzOoHJWXDnyL+Dv89G/46OTE85d5TO5g87rZgqZA51mmj1hNqFW0rrhM6kT/kRMZ7yIw91Wq0zhwIAAMARbrlNBTRuxUtpPuyn7K1bm/SlulgQJyzbQ9nJ5QAAAPDRztk9skfFu7zce+WHRByk/7yfsrFobdJx8OWaLFOd4PyKAAAA+Fzn7B7Zo+Jdtjwh4jhbHvmnL6sIO6KcOem0Q7EsTp4TvKUoAAAAPtfRG0g2qHiv5uMhDrXlkT96WZtJshXLJaLmRYn1ym7lFG32pl+CQj8dZn59P5jZ/BsMAAAAALDkPJs7+smzGWrloktSa+UE7+XUrbX3E59an9Sipfrx85u30DJb6h4DAAAAAGjZJyzxUNaULRcFl1dgS9hBlqV/AlJN/g4eLxQ5zSonvCJOocLZ977cAAAAAPC5/Kd4Hd9UKBcFdxahHHC0ZfWfgFSfL8HRn2NOs0ph0Zy7ItuS8+JmrwIAAAAATPlP8Tq+aUuHy1ROldSp05ir5ESKtNGfOqzTdhTvz+vU0tmOuJ0AAAAA4GtlH/yj+KZCuSi4swjlgKMtq/8E6MV5DD8X/D4+DRDHp8lf/iHyT5vMziuOOKfE2fe+4gAAAADwoR6BVHy/nH5CNONrD4bmFIVhN0o9KY/B06UbD0Z5dP7o1VnmL88rZixkm55942sNAAAAAB9t+pC4fCgzg/sVo+BpvAiOrkqN4ESWl6VT+uXs9M/H358X/At1hmnCKHLXvNMAcYmzestbBQAAAADgyD5k9R/KogxjEhE5rajjRYls8+Ks+LPDeVEKf05PPVb/DYPIsDxrWl6o66ay+XcLAAAAAGCp8IRlXiIe35wnOz8yNem0Qyd4eeoR/H/9Z/tMFZ2e1X/qtD9HoiTL5C/j11ZgeaGum8o2PVu+zQAAAADgyxWe4qNLTP0Myw5F2+MUekAn1c/B568fFl7+bFo2I5YoNcjPkelyiePTI7VXyrlQ101l07eB1y8AAAAA4D/0I2r2qqXm5X6HUc8vDTijiZiXg+OpQp/L5pcNjz3oa6dHXk4tj0+P1F4p50JdN5VNv8pevwAAAACA/9CPqIULhebl2Q6nDT+9Hxmmf4rI57v/ewZxcOwzOvXkd4ZMDwAAAACA0fj87j9eRdeaCVOXnynb5O/gl2tLr0m41P6p3wdf+oz+/XJkmlNfomulOBf6hQpnpzcDAAAAAGBp+excu9zM5lxoltilNuM0OPlSrNfZP/4MHv+dbqP+l8fHbC9H/GUZI6MjzoJPO4wWx0kIAAAAADhU80mNR7ylaE3EWr2cihZ2PO5HTk+JbNlbYjmpecPom1OfBQAAAADgfj7oyfeDWgUAAAAA4Dtd9uHd/G8hAAAAAAC4sm/7b90vO91XvQoAAAAAgBuL/q/s39vVQa48Gj8yAAAAAABuYHyqvfFz7o1HAwAAAADgCvidAQAAAAAA7PLyfzTx++Dv/4Y/+l9yGAPGnOP/LcB48Jz/kwF+ZwAAAAAA4FAvvw+IU+NvAr//fMa/Toxhv0uMYUcPa4bp4CjgEeg0PL1cJ7/y2afxs9U04ZjTyWCmcvp/iSnMrjMDAAAAwA04z3HP4ceB6Z+/k4wJX079HJweOcjyKa/w0OpkqD1dist18muefeZ/ZygHRKWb/b+EbVwZAAAAALiN3089+ulseskz+AHhOTyOvYSNp054/tIlxBNilCcVn5puuj7v/VN3NT0bDeWshvNymKvtpHL6f8b3qr7WyQwAAAAA9/DyBPTyj+fqcfIZPzw+ZsZT0ZEj6PzmQ+tTPi/r59NUq9Hlex9yzzn7csRZDWclzdWupdL3g85fyAwAAAAA9xA9TL0EiKeq6OFx+TAVZT7uEUwkF6VTz8vLEs0+C/GdZ17/2mVmHewPZeZxwrIrE73uzZUBAAAAgDtxHpF+TC+JHr6mkT//70t+0c9G5mOpnyf1CGk+II/5dWM6ZvraRX+mMi8nEq+ss9rNmH45cUSM5lR3+gEA4NM9pHd39z/ndOWshtnDGxcwu1aXfdF/fMpd+mQDiU8z/Xx7DA9TUcwzfvgaj0RpCx+wNSJ59gPT/DO6UJczP+hqZ/uZzYnEypirvWzDP1VI5fevr11mBgDglsYth7nBOM2ZvTmrYVbvNNmcLrVQ57/0heTXv0t/XLAlIKLfWb4x1bTEtKi49qB5s6d0pP6zU2JcMR1mnp2+fKnM03HG+HEE0UPUj3PWaa+Qatl/7VrdNgAAt3TB7z698Tih9PmXb6y7TJXdufX1k1/wLv3t4u0BfZ97k6ceS80ky0fOfolO2uwjcKroOPhL5qjQQxLl+qvtp1r2X7tWtw0AwC1d8LtPbxXeUvroyzfWXaY6f3n7mS94l/528faAPvE8eHGFx9JlZP/J14nU1/pnt2SOboDHzDRP8/vR+Y4zv/6clVkumg7IrjkAAPdzwe++qKUTWm2WaO6jttQ1N1rbeyhUPDPDoS7eHtA0PkJ+kNpj6fihOtUpsYzU1/pnt2RezvgYHs9fLuncPxtX21wZ8aKPk6YyAwDwJZz9RhQzfvM+g69pJ5vfz7KQ2b9fPdoz6I2EuGoME8f9zp34VIzIGVWcvi5bptNnly/HU94/tcaWabOpAOwl3oPLz7eXsOgDTScpf8o5n3vO2X7mwhS1JP7LEcWYYVHMS//iddcDdtYQAIB7EN990+9WcVZ8Oy+zOf08Z9/jIq1Z0amuM4v2xiTjhc7BZfP+jC/JlwHTSHF2eu2W6cxWxVWih1pjy7SpPAC202/A6dnlJ8lz9WnvlF4G6xLnnNVTTK8105oJnYZTbadmT+UvZwYA4JbMb1X/G/bxXy+nyvsW3VL0p+7fyZ+q+/LvaR5/Pf3lemnJ38Y8BtOz0z9Ts/Sn0zHlTrJ9mu0tlw7AmfQbMPpwW34U6A+TaZ5Uq7qr0/58/vrpIGp42vM4u7kaywv91c4e0R1mr/UzAwBwS9HXX2qT8PsrvvZd75x69r7WnS96ZzXEnuf3IiwzmzmXogacDI+/at2mzman05cc2metvY1pAfQt34CPGefyl89bnafWydvPLufSx3WVLRWnMWaq5cqMkbvWHACA24u+/spf0FGYU1SfemYe6Jz+/eq67kvYNED3o9fWb1i8CprZT/9sajqR4Yg+C41li+JOhncz/uPdr8wfTkuif3H5zylzHbKdXOGsM9p4UIyfWqtUnnEoZ3AnwByqnBkAgFtyvh+n35UvB8d/iBLLb3ynW5122X+quq77EjatpfuZ5nTaFoX0pNkZd50d+3RadV6Xfp+FxrJFcSfTdzQeF7vbL9gSAADA7UV7ML03G8/+HBGnysmnx3Xa2t7SKS0ypxZhy9npqf7yZtfWP+v3k+2532ehsWxR3MkDgXe/Mn9csCUAAIDbi/Zg4/HfR8RZfaEummqp3KHglBZtmGHZYNH59FR/eTtru1x5f7pUz0f3aba3TIvbeCDw7lfmjwu2BAAAcHtiD/b71MsG8uWq32fHhM6RZVfjDnaZVvTv112mcv6dSrL8c8w5fS3EsObSpWbJJnemc2KWnZhrXmts2UMqDz7LA4F3vzJ/XLAlAACA2xN7MLF7FNvLMaFzZFm6kLaw+43CdGYd5vQzHvQ7j9KKC8dLlq0+ZkvtLEJzusIs5pjTnP5QUXvR+MsZ8XGiGwzvfmX+uGBLAAAAt6f3YGLr+HL8599jsHMkWz1VyN9nZmvpBlL9jMf9zqdplxcum9E9RJdvny7K6Q/yMo7O6Q81PZu9Fp/rgcC7X5k/LtgSAAAA8IXYmY9YELx422P85b37lfnjgi0BAAAAX4id+YgFwYu3PcZf3rtfmT8u2BIAAADwhdiZv2A1MHrbY/zlvfuV+eOCLQEAAABfiJ05sPS+5/ire/cr88cFWwIAAAAAYPTeZ/kre/cr88cFWwIAAAAAYPRxT9xbfNzUF2wJAAAAAIDRxz1xb/FxU1+wJQAAAAAARh/3xL3Fx019wZYAAADuzdkZRmc7106TnLxZ7ZT4fe1BrZ6/JiL/x71AwAn0x9ddb+CPm/qCLQEAANyYuTmcnupcq/Ps3ayKJJ0Sv689aBN7/h5++Tqe2YzuB7gC/fHl38BOnqOdP/VpLtgSAADAXb1sCKP9oT5euzbKs51O3il9wsa1vKR7K76rGd0PcAW7nridPEc7f+rTXLAlAACAuxq3XtERP9K5dtnGRjp5p/QJG9eoxHGlRebzmzk6OdC364nbyXO086c+zQVbAgAAuKtx6/Vy5OfPZWTq2mUbG+nkndInbFyjEseVFpnPb+bo5EDfriduJ8/Rzp/6NBdsCQAA4K5eNoTj/vDllJOtcK2/A9T72Gn/et87jv9ylSgxvXZZ2qkybU8fj7J1hio3s5wuWpxUP8AVPAy78hzt/KlPc8GWAAAAbszcHDqbtChmea25A9Rb2emp5Wi/jy93y+JsNlJUWa7MeDDbs9lqrRndz7Ki3w9wBdFbLHsDO3mOdv7Up7lgSwAAADdmbg6Xm7T+tXqzOiYRZ/WFOknnT7/V6E9zZZZTjBkKf5rNFPrZsjjAFYj3ReoGdvIc7fypT3PBlgAAAG7s9+5L7MT0Jk1vLJcbvMJm9eVss/MosnPWb9W5VizL3rZTzYiwbM7CtcAViDepfqcU8hzt/KlPc8GWAAAA7mrcekWbMbFJW+4qlxs8fwcotrLR/tbvTV9bOzt2lepwemqasFau2YxozznSXBzgCqI34Hh79/Mc7fypT3PBlgAAAO5q3HpFmzGxSVvu3/oBv8N+gqPmX47r5L/P6tVInRWt+msuTo0JR/2hls2Mq+3k3LU4wBVEb8DxzdjPc7Tzpz7N0UsHAABwV+Wtlz5SO56KqSURV/0+pZPryNrZTh7RXn/AI5pxMhy0OMAVPAy78lznvfBBrf5jLi8AAABelLde+og4btZdhjl5dKsvZ8UpkTZVQpzt5FlOfVzbtWbG2++0foAreBh25bnOe+GDWv3ngi0BAADc1cuGUOwPp8dT++dOwBgz7XwarJPryGXa6bWpVnWH0bLXGssO5TSzLCHKZWcBLuhh2JXnOu+FD2r1nwu2BAAAcGPm5nA85e8tlxs8Z7O6LOcc171FM+o+ozxO5HJ9olTTDGL82lBirfTxZT/ZgGk/wBVEt3T2BnbyXOe98EGt/nPBlgAAAO7N2RmOZ/295XKDZ25WX46IlqL8urcoYZRKXCtadaosV0ZHOiM4Q01LmKdEt2JxUv0AVxC9SfUbtpbnOu+FD2r1nwu2BAAAAADAiN8ZLt7qPxdsCQAAAACAEb8zXLzVfy7YEgAAAAAAI35nuHir/1ywJQAAAAAARvzOcPFW/7lgSwAAAAAAjPid4eKt/iNa2tL/70uuM74/iw57+0Qn32MbS7x96QAAAAB8nF1P3E6e6zywfFCr/4iWtozwO/464/uD6LC3T3Tabba9xNuXDgAAAMDH0c/a/lOGk+c6Dywf1Oo/oqXpqewUVx7Z6U3HvH26La+RWeUlYbPE25cOAAAAwMfZ9cTt5LnOA8sHtfqPaCk6lZriyiM7vemYt0+35TWqVWnez29fOgAA3qizOTziO1TvKGpnAeAI6+dtfme4ANFS7VQ58jQ/LTm96Zi3Txc1sLExcd92qrx96QAAeJfmFvEleMv36bQB3eHFt7gA7ir6CM1+HDl5jvOuqU8jWkqdigb8feTn39PMLweXCcWS6nXWbTz/PlmLSc3p9EtfO7WcVL9AY3upVMt+mgvrlwMA4ONMv+zKX39bvjdFSy/7nOkWYlcbAOB4GHblOc67pj6NaMk/JWb8/efPv6eZx0iR8PdxUdHsfAwwq4uel4Okxowmmp5KDbWsIqpPI/1hC/MCAPDp+l+4/avGDNEXd3TEiQeAI0wfKwqPEk6e47xr6tOIlvxTv/80T4mwVKQT4Ae/vEaFP0W5jSOPDb8QI+taUQkR4I8QLV22JQAAPlf0NVf7+mt+af5c7uTRX9Z8fQM4R/QEFD0NdfIc511Tn0a0ZJ4aw6Kz4qpawmWq5VDLazc2tow3RxgjU7fZS8CyltnM0UsHAMA9ON9x5vdm9NXv7Ad+Is2ulvsHvrsBnEM8AZkffX6e47xr6tOIlsxTekzn3+WE4+XLFRZ1p5fr0s7ZaJCn3AaIU2Kcl2t1MyLDsoQTll06sVAAANzJ8svO/FYV3+zZ79NlMy8BukMAOE707FD73HuXd019GtGSeUqPqf/9+PstmU049qNXePmijJf3z0a1xhjRrZ5oefwl1XI9nRLLPNmzeqEAALgTZ3sQHel8/+p+dKt+hwBwnOjZIfsc4eQ5zrumPo1oKTr1MogeSkT+/Bkd97uKUkWdRy/KePmus0uFF0Kc6rQ9TbW9SqoHAABuabobeWa+N1MXLjvxA5rbHgAoexh25TnOu6Y+jWgpOvVyfAz7fST69+8/ywnHGL280wCdfNfZZbx5ypmo2bZZpfmqZZcOAIAbe9nG+N+b06/jkdmAH8N3N4B3iT7oUh96Zp7jvGvq04iWpqemU/w+8hIQ/fv3n+WEy8b8iZzShcb8ePOUP5HOtlxPkXB6RI8wPesvOwAAt5f9Gp1GPgKp6v0OAeA40Qdd6kPPzHOcd019GtGSP4II+P3neG0zoZPKn1RMrddE5NdLVzul85s967bNQp1hl00uWwIA4BNFX3P62zk6O/0+3dhVrUMAOM70saLwKOHkuc7H2ge1+o9oKdV/FPD7SHR2mtNJaA6yDHg59buu7llPpwcZs/mnpslFoZfjTtvLWssYPZFYWLMfAAA+kf6WjGLM73F9Yaorv4dUIQBomjz8DHbluc7H2ge1+s8FW6q5zSAAAODelg/pL5vG6Z9OKn/zGeURqXQbAHCQXU/cTp7rfLJ9UKv/XLClmtsMAgAA7m25RdQB47+jI/7uaBqpU118iwvgrqJPyNrn3pZUJ/igVv+5YEtZ11xYAAAAQW8OX87+Dnu5ZExS2HaabaTOAsARdj1xO3mu8+H2Qa3+c8GWsm4wAgAAAABgid8ZLt7qPxdsCQAAAACAEb8zXLzVfy7YEgAAAAAAI35nuHir/1ywJQAAAAAARvzOcPFW/7lgSwAAAAAAjPid4eKt/nPBlgAAAAAAGPE7w8Vb/eeCLQEAAAAAMOJ3hou3+s8FW9piXPBxUj179MLpIzpyrKtvD7+WP2OnYqcHndM/PuZ5DpzIWq1dU0Q9O3Ppef2e/YpRpH9VZx2cHjorpvP4x51uaxmc9fEjO73pKp0M/XXQXY05syupJ9IZAADARtHXevbr2MlznW/2D2r1nwu2tMW44OOkevbohdNHdORYV98efi1/xk7FTg86p398zPMcOJG1WrumiHp25tLz+j37FaNI/6rOOjg9dFZM5/GPO93WMjjr40d2etNVOhn666C7GnNmV1JPpDMAU8v7/O38xraPMF2W6I28rC7ClnkOuhZAh/408N90Tp7rvH8/qNV/LtjSFuOCj5Pq2aMXTh/RkWNdfXv4tfwZOxU7Peic/vExz3PgRNZq7Zoi6tmZS8/r9+xXjCL9qzrr4PTQWTGdxz/udFvL4KyPH9npTVfpZOivg+5qzJldST2RzgCM9D3ZTy7+TOUxr91+808XJFo0XV1HvussgA79aeC/45w813nzflCr/1ywpS3GBR8n1bNHL5w+oiPHuvr28Gv5M3YqdnrQOf3jY57nwIms1do1RdSzM5ee1+/ZrxhF+ld11sHpobNiOo9/3Om2lsFZHz+y05uu0snQXwfd1Zgzu5J6Ip0BeDG9W3bdPy95Omn9a/fe/D/ZzLQiLHqzO9c+g7d5+drlIABM41dz9GXdz3OdN+8HtfrPBVvaYlzwcVI9e/TC6SM6cqyrbw+/lj9jp2KnB53TPz7meQ6cyFqtXVNEPTtz6Xn9nv2KUaR/VWcdnB46K6bz+MedbmsZnPXxIzu96SqdDP110F2NObMrqSfSGYAX2TdUJ3knp3/t3pv/J5uTVscsPx/E5YdeC6Bj/qU++7Lu5znHmVOf5oItbTEu+Dipnj164fQRHTnW1beHX8ufsVOx04PO6R8f8zwHTmSt1q4pop6dufS8fs9+xSjSv6qzDk4PnRXTefzjTre1DM76+JGd3nSVTob+OuiuxpzZldQT6QzAi+wbqpO8k9O/du/N/5NtmbYQoD89zrkWQNP8S332Zd3Pc44zpz7NBVvaYlzwcVI9e/TC6SM6cqyrbw+/lj9jp2KnB53TPz7meQ6cyFqtXVNEPTtz6Xn9nv2KUaR/VWcdnB46K6bz+MedbmsZnPXxIzu96SqdDP110F2NObMrqSfSGYAX/q1i3pnTg+Kt5KSd9qkTLtsziQaiSB0wvlWnhZat+tc6RwCUhR9zyU8eJ885zpz6NBdsaYtxwcdJ9ezRC6eP6Mixrr49/Fr+jJ2KnR50Tv/4mOc5cCJrtXZNEfXszKXn9Xv2K0aR/lWddXB66KyYzuMfd7qtZXDWx4/s9KardDL010F3NebMrqSeSGcARs49I94Izs3sx4i05tllZHZloj9FpE5YmFdXdNaq0C0Ax/QNWPjYcfKc48ypT3PBlrYYF3ycVM8evXD6iI4c6+rbw6/lz9ip2OlB5/SPj3meAyeyVmvXFFHPzlx6Xr9nv2IU6V/VWQenh86K6Tz+cafbWgZnffzITm+6SidDfx10V2PO7ErqiXQGYErf6vodZJ5K/Rm9L6YX+j1k3xe64eXxKHK6znpkp7HacgFoGt/X4p3ezHOOM6c+zQVb2mJc8HFSPXv0wukjOnKsq28Pv5Y/Y6dipwed0z8+5nkOnMharV1TRD07c+l5/Z79ilGkf1VnHZweOium8/jHnW5rGZz18SM7vekqnQz9ddBdjTmzK6kn0hkAIbrbxZ/6nvfzTDvRR8a2s+0tmQ37OXVvZlrnDb5xEQBoD8OuPOc4c+rTXLClLcYFHyfVs0cvnD6iI8e6+vbwa/kzdip2etA5/eNjnufAiazV2jVF1LMzl57X79mvGEX6V3XWwemhs2I6j3/c6baWwVkfP7LTm67SydBfB93VmDO7knoinQFwmG+Z6SlxoX47+GnHa523Q+Gtsew/OuhkW14ryi0r6pcvtQgAtOhzJvuZ4+Q5x5lTn+aCLW0xLvg4qZ49euH0ER051tW3h1/Ln7FTsdODzukfH/M8B05krdauKaKenbn0vH7PfsUo0r+qsw5OD50V03n84063tQzO+viRnd50lU6G/jrorsac2ZXUE+kMgMl5y0xPiQuneaZ37PKt9HKV83bIvjWia5cT6YT6yPKsWVG/fKm2AWjis2LXZ87Jzpz6NBdsaYtxwcdJ9ezRC6eP6Mixrr49/Fr+jJ2KnR50Tv/4mOc5cCJrtXZNEfXszKXn9Xv2K0aR/lWddXB66KyYzuMfd7qtZXDWx4/s9KardDL010F3NebMrqSeSGcAXtRubHG5uFDflv5byT/beSNEb1V/omlCfaQTH0V28gBYEp8V44dGM885zpz6NBdsaYtxwcdJ9ezRC6eP6Mixrr49/Fr+jJ2KnR50Tv/4mOc5cCJrtXZNEfXszKXn9Xv2K0aR/lWddXB66KyYzuMfd7qtZXDWx4/s9KardDL010F3NebMrqSeSGcAXhRubP+eFxduTCvO6khtGqmTZxOmWhXlmpkBdDwMu/Kc48ypT3PBlrYYF3ycVM8evXD6iI4c6+rbw6/lz9ip2OlB5/SPj3meAyeyVmvXFFHPzlx6Xr9nv2IU6V/VWQenh86K6Tz+cafbWgZnffzITm+6SidDfx10V2PO7ErqiXQGYLS8b/U7yDzV+dNpwG/PfGtMI38OFt5i0z7NGf8dSWWOZil0DkB4GDamOtr5U5/jgi1tMS74OKmePXrh9BEdOdbVt4dfy5+xU7HTg87pHx/zPAdOZK3Wriminp259Lx+z37FKNK/qrMOTg+dFdN5/ONOt7UMzvr4kZ3edJVOhv466K7GnNmV1BPpDMBI35PTGH359NT4ZzZtdNX0rN+eWJPUckWDmGvlr6QZ4NcF0NH5TPhcHzf1BVvaYlzw6CvAyTDmiY7oyLGuvj38Wv6MnYqdHnRO//iY5zlwImu1dk0R9ezMpef1e/YrRpH+VZ11cHrorJjO4x93uq1lcNbHj+z0pqt0MvTXQXc15syupJ5IZwAiyzvHvDP1VWOYk/Y5vJWWbwGnveWw4pQ2vVA347SqC9UyA2hqfiZ8qI+b+oItbTEuePTt4GQY80RHdORYV98efi1/xk7FTg86p398zPMcOJG1WrumiHp25tLz+j37FaNI/6rOOjg9dFZM5/GPO93WMjjr40d2etNVOhn666C7GnNmV1JPpDMA+I23CYCm6Gv93l/HHzf1BVvaQu8JoyNRBr2f9CPHuvr28Gv5M3YqdnrQOf3jY57nwIms1do1RdSzM5ee1+/ZrxhF+ld11sHpobNiOo9/3Om2lsFZHz+y05uu0snQXwfd1Zgzu5J6Ip0BwG+8TQA0RV/r9/46/ripL9jSFnpPGB2JMuj9pB851tW3h1/Ln7FTsdODzukfH/M8B05krdauKaKenbn0vH7PfsUo0r+qsw5OD50V03n84063tQzO+viRnd50lU6G/jrorsac2ZXUE+kMAH7wHgHQF32t3/vr+OOmvmBLW+g9YXQkyqD3k37kWFffHn4tf8ZOxU4POqd/fMzzHDiRtVq7poh6dubS8/o9+xWjSP+qzjo4PXRWTOfxjzvd1jI46+NHdnrTVToZ+uuguxpzZldST6QzAACAjaKvdbz7lfnjgi1tMS74OKmePXrh9BEdOdbVt4dfy5+xU7HTg87pHx/zPAdOZK3Wriminp259Lx+z37FKNK/qrMOTg+dFdN5/ONOt7UMzvr4kZ3edJVOhv466K7GnNmV1BPpDAAAYKPoax3vfmX+uGBLW4wLPk6qZ49eOH1ER4519e3h1/Jn7FTs9KBz+sfHPM+BE1mrtWuKqGdnLj2v37NfMYr0r+qsg9NDZ8V0Hv+4020tg7M+fmSnN12lk6G/DrqrMWd2JfVEOgMAANgo+lrHu1+ZPy7Y0hbjgo+T6tmjF04f0ZFjXX17+LX8GTsVOz3onP7xMc9z4ETWau2aIurZmUvP6/fsV4wi/as66+D00Fkxncc/7nRby+Csjx/Z6U1X6WTor4PuasyZXUk9kc4AAAA2ir7W8e5X5o8LtrTFuODjpHr26IXTR3TkWFffHn4tf8ZOxU4POqd/fMzzHDiRtVq7poh6dubS8/o9+xWjSP+qzjo4PXRWTOfxjzvd1jI46+NHdnrTVToZ+uuguxpzZldST6QzAACAjaKvdbz7lfnjgi1tMS74OKmePXrh9BEdOdbVt4dfy5+xU7HTg87pHx/zPAdOZK3Wriminp259Lx+z37FKNK/qrMOTg+dFdN5/ONOt7UMzvr4kZ3edJVOhv466K7GnNmV1BPpDAAAYKPoax3vfmX+uGBLW4wLPk6qZ49eOH1ER4519e3h1/Jn7FTs9KBz+sfHPM+BE1mrtWuKqGdnLj2v37NfMYr0r+qsg9NDZ8V0Hv+4020tg7M+fmSnN12lk6G/DrqrMWd2JfVEOgMAANgo+lrHu1+ZPy7Y0hbjgo+T6tmjF04f0ZFjXX17+LX8GTsVOz3onP7xMc9z4ETWau2aIurZmUvP6/fsV4wi/as66+D00Fkxncc/7nRby+Csjx/Z6U1X6WTor4PuasyZXUk9kc4AAAA2ir7W8e5X5o8LtrTFuODjpHr26IXTR3TkWFffHn4tf8ZOxU4POqd/fMzzHDiRtVq7poh6dubS8/o9+xWjSP+qzjo4PXRWTOfxjzvd1jI46+NHdnrTVToZ+uuguxpzZldST6QzAACAjaKvdbz7lfnjgi1tMS74OKmePXrh9BEdOdbVt4dfy5+xU7HTg87pHx/zPAdOZK3Wriminp259Lx+z37FKNK/qrMOTg+dFdN5/ONOt7UMzvr4kZ3edJVOhv466K7GnNmV1BPpDAAAYKPoax3vfmX+uGBLW4wLPk6qZ49eOH1ER4519e3h1/Jn7FTs9KBz+sfHPM+BE1mrtWuKqGdnLj2v37NfMYr0r+qsg9NDZ8V0Hv+4020tg7M+fmSnN12lk6G/DrqrMWd2JfVEOgMAANgo+lrHu1+ZPy7Y0hbjgo+T6tmjF04f0ZFjXX17+LX8GTsVOz3onP7xMc9z4ETWau2aIurZmUvP6/fsV4wi/as66+D00Fkxncc/7nRby+Csjx/Z6U1X6WTor4PuasyZXUk9kc4AAAA2ir7W8e5X5o8LtrTFuODjpHr26IXTR3TkWFffHn4tf8ZOxU4POqd/fMzzHDiRtVq7poh6dubS8/o9+xWjSP+qzjo4PXRWTOfxjzvd1jI46+NHdnrTVToZ+uuguxpzZldST6QzAAAAfJu77oucXa6e3d+RZveuzrXZWv6MnYqdHnRO//iY5zlwImu1dk0R9ezMpef1e/YrRpH+VZ11cHrorJjO4x93uq1lcNbHj+z0pqt0MvTXQXc15syupJ5IZwAAAPg2d90XObtcPbu/I83uXZ1rs7X8GTsVOz3onP7xMc9z4ETWau2aIurZmUvP6/fsV4wi/as66+D00Fkxncc/7nRby+Csjx/Z6U1X6WTor4PuasyZXUk9kc4AAADwbe66L3J2uXp2f0ea3bs612Zr+TN2KnZ60Dn942Oe58CJrNXaNUXUszOXntfv2a8YRfpXddbB6aGzYjqPf9zptpbBWR8/stObrtLJ0F8H3dWYM7uSeiKdAQAA4NvcdV/k7HL17P6ONLt3da7N1vJn7FTs9KBz+sfHPM+BE1mrtWuKqGdnLj2v37NfMYr0r+qsg9NDZ8V0Hv+4020tg7M+fmSnN12lk6G/DrqrMWd2JfVEOgMAAMC3ueu+yNnl6tn9HWl27+pcm63lz9ip2OlB5/SPj3meAyeyVmvXFFHPzlx6Xr9nv2IU6V/VWQenh86K6Tz+cafbWgZnffzITm+6SidDfx10V2PO7ErqiXQGAACAb3PXfZGzy9Wz+zvS7N7VuTZby5+xU7HTg87pHx/zPAdOZK3Wriminp259Lx+z37FKNK/qrMOTg+dFdN5/ONOt7UMzvr4kZ3edJVOhv466K7GnNmV1BPpDAAAAN/mrvsiZ5erZ/d3pNm9q3NttpY/Y6dipwed0z8+5nkOnMharV1TRD07c+l5/Z79ilGkf1VnHZweOium8/jHnW5rGZz18SM7vekqnQz9ddBdjTmzK6kn0hkAAAC+zV33Rc4uV8/u70ize1fn2mwtf8ZOxU4POqd/fMzzHDiRtVq7poh6dubS8/o9+xWjSP+qzjo4PXRWTOfxjzvd1jI46+NHdnrTVToZ+uuguxpzZldST6QzAAAAfJu77oucXa6e3d+RZveuzrXZWv6MnYqdHnRO//iY5zlwImu1dk0R9ezMpef1e/YrRpH+VZ11cHrorJjO4x93uq1lcNbHj+z0pqt0MvTXQXc15syupJ5IZwAAAPg2d90XObtcPbu/I83uXZ1rs7X8GTsVOz3onP7xMc9z4ETWau2aIurZmUvP6/fsV4wi/as66+D00Fkxncc/7nRby+Csjx/Z6U1X6WTor4PuasyZXUk9kc4AAADwbe66L3J2uXp2f0ea3bs612Zr+TN2KnZ60Dn942Oe58CJrNXaNUXUszOXntfv2a8YRfpXddbB6aGzYjqPf9zptpbBWR8/stObrtLJ0F8H3dWYM7uSeiKdAQAA4NvcdV/k7HL17P6ONLt3da7N1vJn7FTs9KBz+sfHPM+BE1mrtWuKqGdnLj2v37NfMYr0r+qsg9NDZ8V0Hv+4020tg7M+fmSnN12lk6G/DrqrMWd2JfVEOgMAAMC3ueu+yNnl6tn9HWl27+pcm63lz9ip2OlB5/SPj3meAyeyVmvXFFHPzlx6Xr9nv2IU6V/VWQenh86K6Tz+cafbWgZnffzITm+6SidDfx10V2PO7ErqiXQGAABwnPHLN/o6nkZOv7711/px+afHo63ICfk77rov0nvC6EiUYcwTHdGRY139svq1/Bk7FTs96Jz+8THPc+BE1mrtmiLq2ZlLz+v37FeMIv2rOuvg9NBZMZ3HP+50W8vgrI8f2elNV+lk6K+D7mrMmV1JPZHOAAAAjjN++UZfx+P2IPXvo/Onejghf9Nd90V6TxgdiTLo/WR27+pcm63lz9ip2OlB5/SPj3meAyeyVmvXFFHPzlx6Xr9nv2IU6V/VWQenh86K6Tz+cafbWgZnffzITm+6SidDfx10V2PO7ErqiXQGAABwnOU2YHp8jPl3JDp+dH5RNyp3aP6+u+6L9J4wOhJl0PtJP3KsG12breXP2KnY6UHn9I+PeZ4DJ7JWa9cUUc/OXHpev2e/YhTpX9VZB6eHzorpPP5xp9taBmd9/MhOb7pKJ0N/HXRXY87sSuqJdAYAAHCc5TZgenyMeVzyd4Zoa3F0/r677ov0njA6EmXQ+0k/cqwbXZut5c/YqdjpQef0j495ngMnslZr1xRRz85cel6/Z79iFOlf1VkHp4fOiuk8/nGn21oGZ338yE5vukonQ38ddFdjzuxK6ol0BgAAcJzfX76PxjO1vjabX+SZhumc4zZD9+kfj/L33XVfpPeE0ZEog95P+pFj3ejabC1/xk7FTg86p398zPMcOJG1WrumiHp25tLz+j37FaNI/6rOOjg9dFZM5/GPO93WMjjr40d2etNVOhn666C7GnNmV1JPpDMAAIDjjF++0dex+Jr+/Z2+Jb++atw2RPFRmNnVstVlG2V33RfpPWF0JMqg95N+5Fg3ujZby5+xU7HTg87pHx/zPAdOZK3Wriminp259Lx+z37FKNK/qrMOTg+dFdN5/ONOt7UMzvr4kZ3edJVOhv466K7GnNmV1BPpDAAA4Djjl2/0dewcd7I180zDslV29Zk967vrvkjvCaMjUQa9n8zuXZ1rs7X8GTsVOz3onP7xMc9z4ETWau2aIurZmUvP6/fsV4wi/as66+D00Fkxncc/7nRby+Csjx/Z6U1X6WTor4PuasyZXUk9kc4AAACOM375Rl/H0Rd9NpuTP7oqezyqsoxPLUIqwHTXfZHeE0ZHogx6P5nduzrXZmv5M3YqdnrQOf3jY57nwIms1do1RdSzM5ee1+/ZrxhF+ld11sHpobNiOo9/3Om2lsFZHz+y05uu0snQXwfd1Zgzu5J6Ip0BAADs9fsLd/zyjb6OncjaEb+r7PGoio73V6AW47jrvkjvCaMjUQa9n8zuXZ1rs7X8GTsVOz3onP7xMc9z4ETWau2aIurZmUvP6/fsV4wi/as66+D00Fkxncc/7nRby+Csjx/Z6U1X6WTor4PuasyZXUk9kc4AAAD2+v2FO375Rl/H4/ZgGVPOH1119HGzvWWffXfdF+k9YXQkyqD3k9m9q3NttpY/Y6dipwed0z8+5nkOnMharV1TRD07c+l5/Z79ilGkf1VnHZweOium8/jHnW5rGZz18SM7vekqnQz9ddBdjTmzK6kn0hkAAMBev79wxy/f6OtYX+WHOflFntS/sznN3pyem+66L9J7wuhIlEHvJ7N7V+fabC1/xk7FTg86p398zPMcOJG1WrumiHp25tLz+j37FaNI/6rOOjg9dFZM5/GPO93WMjjr40d2etNVOhn666C7GnNmV1JPpDMAAIDt9Hd3dMl4+fRLXH+t+8ejPKnjYpshOn+5Ssx70DbmrvuiaAHHGCeDfiGyL5lzbbaWP2OnYqcHndM/PuZ5DpzIWq1dU0Q9O3Ppef2e/YpRpH9VZx2cHjorpvP4x51uaxmc9fEjO73pKp0M/XXQXY05syupJ9IZAAAAvs1d90XOLlfP7u9Is3tX59psLX/GTsVODzqnf3zM8xw4kbVau6aIenbm0vP6PfsVo0j/qs46OD10Vkzn8Y873dYyOOvjR3Z601U6GfrroLsac2ZXUk+kMwAAAHybu+6LnF2unt3fkWb3rs612Vr+jJ2KnR50Tv/4mOc5cCJrtXZNEfXszKXn9Xv2K0aR/lWddXB66KyYzuMfd7qtZXDWx4/s9KardDL010F3NebMrqSeSGcAAAD4NnfdFzm7XD27vyPN7l2da7O1/Bk7FTs96Jz+8THPc+BE1mrtmiLq2ZlLz+v37FeMIv2rOuvg9NBZMZ3HP+50W8vgrI8f2elNV+lk6K+D7mrMmV1JPZHOAAAA8G3uui9ydrl6dn9Hmt27Otdma/kzdip2etA5/eNjnufAiazV2jVF1LMzl57X79mvGEX6V3XWwemhs2I6j3/c6baWwVkfP7LTm67SydBfB93VmDO7knoinQH3Ft1dAADg3d/S+43TjZPq2aNV0kd05FhXvxZ+LX/GTsVODzqnf3zM8xw4kbVau6aIenbm0vP6PfsVo0j/qs46OD10Vkzn8Y873dYyOOvjR3Z601U6GfrroLsac2ZXUk+kM+DeorsLAAC8+1t6v3G6cVI9e7RK+oiOHOvq18Kv5c/YqdjpQef0j495ngMnslZr1xRRz85cel6/Z79iFOlf1VkHp4fOiuk8/nGn21oGZ338yE5vukonQ38ddFdjzuxK6ol0BgAAgG9z132Rs8vVs/s70uze1bk2W8ufsVOx04PO6R8f8zwHTmSt1q4pop6dufS8fs9+xSjSv6qzDk4PnRXTefzjTre1DM76+JGd3nSVTob+OuiuxpzZldQT6QwAAADf5q77ImeXq2f3d6TZvatzbbaWP2OnYqcHndM/PuZ5DpzIWq1dU0Q9O3Ppef2e/YpRpH9VZx2cHjorpvP4x51uaxmc9fEjO73pKp0M/XXQXY05syupJ9IZAAAAvs1d90XOLlfP7u9Is3tX59psLX/GTsVODzqnf3zM8xw4kbVau6aIenbm0vP6PfsVo0j/qs46OD10Vkzn8Y873dYyOOvjR3Z601U6GfrroLsac2ZXUk+kMwAAAHybu+6LnF2unt3fkWb3rs612Vr+jJ2KnR50Tv/4mOc5cCJrtXZNEfXszKXn9Xv2K0aR/lWddXB66KyYzuMfd7qtZXDWx4/s9KardDL010F3NebMrqSeSGcAAAD4NnfdFzm7XD27vyPN7l2da7O1/Bk7FTs96Jz+8THPc+BE1mrtmiLq2ZlLz+v37FeMIv2rOuvg9NBZMZ3HP+50W8vgrI8f2elNV+lk6K+D7mrMmV1JPZHOAAAA8G3uui9ydrl6dn9Hmt27Otdma/kzdip2etA5/eNjnufAiazV2jVF1LMzl57X79mvGEX6V3XWwemhs2I6j3/c6baWwVkfP7LTm67SydBfB93VmDO7knoinQEAAODbsC8CAAAAAAC78DsDAAAAAADYhd8ZAAAAAADALvzOAAAAcDL9v0NiHhf/YyPO/2xIFL+svn3r+JIz1fxynDG/08yj+j/zYq6SDhZnt3cCCNEdFd2x0z9F2k4zfqvLxmrvo1TFZbb7+ZIxAQAALmK57cxuhnXaaKenN8nTfo7YJy9r6ebNJs2GaytQWB8dLM5u7wQQojsqui2nf4q0/U5SrUZvChEWlStU1Nnu5xtmBAAAuAixER1jomudP6e1ll2lGt6yh0xN5DcwTVXuRCdPnXX68de23wkgTG8h8ZkgrlqmddoQJfz3nZjImbpW0WnjZm4/IAAAwHWUd7nLMHNv7HfVT7tUmOjp7dJfzjoNO6WnyVNnd2XYmweYWr4HnT/9tKn4bNHn348O8fnglMtWNI/fye0HBAAAuI7OhlmHmXtjvyt9+ZZ9cmGi59+HBXOcZbf+ONnSUUzt7N5OAM38ZDDfucu0qfhs0effjw7x+VCb2u/cvPbT3X5AAACA65jucqMYfdzZ9Jq72cJmeLlXdxR2+4+//HFqa56NdPL8BOg+l6n6nQCa+cngvHOdtMv4Zs7HINV/dkw/1S3dfkAAAIBLmW50pwH6uLPpNXezhc3weCq7cxYNj//+HbCsVdjYPwa67drZqNZ0GZfN9DsBNPEmFe99802Uuj+X7wgnZ/TOMvvPjhnFLN/a93D7AQEAAC5l3OuKXagIXm56/d2sua82T5mWRaNTtcZ0w2KdC0mmZ8cpnrNh9dm9nQDa9H0x3lrTe1jcfoX70+9BRP7+M/q3aC8Kcyou+7+fb5gRAADgasSeU29No81wZzcbRYoM/a3ysmh0qtaYblivbS2JqZbziE6AFw9pDJv+KdL2+0m1+vvP6N+ivSjMqRi1fWPfMykAAMAF6c3wNFL/WdjQmhXNU6ZphnGil7PlxnTDtZcgdTZSy3lEJ8AL8SYV75fl7de/P5c9OHV//l17+5crfskb83smBQAAeDu9bxcxy7DODtasaJ4qF3UGLDemG669BM7Zxy/+VXvHAWrM2898+yzTpuKzRcUlZqp+RfOqe/iSMQEAAK6gs2HWYZ3tq1nRPFUuag446jfsLGYqw3g8e+H2ToCs5V3t/OmnTcVni05jHn/pVFsqmhfewDfMCAAAcBGdDbMO6+xdxbXH7ZPFnl//OSoPYpYuZPAHT/25txNAi+6lwk3rpE3FZ4tOYx5/+eXKFc0Lb8D5jJquf7aEc7M5XQEAAHy0aE+73DDrsM7eVVwr2mvu1sYk/kTmEpmnfgeIYJ3BX5PpmObZvZ0Amn4jRO8X872WukWjEn7R8jjRwXJF89pP53xGjbL5zZvN6QoAAOCjLfdX5tbU2RtnW6o1XG5gjDe3nYUelr01B0yNr2ud2QkgjLfi9LZ8ObK8xMlpdpJqdVql3F65ojh+J2LALWtiBjsvBwAAwG0429foEufPWjNmw+UkziXLQqJW9ngUNo3cO75Z64ROgMgjNg0zrzLTLvsxWx3rRml1Kn9xlhX1qXsQ0+k16eePYu694AAAAPjntJ327bf0AHAp/M4AAACAdznzd4ajqwAA/tn1O8P4X4k85X/ZMp6d6g0HAACAS+N3BgC4n+zvDOYvBtPjy6v4nQEAAODbHL3rY1cJACdb/s7gPP6Pvy04+aMYvgsAAAAAAPhQqd8ZzAziZ4fl5fzIAAAAAADA5yr/gPByMPpRovbfS+hfNgAAAAAAwDUVfmeIDvI7AwAAAAAAXy71O8P0oP5BYPlzwUsAPy8AAAAAAPC5ar8z6F8GzP+eYRrA7wwAAAAAAHyu7O8M0+PjDwvm7wz8yAAAAAAAwJ1s/J1h+r+rwO8MAAAAAAB8j8LvDNNT0x8ZdJLxLL8zAACAG4v+/2VqYcsL+9uqMWGzH63Zrai+N/O7XG2i7M1spjKzpe6obHx2/ChDv8oRLtUM7oobDAAA4DTODr/2FLDl0WmZLZV2mSTK2e9/V5JliYMyR+WOnijFuSXMns0bbMsdZV6SHX96ebPEQS7VDO6KGwwAAOA0yyeO2iPJrkcnJ5uf1swzJuw0vzfJMv8RmXXF8+tGnPvB6dm/u7bcUf5VqfGn13byH+dSzeCuuMEAAADOpDf5u553Oo82TkInp5lnTFjuPKpeTnJy5mvWjTi3hNOzvhmWkULtwvL447Wd/Me5VDO4K24wAACAM4mHjv7zTjPV8sLmE5N57ZbnoOMept71mPauupHHQMf4efwBd91Rheov8dMLX85e5LW7VDO4K24wAACAk0X7/Nr+/5xszbT9a69T612PaVd7PHzMiJhlEic+m98Pa5YW4xeSH+pSzeCuuMEAAABONn00089r2WxPfmc4rNa7HtOu9nj4mBExyyROfDa/H9YsPV7bmetQl2oGd8UNBgAAcL5xq9/Z/G/MdugzyK6nwsfAT6IvTJ01S+xaSaecM8WuVrNL4QyV7aQwZjPVsvMxwEku1lB0WFt2P8+WPv3kuBNecQAAgPM9pDdma3biJy+HmZNOj+vplmmXpUXAdJYUc1K/dLPVqK7Ts9l8eU0KYdvHj+bSqUSkXqtHfBNm80QrUOjTSYtbWt5I76q+PTn3NgAAuJS9W/GN2Y57QDAT1h5hHsYTbi2zH6PbG4umOJP6pft9vsRPL9c5C5eYa5INy84+XiIG0flfl74UqdXyHNGnuba4AfGKn3AzHFriJTn3NgAAuJS9W/GNW/rjHhPMVFHY9HjU4XhQVDcz+MedDrM21t3S50twag3NNsprIsKWlkWj0tNBdJ+pFZj2GfUvhmpeku1zGoPbEy/6p98P03fNe1sCAAD4bddW/IhdffSw0MlsJnGeX8bjOokuHZ0qtOHnyXLqNltNNTkmGXOay+5f4mTQYYI5eFR6OojuMyqtgzcez16S7TO7qrgN8ep/9I0h3uYAAAAX8RhsSbJrh789s5lhGpat/hIvrhWZU51sXPmpQt0tK+n34xwpt232sAzTtoz/HO63aYnsCkTxu44v52r2ia8iboCPvjemd/sb+wEAAJhq7skff02PPIennmaVLd1mw7Klpz1PL8+eiuK3LJE50RH9b+nn5cjGZfd70GFL5fF//+msQ3YFovhdx5dzNfvEVxE3wHh3PeMbT6fVN/l4xLzndUwkuhYAAOB8zV3Kcp//mD31bOm2kM28djlUoVVxuYjMdmJWrIkSmv048f1+Xg4W1iq1dGbkMixVNMo5HVw0kF2BqMNdx5dzNfvEVxE3gHN3RRn0tVH1KEwn0WHmhQAAAG/R2aX4G6eNG6EjGnbCsnX9DaGOTHXiFy1w+ncu2dXSsh+z56VCD4Ww7LKM8dPBRfLs+FGHu44v52r2ia8iboDp3SX+jHKakX5O56bVLQEAAFxEZ08url0+C5T7OajhZVi27jh4lKGT2axeXjGnrsi/ZSU7/WRjsou2MSy7LGJtn8HDiHOwMMWu49HZXX3iq4gbYLy7zLO1C8tJlkNxkwMAgMvq7Mn954WNzwjHNazDtgxyXGYnuLZoy7oi+ZZ5s/08Z1OLnpdqPWTDsssi1vYZPI84BwtT7Doend3VJ76KuAHGu8s8W7tw2cnIj+RuBwAA19TZpWT3Rdmcy7R7G9Zh4lpx0EmynLc2RfMSP8lyZZorme3nGezJzWvNgO1h2ddoubbLNdcV9aoecTw6u6tPfBVxA4x3lz77+PXbXepCs5ORH8ndDgAArqmzSxGbnM5GyE/babgQljq+PLgMTmXW03UW7Yi6hVR+P+NZcwFT+femilrN5syOnDq+JYke9tA+8VXEDfD7VHRTjX9Gx5dpzU4KQ3GTAwCAy2ruyR8lW3IeN2wUNj0edbU3Sba9TodblmXjImT7mWYrDO6EFVItLQcXpbMjL+OdYTvHf58V67ClT3wVcQOMd5248OfP6PgyrbhwWV0PxU0OAAAuq7knH54Y1nalbXZbC/Nb6hz3M78E6CRmJ9llEXnMU7rPbD9RCf9CP6yQamk1tyod5RH5/WaOOO4vQr9PfBVxA/w+Fd0/4586Mrrq+euGzEbqibjDAQDAlfX35M7+v1DFSdtptRbmt5Q9lRpWxGzpMLUs/UVYzpvqJyohDvqXZ3uYpurPLkrrg2MJv5llxdpxcx36feKriBvg96no/hyP6DvtJWD65/LtoG9aXQIAAOBSdu3Jnc1SoYS/B8tm64Q5LYkA85TfZC1PdlWj+Nqkfp/ZfnTyVLlda/jwLPtxSuuDqbXKViwf9xeh0ye+yvIOicLM99TLqSOS6Im4wwEAAHBZbFYB3A+fbAAAAMC7sBsHcD98sgEAAABvwVYcwC3x4QYAAACcj/8bdgB3xYcbAAAA8BbswwHcEr8zAAAAAACAXfidAQAAAAAA7MLvDAAAAAAAYBd+ZwAAAAAAALuI3xkeAZ0q+8PFSzy/ewAAAAAA8LkKvzOM8dlfJEQD/M4AAAAAAMDnWv7OMD04/jLwEun/1PA7rPCfQwAAAAAAgOvI/s4wHjfDnBL8yAAAAG7M/C8/C/+B6PTC/rZqTNjsR2t2K6rvzfwuV5soezObqcxsqTsqG58dP8rQr3KESzWDuxI3WHRq+rZNZc6mAgAAuAdnh197Ctjy6LTMlkq7TBLl7Pe/K8myxEGZo3JHT5Ti3BJmz+YNtuWOMi/Jjj+9vFniIJdqBnclbrDo1O/j+v7UGXa9xwEAAD7FcsNT2xHt3VY5WzUnrZlnTNhpfm+SZf4jMuuK59eNOPeD07N/d225o/yrUuNPr+3kP86lmsFdiRssOvX7uL4/dYYtb3AAAIDPojc8u553OjsrJ6GT08wzJix3HlUvJzk58zXrRpxbwulZ3wzLSKF2YXn88dpO/uNcqhnclbjB9Jtlefny7BjA3Q4AAG5PPHT0n3eaqZYXNp+YzGu3PAdtSXJy5mvWjTwGOsbP4w+4644qVH+Jn174cvYir92lmsFd+W+36Q3pfGiYZ7nVAQDAl4j2+bX9/znZmmn7116n1plTXKFu5DEjYpZJnPhsfj+sWVqMX0h+qEs1g7sy3/XR3ei8W/3M3PMAAOAbTLc95b1QdGE/Wy2gk3yj42q9a8t6ta2ys4fP3k7ZGc14J6xZery2M9ehLtUM7mr5ri9fLs4+DKkpAAAAPsu47elshDZmO3Q/ZiZfhjlbx+isvjB11iyxayWdcs4Uu1rNLoUzVLaTwpjNVMvOxwAnuVhD0WFt2f08W/r0k+NOlndIOcPy8pcA7j0AAPA9HtIbszU78ZOXw8xJp8f1dMu0y9IiYDpLijmpX7rZalTX6dlsvrwmhbDt40dz6VQiUq/VI74Js3miFSj06aTFLS1vpHKG5eUvAdx7AADgq+zdim/MdtwDgplQhEVjjsHT47XMfoxubyya4kzql+73+RI/vVznLFxirkk2LDv7eIkYROd/XfpSpFbLc0Sf5triBsQr7t8MY+TyXno5y40HAAC+zd6t+MYt/XGPCWaqKGx6POpwPCiqmxn8406HWRvrbunzJTi1hmYb5TURYUvLolHp6SC6z9QKTPuM+hdDNS/J9jmNwe2JF92/Hwr30vQuTXUOAADw6XZtxY/Y1UcbvE7m1EZxDNPHdRJdOjpVaMPPk+XUbbaaanJMMuY0l92/xMmgwwRz8Kj0dBDdZ1RaB288nr0k22d2VXEb4tXP3hip2+kljJsQAAB8ocdgS5JdO/ztmc0M07Bs9Zd4ca3InOpk48pPFepuWUm/H+dIuW2zh2WYtmX853C/TUtkVyCK33V8OVezT3wVbgAAAIA3au7JH39NjzyHp55mlS3dZsOypac9Ty/PnorityyROdER/W/p5+XIxmX3e9BhS+Xxf//prEN2BaL4XceXczX7xFfhBgAAAHij5p58uc9/rP5/88vlCtnMa7c8vDxifmS2E7NiTZTQ7MeJ7/fzcrCwVqmlMyOXYamiUc7p4KKB7ApEHe46vpyr2Se+CjcAAADAG3X25M5zRPQscKmGnbBsXfOZaBmZ6sQvWuD071yyq6VlP2bPS4UeCmHZZRnjp4OL5Nnxow53HV/O1ewTX4UbAAAA4I06e3Jx7fJZoNzPQQ0vw7J1x8GjDJ3MZvXyijl1Rf4tK9npJxuTXbSNYdllEWv7HB6yRHCz4sbj0dldfeKrcAMAAAC8UWdP7j8vbHxGOK5hHbZlkOMyO8G1RVvWFcm3zJvt5zmbWvS8VOshG5ZdFrG2z+EhSwQ3K248Hp3d1Se+CjcAAADAG3X25P7zQu0ZwXwM2dWwDhPXioNOkuW8tSmal/hJlivTXMlsP8/Zvef3bAZsD8u+Rsu1Xa65rqhX9Yjj0dldfeKrcAMAAAC8UWdPHj3UjKdSJfy0nYYLYanjy4PL4FRmPV1n0Y6oW0jl9zOeNRcwlX9vqqjVbM7syKnjW5LoYQ/tE1+FGwAAAOCNmnvyR8mWnMcNG4VNj0dd7U2Sba/T4ZZl2bgI2X6m2QqDO2GFVEvLwUXp7MjLeGfYzvHfZ8U6bOkTX0XcAOMd+K67ZXo/n98GAADAds1dVrRhE3albXZbC/Nb6hz3M78E6CRmJ9llEXnMU7rPbD9RCf9CP6yQamk1tyod5RH5/WaOOO4vQr9PfBVxA6RuvE4D2Sa5aQEAwG30t1jO/r9QxX+sqLVaC/Nbyp5KDStitnSYWpb+IiznTfUTlRAH/cuzPUxT9WcXpfXBsYTfzLJi7bi5Dv0+8VXEDaBvm6OrR2HctAAA4E527cnNJ4Uj0taydcKclmrPQf6wOrLZYSq+NqnfZ7YfnTxVbtcaPjzLfpzS+mBqrbIVy8f9Rej0ia+yvENSl2ysHsVwxwIAAOA22NwCuJ/C7wz61K7qUQwfxQAAALgNNrcA7mfX7wz6P8t5zv4LIv1f3TwMhXkBAACAi2BPC+CWtvzOIH4BEL8P8DsDAAAAvhZ7WgB3tfF3BnFq/G1hWcKpBQAAAHwutrUAbqn/O8MY1jmre+BHBgAAAAAArmzX7wzR/1FD+XeGKO1YAgAAAAAAXAS/MwAAAAAAgF0KvzMsf0nQSfxrxwB+XgAAAAAA4MrKvzOIsC3/PYNZCwAAAAAAXEf2d4bp/83C+NOB/tOpng0GAAAAAABvt/ydYWoZqZNMzy5743cGAAAAAAAurvA7wzJ4mn/5G8WyN35nAAAAd+JsscYw86qXa4/LLHZxZoAuVO5hY6HPUh65cBsAwNTRHyB8QAEAALxIPd2LYL3LWoaVMy+Tp4bShco9bCz0WQoj6ztB57nmwl6zK+B7HP2p+1Wf6gAAAI7UM13nAVCHlTMvky9T+YXKPWws9FmyI+vbwLmFtra/wTW7Ar7K0W9D3uYAAAC/pR7oak9/44XNNpYllsnNC1Oy69Cp9UGyIy/vhOVLfMAQRdfsCvhCR78NeZsDAAD8Nn0Uip7pas9Ny4fEcmYnw/IpddfDoJPnCx88UyPr++SEF3Gva3YFfCHehgAAAKcxn+mc+GV+fW3/iWz5BPry742l/Txf+OCZGnkZvHyVL7Ww1+wK+EK8DQEAAE4jnoOmp8rPjC9SnTRn+X3w0EdUJ88XPniW75lUwDUX9ppdAV+ItyEAAMAVTB+RXg6mHgn1M1cqs9/w89N+Z3gMUkmWx8XsZldmAz+nUsubCo5G0A2YzS8b0MHLrgCciXcfAADA20WPReLpSTxq6ZyFzLWeo5hdz4BOntoi+En8S6Jay8ujyOisSLtcgWW805s5phPjB5tFAZyDdx8AAMC7lJ/Fpls4ca1ZOvtoph/3oh52PQDqEZzlqgVHGVLBuuIybJltOoW/jLVlNwPMmFRwKiGAo/HuAwAAeBf9TCQenZxnKBHWzDzmaR6pcaaIJpqeMuN1kmlw6nIxWqr/R+N3huXlIizV/DJhPxjA+XgbAgAAvIt+sms+TJlPgoXMIl48EtZKmNUdzqSp41F+kUSMIEbTs6cujJjrtmwg1bxzvBacmh3AdrwNAQAA3kU/1pWf48aw6FQtsyixnGLjk+B09QTRth7KOejnX9aqdVioLi6MRnOqbLwwW6U8O4C9eBsCAAC8Xfb5yImvPXOlrnoJdh79Nj4JlhdBXxidjfI8hx21OeDjL3+0zlnHY8avUm5vPJWt0p8dwBa8DQEAAK4g9YjkBNeeuTptOI9+G58Ey4ugL4zORoNE/466egT80TpnU2od+u0JheC9swPo4G0IAABwBalHJCe49syVvep38PTCl4QbHwPLi6AvjM6KQab/HpM//vJrpQZ3lsVX6NBvTygEb58dQBlvQwAAgCtIPSI5wbVnruxVv4OjC52YgvIi6Aujsy/Hl//Wl6dqpQZ3lsUPLnR4ZnvNeAAH4W0IAABwmtTTWflRrnN5+cnu+cW/Mzx/bapTFbPH+2ezwYUOz2yvGQ/gILwNAQAATpN6OnMe8cRGrvAkaGYWlyzLbXwMLC+CvjA1iHNK5Mwe75/VE/nZzFUqNF8OXsYDOA1vQwAAgDNNH4Wixz3/oFloS+YoT3StE1PgZItissejQZxT29swLzQX2Wzb73BL83uDAZyMtyEAAMCZHivlYFGo2UZ2nELMsuFafBQzbSbV4UuMHtC5JDWa03907XKoKXGVf0oPvlyKVLAzO4CD8DYEAAA4mf80lw2OLuy34Y+zJSZbtxAjxvdXzDm1rKXjndFSU2xJJWL6zZeDnWwATsC7DwAA4GTm41UhOLpwb2adZ0tMtm4tprACIqxQ6/fxzmjThOYy6qUQeaKwcvPRJc1gfxEA7MK7DwAA4C1ST0OFRyczvv9Q5mTIxmTrlmOy44vgWq3pVX5LZkLTY5CK7zS/MTiVFsARePcBAAAAAIBd+J0BAAAAAADswu8MAAAAAABgF35nAAAAAAAAu/A7AwAAAAAA2IXfGQAAAAAAwC78zgAAAAAAAHbhdwYAAAAAALALvzMAAAAAAIBd+J0BAAAAAADswu8MAAAAAABgF35nAAAAAAAAu/A7AwAAwFs8Zt7d1CGc6VLrYAZPV/jeSw0AV8DHLAAAwMn08++uvdky4TmbwPIPAv1gfmcAgLfgYxYAAOBMyx8Zdm3PdLbTNoGdXwOOCz5ndgD4TnzMAgAAnEk//G58Co6ynfms3fwpQHS+NxgAsBEfswAAAKfRj7p7H4SdB/BmCb+BqNz0bKrzt48JAHjBZy8AAMBplg+/Jzwdn1yC3xkA4Nvw2QsAAHCab/idIfqRYSyXOpXNw+8MAPAufPYCAACcpvzwW3ha14/q/i8AzVYLnS+bd/JEbaRmAQAU8HkLAABwmvLze+FpXT+qi06cGKfVcufL5p08Ylh/FgBAAZ+0AAAAZ6o99hae1vWjetRA/9l82UbtVDaPGMSfBQBQwCctAADAmWoPv4Wn9ezx6FTz8bzQ+fRUOThSmAUA4OBjFgAA4HzZ59/C03r2+O9T5vHspJ1TW4KX6wwA6ONjFgAA4C0eMR1sntp1XJ9Kjdk5VQsWkYVZAAAOPmMBAADe6zEjYsxTu47rU6npOqd2tdeZBQDg4DMWAADgIh5/RafEVbuOC52hOqdqeVL9AAC24DMWAADgOrI/DhQucY4LuybKnqrlSfUDANiCz1gAAIDTLB9ynR8BmpcUSnTUfh8YT9XypPoBAGzBZywAAMBplg+5hR8Bdv2ecNADeO33gfHUccEAgL34jAUAADjN4y8d4BwvXFIo8e9salI/7Xg21WE5Q3kiAIDGZywAAMCZxKOueWrjJaI385LUvObZVBtbggEAG/ExCwAAcKaHR1+1PP5s/84wrdKct7Aa5wQDADbiYxYAAOBky0fg6fYse1XqAdys0hw2uxpnBgMAduGTFgAA4HyFx/koMrpQJBQVtz+bOxlS5ZorVh4EAGDiwxYAAOBdso/A0+Docp1W1934YG7m6a/DlmAAQB+ftwAAAAAAYBd+ZwAAAAAAALvwOwMAAAAAANiF3xkAAAAAAMAu/M4AAAAAAAB24XcGAAAAAACwC78zAAAAAACAXfidAQAAAAAA7MLvDAAAAAAAYBd+ZwAAAAAAALvwOwMAAAAAANiF3xkAAAAAAMAu/M4AAADwFo8ZJ/joZo7I3xd1eFDnflrzFTSDp3eFeZ9c/0UE8CX4IAIAADhZ4Sny6EfIVP63PM9GRbc3U360X7bhBEfVa1c1lwIAavgIAgAAOFP/QfLorvYG7xIV3dtM+UXRkX6wbqBwSX9NACCLzx8AAIAzFR4kn/zOcL3fGVKvoB+8jMwm768JAGTx+QMAAHAa/QxoPkse3dje4F1OWJlpqlTdLcF+b1syA8B2fP4AAACcZvkAeMLTdK2xtzthZfrP8gf9GiAi33XDAIDAhw8AAMBplk9/73psvP5j6Qkr4z/Lpx78U8G6MZGhlhkAjsCHDwAAwGnKT3/OU7Z+2NRFU/mXCc2iKU6HR+TPBqeWq/PSZHsGgNPwuQQAAHCa8gP49Cqd7RHo59fZUnV9UapdJV7y+DMuT6WCO7U2rjYAdPBBBAAAcKbXx29vMzYNNp9AnXKp/LW6yxkLK6CbKedf9m+uQCE4FZDqGQBOw0cQAADAmWpPheWn12m888CbChbHdz32poo280ecoltW0gxI9QwAp+HzBwAA4HzZB8OXAP/x0zm+PX8Uv1gUqTBUOX/EKTqeSgWnAlI9A8Bp+PwBAAB4i9Sz4ctZP9I86+ePkoi6Wx57zc635I/mepR+OiivzHK0VM8AcBo+fAAAAN7rMePEbHx09fNHScotmZzOy8l1nvHUccGp0Y5ecwCo4cMHAADgIh5/iVPbH139/FESkUFnKyyOP28///TUccHTs1t6BoDT8OEDAABwHc7T9MjJsCt/lERnEC1tXJlycp1nPHVccGqu5uUAcBA+fAAAAE6zfPpznqYf8n+qUZfo549OHf1gW5u3n3966rjg1FzNywHgIHz4AAAAnGb59Jd6mvYPbsxfq9uXamZj/ump44JTczUvB4CD8OEDAABwmsdfOmB53D+4MX+5brQmplQzJ5Q4YcWcuU5YFgDI4sMHAADgTI+/CqeW8YVnz0Pzb3nmPTS5SJWquyU4NVQ2MwCcgM8fAACAMz084qrl8Wmqg/KL0cbjTkVn3URRIVVimeG44NTiZDMDwAmWH00AAACYOnQDJi7JHnc6T+U3+9R1dT+1DrNT6yrLy88J3tszAJxAfC4BAABAOG4PpuPNU6m2U/nNYF132VJzTHNwXWh5eapQLe32ngHgaHwEAQAAvEvtEbV2Sm/5UvlTj8lHPEqLWoJTxe//0OBC2+VhAWA7PogAAADwLuxFAeB++GwHAADAu7AXBYD74bMdAAAAb8FGFABuiY93AAAAnI//JQEAuCs+3gEAAPAW7EIB4Jb4nQEAAAAAAOzC7wwAAAAAAGAXfmcAAAAAAAC78DsDAAAAAADYhd8ZAAAAAADALvzOAAAAAAAAduF3BgAAAAAAsAu/MwDAB3nMbIyvXTJeuCvyCOZ003UQsqV3RUatbszfvCqbuZz8uPYAAEAKX8cA8EGyT5Sp+GlwuZYZuRx5L38uZzWyg2xfonJjhzZfsCX5ce0BAIAUvo4B4IP4D5LZeOeJVZerdWUOvkVqLn9B/EH2LlGzt07zTnxKYTG3ZwAAALvwdQwAH6T8IKmDnWfVQjlnito6FGTn2rImyx6cyPI4OkmneSfeV15PkWRvhwAAIIWvYwD4IP5TpB+cfSbtxIyRmenrRGNmzzqheeGuJdrycqT6r62SaWw1W6J5OQAA2IuvYwD4INMnsuhj3Aw2H9CWYbXG7NFb/M4LCctXlZfoiJfM71xHFvjLcsTlAABgO76OAeCDTB+pnGfVKDj1dKaDa43Zo7foitl+UosmLqwt0UEvmZ9HR2ZN16T8WhzUJAAASOHrGAA+yMuTlH6wciI7j3XilNnYaV9Ay3J+P53n2S1LdNxLZrbtFPW9ZE4VitZze5MAACCFr2MA+CD+Q5kZmX00MyvqtCc/Dzrl/JaW0/nX1pZo40tmZjv09XpJ3nkhDu0TAAD4+DoGgA/iP1g5keOTWqGHKJvf29GccmZLtRWLLi8s0d6XTJ9KxdRMMxdei6P7BAAAKXwdA8AHeXmS8p8fOw90OnO/t6M55cyWHn81Oyks0d6XzJmoOXKhsexrkb0QAAAcja9jAPgg45PU8vkxujA6WOhhelw8n578POiUyz7b1jrvL9Hel6x/tmma3H8tzmwVAAD4+DoGgA8yPkmZT2qdB7plD6mi5bplTrlsTK3z/hLtfcn6ZztE5iPGBAAAp+HrGAA+iPmgWg6r9eAU7dctc8rtisl2kl2ivS+Z7kGf6hOZjxgTAACchq9jAPgg+kG1H1brIVvi5OdBp9wy5vHXxk5SS7T3JTuoXL+rg8YEAADn4OsYAD6IfiZ1jjgPs4UenBIP+aPHcZxyy5gtPfeXaO9Ltgw47pU6YsGP6xYAAKTwdQwAH2T5oGrGiIO1HlLHT34edMotY7b03F+ivS/ZNGDaw/ZX6ogFP65bAACQwtcxAHyQ6ZPUy0EnRhys9WAeFx36RceEtYZTMbW1MpP4S1Rro7YCW0Z2yi0Vcm5vGAAA+Pg6BoAPMn2Sch5RlwfN7wJxiUjldJiqm8rgBOuYQsPZKuYSZWf3Lxljdk29bMmRTbu3YQAAkMLXMQB8kOmT1MvB6aNW9Ai28VHOPFV4fmxmcIJ1TKHhbBV/wGwzZrxooDl1tla5geMaBgAAKXwdA8AHiZ6klo9m2ePZBpapOs+P4vJs27WAQsOFPOaA5dmX8c3XyCdezU4Ph/YMAAB8fB0DwAeJnqSWj2bmhX71MVjn6Tw/9vm9FS4/og1znZsvWa2BplTm44IBAMBx+DoGgA8SPUktHw/7z63LsGWSE55hIxs7P66Np71Eu14yp/r216jc0t5gAABwHL6OAeCDRE9Sy8dD8QjmPFo6z57Lpzyn0EHMlanNVWvD6dNpKQqrLfUJL1Aq+XHBAADgOHwdA8AH8Z8r/QvHa5eyvYlCyQWoO3SuQg9+n+VxCp2XLyzk337JcW0DAIAUvo4B4IOIJyn9eLh8BOs/sWZjzv8COm6uQgN+k7Vxam0f/QIVMvuXHNc2AABI4esYAD6IeJLSj4e7nltrvfl9Hu24ubLV/Sb9yOxonSZrCsn9Sw7tHAAA+Pg6BoAPIp6k9NNl6hGs9qxqxjefgvuy023s9rglqr1knSbPzFxYt16nAACgha9jAAAAAACwC78zAAAAAACAXfidAQAAAAAA7MLvDAAAAAAAYBd+ZwAAAAAAALvwOwMAAAAAANiF3xkAAAAAAMAu/M4AAAAAAAB24XcGAAAAAACwC78zAAAAAACAXfidAQAAAAAA7MLvDAAAAAAAYBd+ZwAAAAAAALvwOwMAAAAAANiF3xkAAAAAAMAu/M4AAAAAAAB24XcGAAAAAACwC78zAAAAAACAXfidAQAAAAAA7MLvDABwG4+86Fq/Srb6luaPWAEnlViWvZefkNnP0F+BqG1tObVzIQAAOB/fyABwGxsf5cwq5er95o9YATNJ81XQl5+W2b+2UGW8ZNm8HqdzLQAAOBlfxwBwG3sf5ZwSnep7m9+VpFO9f7mwPXPqqmz+Md7vf1qoeTkAADgT38UAcBv9Z7HlY5p5baF64fL39tBcf+cFPS5z9sJO/sIIY6HC5WarAABgO76IAeA2+k9Y+jFN53eqmw+SB3W45Cd3ArKXH9fYMuHyqlT+aXBt8GnOXX3i/9gxtyVHdlyH1v//9JyHEzFRUzLgRVKSLw085c4kccu2S95BEARBcAj5QxwEQfA12PILS5E8/QU3/CU4/BlbJSnREonh+jljhLNae2Ny8mra6j2tIAiCIAiGyF/hIAiCr8Gun1e934k3f4eq4UkDcFf9jB2unzP2dLKx1TA8eTXV9aFWEARBEARD5K9wEATB12DXz6v1tyf5NXrzd6ganjRwzX/V5HbmP2ONLTOpxiavpro+1AqCIAiCYIj8FQ6CIPgabPx51fgpytXnv0PV8KSBa/6rJrcz/xnby3/i1VTXh1pBEARBEAyRv8JBEARfg40/r3405uoPJ7f8kJw0AGMeWr/GPCl/8s9gGGHLP48gCIIgCO4gf4WDIAi+Bnt/Xv08whb1yU/d1VjPQ5X5wvod5kn5T23ceTV8cu9bCIIgCIIAIn+FgyAIvgZ7f179PMJcXRFu+am78cdsg2e4fod5Uv7TyTuvBo7tKj8IgiAIgiryhzgIguBr8PDXqEGVkA83CKvmHwoN1z3D07rm66eZzRYk5O1x/yRUdf1pkCAIgiAIziF/iIMgCL4G23+FleaHPwOH6xsb6G3tWj/KbIYhj1H0Zp6a37vuUwRBEARBcBT5WxwEQfA12PtDbD5fUt9ifgsJ4RlW4dfPMZvJOYlneOrcBxmuB0EQBEFwE/lzHARB8DXY+1usujKU3mK75MFTPSV8ujtf38v8dAbaU2N+3RgmKdqLQRAEQRDcR/4oB0EQfA02/tpq/I4b/gws/cZskMwx/Ek7XJ8zzwceypmbDXIi6v1sLDYIgiAIgh7yFzkIguBrsPGn1h8qwnzuhyTnv/Bjc/iT9twv4qfMy89xB66l7jxdaac7wR8EQRAEwUbkL3IQBMHXYNdPrfUnJ/kRevqHJJm582PzQtLtxn7qKAmdfjVk/VyxQRAEQRCUkD/HQRAEX4Ndv7Me8rzbD8mHYxMPfLfXT9vkFuafOqAQXKymbqxz/0EQBEEQHEX+FgdBEHwNtvzIUj/Wnv6Iu/BD8unYxAPffdrPLqGNzD91cC2yVU3dWx+qBEEQBEGwBflbHARB8DXY8iML/lbdrt74IVl1yA00fswO188Zqyr2XsTTlcmrabtqCAVBEARBMEf+EAdBEHwN5r+w/M80/vSo+TseFIOZGa7fMVbS4paerjRS99aHQkEQBEEQzJE/xEEQBF+DjT/lyI/Zc+ptn3sb8BmfPq2u3zFW0hq6anvY6KqhFQRBEATBEPkrHARB8DVYf/c9hVqHKo31Km3bQ6OBKoM31liHkUvMDUW4UkpU8j9M0YgcBEEQBMFG5K9wEATB12DyU878xPMqD+8PzVeTbmmgSgK9Vdd5asjcEIUrpVDQvGIrpeCugiAIgiA4gfwJDoIg+BpMfsq1f8f1GCDnxEO1AUXOt3at72Vu6PaSDp37LNUUjdRBEARBEOxC/gQHQRB8Ddo/5czvOyK03hyab4QdNkAkqtGG61uY29KNihqePSZBTtQeBEEQBAFE9e8vORVUzwxKQvm8dmZ4GlY5eTpA+MnKllwfEbydpaHYsDeJEARBEARBEARB8E2o/izyP8F+nv1/ht9aD3+U+Z9+Pc9tPA1r/JsZyM+HtyR62+DVOHPFXcM8SBAEQfCh8H8ygiAIguBfxvBvK7m/yvmxh5zr9VE02iuVzGmrw71E7xm8kWWoyIf9JM8SBEEQfCie/iEIgiAIgn8Ww7+tvfvrGFkcei6h2l6p53PD7ThvGHye5X2GgyAIgiAIgiAI/gVMfg2pH1Pwvv9PZfIdfr6RX5294ZJi9SftnOR+8HaWtmKvjXalQRAEQRAEQRAEX4bJTyH1Swre//2fT1ceomd7iOqP2ep9rniZ5H7wqpktipffSxAEQRAEQRAEwZdh8lPo6Y+shzd/lv/PAH+vPR2+g8aP2YePeJAq8yGS+8F3ZSkpXn4vQRAEQRAEQRAEX4YtP/fU/Yd4OuZNvva3W++n5dP4/9Fltv3sJXmr4EcV3+e9BEEQBEEQBEEQfCImP4XIbz34k9D/Lvt9/7W/3aq/cM0jFZ8HrM5PSD4l+Fxxbs87DIIgCIIgCIIg+G5Mfgo9/aUGx8zklh99u+Cl4U/XhzdL0bZUUSJ5k+B3FPfagytBEARBEARBEARfg8lPoae/1PwkGZv/6NsILw1/uq53qum29FAieZPgFxRP2wuCIAiCIAiCIPh6TH4NPf2t52+SO39uvvbnm//9CH+6eiryE/VnwTDLU5I3CU6Gh4pze6qNIAiCIAiCIAiCfwSTX0NPf+v5m6WxudshyI/HH4A/k56Bq0zicLnXBieWhopDe6UUQRAEQRAEQRAEX4nJD6KnP8r8/Ydj6811ped2CPj78ecZdgmVJicknxX8kCIcrkYOgiAIgiAIgiD4Pkx+ED39refvl8bmbodo/AR+CC60fbhH8obBzeQLq67mDYIgCIIgCIIg+EpMfhM9/a3nV+C6ur6J6u/HP/Mv/PHbcDvhvxN8l+GNwz//i6dsQRAEQRAEQRAE34r8LCIY/oR81Y/fub33DL7L8K7hn//FU6ogCIIgCIIgCIIvRn4ZEZBfkeR3aGn4v4+2/Kzukbw2+JYsp6v++V9450EQBEEQBEEQBF+P/DgimPzcfni/9KO18eO3FGHIfy54Nc5csWGv6jwIgiAIgiAIguC7kR9HTwF/Rarfm+T36XyYvMQqyTsEb2e5PwydB0Hwj6P31Uq+ZB6uKAydt/lLJA2qRq4gCIIgCLYjf5efgh9gSiel0smqfQybkHD+Nu2dLC8chnGCIPh38PT7of2tQr6RJt9OW/irJA95tkcLgiAIgmAv8hf5KfjRZeNJaTjccPi2wbdn2TXsUzcSBUHwL6D9/TP57tryBbWFf04yUQ+CIAiC4A6qf4sbJ4TXYldFkK1k49zwdnvnmNtZFM81e2psGCoIgu/G/FtFfbeU1hvfUVv4hwwT6SAIgiAIrqH6t/iz/nBvcVs9tFTPOaX5LYcoSPJWwbeQ7B3+YejlCoLgW2G+H/y3x9OvF/jN0/6O2sI//HocVhQEQRAEwR00jhnnzGzHZ7kNgiAIvh7qVzD8gbzlJ3zvx/gW/sn/BJhXFARBEATBHeT/MwRBEATBNQx/gO/6CX/0/zOY4V3/nwGO5RgQBEEQBC9B/j9DEARBEFzD/Ad4/j9Du6IgCIIgCO4g/58hCIIgCK4h/58h/58hCIIgCL4eJ/4/g/n7/vO/KO02POeAEQRBELwVyA/w3h+vLf8f4DT/JOO8oiAIgiAI7qD6x/rp8M8C88ifsh5qlQ4YOYcEQRAEbwXzN274O5rv9oS28E8Ctp0HQRAEQXAZ1b/U8HShrn8e/W+Hp7srP/ScQ0gQBEHwVjA/k38eYQuzUbnMP/y/BMOKgiAIgiC4g8Yx4ynbz6P/t7CeB8zww/n/1M8nOX4EQRAEbwX/h+zh72j4V8/sTmj38s/tbckSBEEQBMFRNI4ZT9l+7P8rgMPrbuM4kVNHEARB8FZ4+oes8dMbLnKqc/xbHO7NFQRBEATBdlT/IvvhP3/i4TmB/+dTzpLbIAiCILgM8nP40K9vQgKdt/l3mdyeLgiCIAiCjaj+OfbDf/6+mz/362Hg6X8+5ay6DYIgCILLKP0WLv2OfvrTe/gbfAs/J4FWt8cMgiAIgmCO6t9ieH54+J9mku+Wzg85ZgRBEARvhfYP4ae/o+Gj9m/wLfxDD9zhIYkgCIIgCAiqf4j98J+/7KWzUHV37jYIgiAILmPjj/0/DE+ZN0q3+S/8TwBTURAEQRAEd1D9K+yHyf8reHpAMrvmfsNtEARBEFzG/Cfw0z+jh37mb+G/838A7qgEQRAEQaBQ/Svsh5/+v4Kf/0Vp199vuA2CIAiCy5j8X4I5g/kr3HZe4m+rl3YnKu+AnyJ+b/lrrzLf9ZPrfc7Js3sn1adehXsgDuc+Vz8kl7p/p1WfRSXi74Xr+kmluLriT7luj9/3QBIplV6H6inhJFmqDfR4qs6rGQl4OpWUeJ6jquWHH8b/vbJWxHf9/YbbIAiCILgM9XfQ/H3cy8CFeot+rK1e2p2ovAN+ivi95a+9ynzXT673OSfP7p1Un3oV7oE4nPtc/ZBc6v6dVn0WlYi/F67rJ5Xi6oo/5bo9ft8DSaRUeh2qp4STZKk20OOpOq9mJODpVFLieY6qlh9+GH/9T9OJ2lVjE7dBEARBcBnqT+HDU0SbAa5XDwBz/p501flE5R3wU8TvLX/tVea7fnK9zzl5du+k+tSrcA/E4dzn6ofkUvfvtOqzqET8vXBdP6kUV1f8Kdft8fseSCKl0utQPSWcJEu1gR5P1Xk1IwFPp5ISz3NUtWBFD8n/lFPa5SQlt0EQBEFwE+tR4eEj9ffLzDzdbUxu5+9JP1xvVPQp+Cni95a/9irzXT+53uecPLt3Un3qVbgH4nDuc/VDcqn7d1r1WVQi/l64rp9Uiqsr/pTr9vh9DySRUul1qJ4STpKl2kCPp+q8mpGAp1NJiec5qlqNitT9PwNm9yH/FrdBEARBcBPmb9zTP4L8KffA/1Bu4W/oGuZqRZ+CNabH7y1/7VXmu35yvc85eXbvpPrUq3APxOHc5+qH5FL377Tqs6hE/L1wXT+pFFdX/CnX7fH7HkgipdLrUD0lnCRLtYEeT9V5NSMBT6eSEs9zVLXI8NrDf2x1T3cfDuxyGwRBEATX4P/M8WPGuv70D2h7eCN/KeDTmNXdT0GvInLtVea7fnK9zzl5du+k+tSrcA/E4dzn6ofkUvfvtOqzqET8vXBdP6kUV1f8Kdft8fseSCKl0utQPSWcJEu1gR5P1Xk1IwFPp5ISz3NUta4Z24LPchsEQRB8Pdajghl4eswoMRuVLc4JP0zHk5YWPwW9isi1V5nv+sn1Pufk2b2T6lOvwj0Qh3Ofqx+SS92/06rPohLx98J1/aRSXF3xp1y3x+97IImUSq9D9ZRwkizVBno8VefVjAQ8nUpKPM9R1bpmbAs+y20QBEHw9ViPCn7GnDEazC+ffxqtEZZvfQp6FZFrrzLf9ZPrfc7Js3sn1adehXsgDuc+Vz8kl7p/p1WfRSXi74Xr+kmluLriT7luj9/3QBIplV6H6inhJFmqDfR4qs6rGQl4OpWUeJ6jqnXN2BZ8ltsgCILg67EeFcgkWeHMD/k3Ojf8T49P7cglb2+OXkXk2qvMd/3kep9z8uzeSfWpV+EeiMO5z9UPyaXu32nVZ1GJ+Hvhun5SKa6u+FOu2+P3PZBESqXXoXpKOEmWagM9nqrzakYCnk4lJZ7nqGpdM7YFn+U2CIIgCIKAnzYnJ+EqD9n1k+t9zsmzeyfVp16FeyAO5z5XPySXun+nVZ9FJeLvhev6SaW4uuJPuW6P3/dAEimVXofqKeEkWaoN9HiqzqsZCXg6lZR4nqOqxRt4E5yrLgiCIAiCYDt6Rx1yTVQUw8pDjluKuZeIOyGKxO0ky7rLm7/D43eVrrpTbczPE1dehXB6HuKN61YzekW1Vc3Cnfu3QBrz3nq6fsszcK1VxafreSYqnN9nUS3txU2tIAiCIAiCwIOfNtV5kp9FOZs6r/qTpGLuJeJOiCJxO8my7vLm7/D4XaWr7lQb8/PElVchnJ6HeOO61YxeUW1Vs3Dn/i2Qxry3nq7f8gxca1Xx6XqeiQrn91lUS3txUysIgiAIgiDw4KdNdZ7kZ1HOps6r/iSpmHuJuBOiSNxOsqy7vPk7PH5X6ao71cb8PHHlVQin5yHeuG41o1dUW9Us3Ll/C6Qx762n67c8A9daVXy6nmeiwvl9FtXSXtzUCoIgCIIgCDz4aVOdJ/lZlLOp86o/SSrmXiLuhCgSt5Ms6y5v/g6P31W66k61MT9PXHkVwul5iDeuW83oFdVWNQt37t8Cacx76+n6Lc/AtVYVn67nmahwfp9FtbQXN7WCIAiCIAgCD37aVOdJfhblbOq86k+SirmXiDshisTtJMu6y5u/w+N3la66U23MzxNXXoVweh7ijetWM3pFtVXNwp37t0Aa8956un7LM3CtVcWn63kmKpzfZ1Et7cVNrSAIgiAIgsCDnzbVeZKfRTmbOq/6k6Ri7iXiTogicTvJsu7y5u/w+F2lq+5UG/PzxJVXIZyeh3jjutWMXlFtVbNw5/4tkMa8t56u3/IMXGtV8el6nokK5/dZVEt7cVMrCIIgCIIg8OCnTXWe5GdRzqbOq/4kqZh7ibgTokjcTrKsu7z5Ozx+V+mqO9XG/Dxx5VUIp+ch3rhuNaNXVFvVLNy5fwukMe+tp+u3PAPXWlV8up5nosL5fRbV0l7c1AqCIAiCIAg8+GlTnSf5WZSzqfOqP0kq5l4i7oQoEreTLOsub/4Oj99VuupOtTE/T1x5FcLpeYg3rlvN6BXVVjULd+7fAmnMe+vp+i3PwLVWFZ+u55mocH6fRbW0Fze1giAIgiAIAg9+2lTnSX4W5WzqvOpPkoq5l4g7IYrE7STLusubv8Pjd5WuulNtzM8TV16FcHoe4o3rVjN6RbVVzcKd+7dAGvPeerp+yzNwrVXFp+t5Jiqc32dRLe3FTa0gCIIgCILAg5821XmSn0U5mzqv+pOkYu4l4k6IInE7ybLu8ubv8PhdpavuVBvz88SVVyGcnod447rVjF5RbVWzcOf+LZDGvLeert/yDFxrVfHpep6JCuf3WVRLe3FTKwiCIAiCIPDgp011nuRn0R4z4VfzXp0oKpWeQ+WHzxDnPu+kMeVHMVQ9kx68k/Wa9En4yXxPfVdqnvRV74XzqMlJwz5jzwPJTtjm3Va1iIrS8p2QjCdwUysIgiAIgiDw4KdNf75dr6sqio3wq3mvThSVSs+h8sNniHOfd9KY8qMYqp5JD97Jek36JPxkvqe+KzVP+qr3wnnU5KRhn7HngWQnbPNuq1pERWn5TkjGE7ipFQRBEARBEHjw06Y/367XVRXFRvjVvFcnikql51D54TPEuc87aUz5UQxVz6QH72S9Jn0SfjLfU9+Vmid91XvhPGpy0rDP2PNAshO2ebdVLaKitHwnJOMJ3NQKgiAIgiAIPPhp059v1+uqimIj/GreqxNFpdJzqPzwGeLc5500pvwohqpn0oN3sl6TPgk/me+p70rNk77qvXAeNTlp2GfseSDZCdu826oWUVFavhOS8QRuagVBEARBEAQe/LTpz7frdVVFsRF+Ne/ViaJS6TlUfvgMce7zThpTfhRD1TPpwTtZr0mfhJ/M99R3peZJX/VeOI+anDTsM/Y8kOyEbd5tVYuoKC3fCcl4Aje1giAIgiA4jYcHkrfa5acpYmPi+T3R64dcV1UUG+FX816dKCqVnkPlh88Q5z7vpDHlRzFUPZMevJP1mvRJ+Ml8T31Xap70Ve+F86jJScM+Y88DyU7Y5t1WtYiK0vKdkIwncFMrCIIgCIJz8GeS3i5Zr+6yk9RzA5O874xeP+S6qqLYCL+a9+pEUan0HCo/fIY493knjSk/iqHqmfTgnazXpE/CT+Z76rtS86Svei+cR01OGvYZex5IdsI277aqRVSUlu+EZDyBm1pBEARBEBwCOZb0dv16Y/fpClFvG35/9Poh11UVxUb41bxXJ4pKpedQ+eEzxLnPO2lM+VEMVc+kB+9kvSZ9En4y31PflZonfdV74TxqctKwz9jzQLITtnm3VS2iorR8JyTjCdzUCoIgCILgBPjJpLer1nu7cMtItw1/BHr9kOuqimIj/GreqxNFpdJzqPzwGeLc5500pvwohqpn0oN3sl6TPgk/me+p70rNk77qvXAeNTlp2GfseSDZCdu826oWUVFavhOS8QRuagVBEARBcAL8ZNLbVeu9XbhlpNuGPwK9fsh1VUWxEX4179WJolLpOVR++Axx7vNOGlN+FEPVM+nBO1mvSZ+En8z31Hel5klf9V44j5qcNOwz9jyQ7IRt3m1Vi6goLd8JyXgCN7WCIAiCINgOfwghR52HM0/XJ7s8zt68H4E1iMfvLX/NVdSuL1wxeA/kPuFRM2TLJ/I8RIvn9YrEw8TJ76e+GT/f88BVlDfiyqtzt9X75N2pdGTG5+2pVHlelUVdKzb/dJKl2pKa9CCKVZ8ncFMrCIIgCILtMCelp4coP3DuaS/OPO9H4KeI31v+mquo3fUpYfAeyH3Co2bIlk/keYgWz+sViYeJk99PfTN+vueBqyhvxJVX526r98m7U+nIjM/bU6nyvCqLulZs/ukkS7UlNelBFKs+T+CmVhAEQRAE22GOMU8PUX7g3FOSRW1tF303/BTxe8tfcxW1uz4lDN4DuU941AzZ8ok8D9Hieb0i8TBx8vupb8bP9zxwFeWNuPLq3G31Pnl3Kh2Z8Xl7KlWeV2VR14rNP51kqbakJj2IYtXnCdzUCoIgCIJgO8wxpnF8apA3dtt+oCUo957Qp8vH+L3lr7mK2l2fEgbvgdwnPGqGbPlEnodo8bxekXiYOPn91Dfj53seuIryRlx5de62ep+8O5WOzPi8PZUqz6uyqGvF5p9OslRbUpMeRLHq8wRuagVBEARBsBfwmNTeVTOT3RN+vgkPjpUWv7f8NVdRu+tTwuA9kPuER82QLZ/I8xAtntcrEg8TJ7+f+mb8fM8DV1HeiCuvzt1W75N3p9KRGZ+3p1LleVUWda3Y/NNJlmpLatKDKFZ9nsBNrSAIgiAI9gIek9q7amaye8LPN+HBsdLi95a/5ipqd31KGLwHcp/wqBmy5RN5HqLF83pF4mHi5PdT34yf73ngKsobceXVudvqffLuVDoy4/P2VKo8r8qirhWbfzrJUm1JTXoQxarPE7ipFQRBEATBXjw9RFV34cxkdzI8yfsp+Cni95a/5ipqV9XuGbwHcp/wqBmy5RN5HqLF83pF4mHi5PdT34yf73ngKsobceXVudvqffLuVDoy4/P2VKo8r8qirhWbfzrJUm1JTXoQxarPE7ipFQRBEATBXqzHD358mszs4q8OT/J+Ch4mMvi95a+5itpdnxIG74HcJzxqhmz5RJ6HaPG8XpF4mDj5/dQ34+d7HriK8kZceXXutnqfvDuVjsz4vD2VKs+rsqhrxeafTrJUW1KTHkSx6vMEbmoFQRAEQbAX1ZOJ2SX8W3Y3Tpbyfgp4xt9JyTVXUbvrU8LgPZD7hEfNkC2fyPMQLZ7XKxIPEye/n/pm/HzPA1dR3ogrr87dVu+Td6fSkRmft6dS5XlVFnWt2PzTSZZqS2rSgyhWfZ7ATa0gCIIgCPaiejIxu4R/y+7GyVLeTwHP+DspueYqand9Shi8B3Kf8KgZsuUTeR6ixfN6ReJh4uT3U9+Mn+954CrKG3Hl1bnb6n3y7lQ6MuPz9lSqPK/Koq4Vm386yVJtSU16EMWqzxO4qRUEQRAEwV7wk8n6594/9TOT3Y2TpbyfglLGH33GXq9XFXVNyiQlE2aSxWtxz8R/tZmVRyXikyQpacBnmWT0fkgPVbdKaxcbYfDMPvXaj2/sdJZdb6SahUzyztV11Uk1C2mSsBGfRF1lqSbdi5taQRAEQRDsxdNDiDn2kEORmpns9sbmeT8FDzMa/N7y16uKuiZl8vfFc/mtamPcm/dM/Cs/PoXP7pOSBnyWSUbvh/RQdau0drERBs/sU6/9+MZOZ9n1RqpZyCTvXF1XnVSzkCYJG/FJ1FWWatK9uKkVBEEQBMFekLMTfEr4t+z2xuZ5PwVrCo/fW/56VVHXpEn+vnguv1VtjHvznol/5cen8Nl9UtKAzzLJ6P2QHqpuldYuNsLgmX3qtR/f2Oksu95INQuZ5J2r66qTahbSJGEjPom6ylJNuhc3tYIgCIIg2IunBxszQA5Famay2xub5/0U/BTxe8tfryrqWqkoBp+F5/Jb1ca4N++Z+Fd+fAqf3SclDfgsk4zeD+mh6lZp7WIjDJ7Zp1778Y2dzrLrjVSzkEneubquOqlmIU0SNuKTqKss1aR7cVMrCIIgCIK9eHqwMQPkUKRmJru9sXneT8FPEb+3/PWqoq6VimLwWXguv1VtjHvznol/5cen8Nl9UtKAzzLJ6P2QHqpuldYuNsLgmX3qtR/f2Oksu95INQuZ5J2r66qTahbSJGEjPom6ylJNuhc3tYIgCIIg2IunBxszQA5FamayW53ZlfdT8FPE7y1/vaqoa6WiGHwWnstvVRvj3rxn4l/58Sl8dp+UNOCzTDJ6P6SHqlultYuNMHhmn3rtxzd2OsuuN1LNQiZ55+q66qSahTRJ2IhPoq6yVJPuxU2tIAiCIAj24unBxgyQQ5GamexWZ3bl/RT8FPF7y1+vKupaqSgGn4Xn8lvVxrg375n4V358Cp/dJyUN+CyTjN4P6aHqVmntYiMMntmnXvvxjZ3OsuuNVLOQSd65uq46qWYhTRI24pOoqyzVpHtxUysIgiAIgu3wBxt/8nl6KDq0C+01aBuE74afIn5v+etVRV0rFcXgs/BcfqvaGPfmPRP/yo9P4bP7pKQBn2WS0fshPVTdKq1dbITBM/vUaz++sdNZdr2RahYyyTtX11Un1SykScJGfBJ1laWadC9uagVBEARBsB3+YONPPq96Wh3blfcj8FPE7y1/vaqoa6WiGHwWnstvVRvj3rxn4l/58Sl8dp+UNOCzTDJ6P6SHqlultYuNMHhmn3rtxzd2OsuuN1LNQiZ55+q66qSahTRJ2IhPoq6yVJPuxU2tIAiCIAi24+nZyZx8XvW0OrYr70dgTeHxe8tfryrqmjRJGibMJIvX4p6J/2ozK49KxCdJUtKAzzLJ6P2QHqpuldYuNsLgmX3qtR/f2Oksu95INQuZ5J2r66qTahbSJGEjPom6ylJNuhc3tYIgCIIg2A5zvHl67OG76/pkF9rbnvcjsAb0+L3lr72K2vW6K6e647W4Q67LnROHvBnih7fEE5E7vrFJt36XzBNFNc/74ZxzFe+WuyK63jnx4JknHfpOiE+St5qCNKPuE13eD+dRHnwnXvccbmoFQRAEQXACpcPJm+yu63fyvj9guj8xybVXUbu83vW+YvPevEOuy50Th7wZ4oe3xBORO76xSbd+l8wTRTXP++GccxXvlrsiut458eCZJx36TohPkreagjSj7hNd3g/nUR58J173HG5qBUEQBEFwAr1zDt9V65Pddf1O3vcHbPVPUnLtVdQub3i9r9i8N++Q63LnxCFvhvjhLfFE5I5vbNKt3yXzRFHN834451zFu+WuiK53Tjx45kmHvhPik+StpiDNqPtEl/fDeZQH34nXPYebWkEQBEEQHELjnPNuu9fyvjmeRnuYlFx7FbXLG17vKzbvzTvkutw5ccibIX54SzwRueMbm3Trd8k8UVTzvB/OOVfxbrkrouudEw+eedKh74T4JHmrKUgz6j7R5f1wHuXBd+J1z+GmVhAEQRAE51A65LzDblVoo+d3hsllwpJrr6J2ecnrfcXmvXmHXJc7Jw55M8QPb4knInd8Y5Nu/S6ZJ4pqnvfDOecq3i13RXS9c+LBM0869J0QnyRvNQVpRt0nurwfzqM8+E687jnc1AqCIAiC4DSeHm/eZ3cit5fkrcBPm+o8qa69itr1uiunuuO1uEOuy50Th7wZ4oe3xBORO76xSbd+l8wTRTXP++GccxXvlrsiut458eCZJx36TohPkreagjSj7hNd3g/nUR58J173HG5qBUEQBEEQBB78tKnOk/wsSni87sqp7ngt7pDrcufEIW+G+OEt8UTkjm9s0q3fJfNEUc3zfjjnXMW75a6IrndOPHjmSYe+E+KT5K2mIM2o+0SX98N5lAffidc9h5taD6X9KwiCIAiCIPinwE+b6jzJz6KEx+uunOqO1+IOuS53ThzyZogf3hJPRO74xibd+l0yTxTVPO+Hc85VvFvuiuh658SDZ5506DshPkneagrSjLpPdHk/nEd58J143XO4r1XCHWNBEARBEARvgt5hiVx7FbXLD2nrfcXmvXmHXJc7Jw55M8QPb4knInd8Y5Nu/S6ZJ4pqnvfDOecq3i13RXS9c+LBM0869J0QnyRvNQVpRt0nurwfzqM8+E687jlc0OKlPe3zffxswcZQu1LfVNxufgs2NsDNf65cz8AFG0EQBJ8L/kX6++uUXHsVtcu/xtf7is178w65LndOHPJmiB/eEk9E7vjGJt36XTJPFNU874dzzlW8W+6K6HrnxINnnnToOyE+Sd5qCtKMuk90eT+cR3nwnXjdczitxRvjrb6VpQm2xNmb+qbidvN7ca2HjVr35XoG7jgJgiD4UJS+SH/0uXe9XlXUDNEi3+pkks94t+p6noI0SfgJp+/He/MtEc7Jlm+AZJkk9Vkmip6Bz3gP5Jr0XO1QsRGfXnGuUuXhDol/5aHXkmImSUl270E9PY1zWryrKt7WWBVbet6e+priCfPbcaGHLSqvkusZuOMkCILgQ1H6Iv1h522lomaIFvlWJ5N8xrtV1/MUpEnCTzh9P96bb4lwTrZ8AyTLJKnPMlH0DHzGeyDXpOdqh4qN+PSKc5UqD3dI/CsPvZYUM0lKsnsP6ulpHNLiRfXwzt44drW9N/U1xRPmT+B0D3OJF8o11O84CYIg+Fz0vkvJ9aqiZogW+VYnk3zGu1XX8xSkScJPOH0/3ptviXBOtnwDJMskqc8yUfQMfMZ7INek52qHio349IpzlSoPd0j8Kw+9lhQzSUqyew/q6Wmc0OItTfDm9gg2dr439R3FQ+ZP4GgPQ/7XyjXUr5kJgiD4UPS+SMn1qqJmiBb5SieTfMa7VdfzFKRJwk84fT/em2+JcE62fAMkyySpzzJR9Ax8xnsg16TnaoeKjfj0inOVKg93SPwrD72WFDNJSrJ7D+rpaWzX4hXN8f4OPTbWvjf1HcVD5g/hXA8T8pfLNdSvmQmCIPhQ9L5IyfWqomaIFvlKJ5N8xrtV1/MUpEnCTzh9P96bb4lwTrZ8AyTLJKnPMlH0DHzGeyDXpOdqh4qN+PSKc5UqD3dI/CsPvZYUM0lKsnsP6ulp7NXi/ezCR5hU2FX7idQXFM+ZP4RDPbSZ30GuoX7NTBAEwYei90VKrlcVNUO0yFc6meQz3q26nqcgTRJ+wun78d58S4RzsuUbIFkmSX2WiaJn4DPeA7kmPVc7VGzEp1ecq1R5uEPiX3notaSYSVKS3XtQT09joxYvZy8+xeeKLbUfSn1B8Zz5czjRQ4/2TeSq0jf9BEEQfCh636LkelVRM0SLfJ+TST7j3arreQrSJOEnnL4f7823RDgnW74BkmWS1GeZKHoGPuM9kGvSc7VDxUZ8esW5SpWHOyT+lYdeS4qZJCXZvQf19DQ2avFy9uJTfK7YUvu51KcVj5o/hBM99GjfRK4qfdNPEATBh6L3LUquVxU1Q7TI9zmZ5DPerbqepyBNEn7C6fvx3nxLhHOy5RsgWSZJfZaJomfgM94DuSY9VztUbMSnV5yrVHm4Q+Jfeei1pJhJUpLde1BPT2OXFm/mBD7I6m/Maz+a+rTiUfPnsL2HBuf7yFWlb/oJgiD4UPS+Rcn1qqJmiBb5PieTfMa7VdfzFKRJwk84fT/em2+JcE62fAMkyySpzzJR9Ax8xnsg16TnaoeKjfj0inOVKg93SPwrD72WFDNJSrJ7D+rpaezS4s2cwAdZ/Y157adTH1U8bf4QtvfQ4HwfuZLuZUtBEAQfit5XKLleVdQM0SJf5mSSz3i36nqegjRJ+Amn78d78y0RzsmWb4BkmST1WSaKnoHPeA/kmvRc7VCxEZ9eca5S5eEOiX/lodeSYiZJSXbvQT09jS1avJanbTeoqhHaEtsxrL2E+w43kvfMn8PeHqqEbyVX0r1sKQiC4EPR+wol10RFbfGva++kl8WrKx7vTTGQLBP+eV6/RTzzXdWJh2Igk2qG+K9m9MwqtZ/3GZWuV+HeiCLxrHj8JM+i/Eyy9JwTn2RrZfDeqipEUemqp6exRYvXwrVKnKUIe9k+BdU+51W8/yu708kh828iV9K9bCkIgpfjz2e88ZFXKw/vf80XS+8rlFwTFbXFW/VOelm8uuLx3hQDyTLhn+f1W8Qz31WdeCgGMqlmiP9qRs+sUvt5n1HpehXujSgSz4rHT/Isys8kS8858Um2VgbvrapCFJWuenoaW7RKnWynrZKfsPr+KJW5pYrPemXnOrlg/oVyXPS+qyAIXo4/H/DG512trPe/6bul9/1JromK2uJ9eie9LF5d8XhvioFkmfDP8/ot4pnvqk48FAOZVDPEfzWjZ1ap/bzPqHS9CvdGFIlnxeMneRblZ5Kl55z4JFsrg/dWVSGKSlc9PY25VqmQQ+Ql/nNu3xm8yV1tfNwrO1TIHfOvkuOi910FQfBy/PmANz7vakUxT7TeB73vT3JNVNQW79M76WXx6orHe1MMJMuEf57XbxHPfFd14qEYyKSaIf6rGT2zSu3nfUal61W4N6JIPCseP8mzKD+TLD3nxCfZWhm8t6oKUVS66ulpzLVKhZzj5xJH3b4teI272thIe+2VnejkmvmXyHHR+66CIHg5/nzAG593tUKYP/frpff9Sa6JitriZXonvSxeXfF4b4qBZJnwz/P6LeKZ76pOPBQDmVQzxH81o2dWqf28z6h0vQr3RhSJZ8XjJ3kW5WeSpeec+CRbK4P3VlUhikpXPT2NuRZv4xx/SeW04fcEr3FXIRs5b76y7YXcNH9fDipC7DUWBMHL8efT3fiwqxXC/LnfLb0vT3JNVNQWL9M76WXx6orHe1MMJMuEf57XbxHPfFd14qEYyKSaIf6rGT2zSu3nfUal61W4N6JIPCseP8mzKD+TLD3nxCfZWhm8t6oKUVS66ulpzLV4G0cluMoFw28I2OHGQjZy3nxl2wu5af6+HFSE2GssCIKX48+nu/FhVyuK+Tu+SXpfnuSaqKgtXq930svi1RWP96YYSJYJ/zyv3yKe+a7qxEMxkEk1Q/xXM3pmldrP+4xK16twb0SReFY8fpJnUX4mWXrOiU+ytTJ4b1UVoqh01dPTmGvxNo5KcJULht8QsMONnWwkPGFvovW25u/LccX7xoIgeDn+fLobH3a1st7/pu8T8oW5hiXXREVt8WK9k14Wr654vDfFQLJM+Od5/RbxzHdVJx6KgUyqGeK/mtEzq9R+3mdUul6FeyOKxLPi8ZM8i/IzydJzTnySrZXBe6uqEEWlq56exlyLt3FUgqtcMPyGgB1u7GQj4Ql71+Q+2vwWuR/8/xn2eguC4OX489FufNLVynr/m75S4Bfmw3r9NVFRW7xS76SXxasrHu9NMZAsE/55Xr9FPPNd1YmHYiCTaob4r2b0zCq1n/cZla5X4d6IIvGsePwkz6L8TLL0nBOfZGtl8N6qKkRR6aqnpzHX4m28icodw+8GknpvLRvZtnu7KffR5jfKkcm93oIgeDnU90Cbgd//6G8V+IX5sF5/TVTUFu/TO+ll8eqKx3tTDCTLhH+e128Rz3xXdeKhGMikmiH+qxk9s0rt531GpetVuDeiSDwrHj/Jsyg/kyw958Qn2VoZvLeqClFUuurpacy1SoWcUDlheOj2DQFf08ZaNrJt93ZT7qPN75W77C0Igpfjz0e78UlXK0+pPvqLhXxbrgHJNVdRu2rSz/td4mHu0zP7rkiTfItn8T3wvGreM/QSqUnukDhR82qSaHl1la7KT7JUnShXRNF78PxeSz31vZH2fEuKR82Tljh/ryXejNdVnOrpacy1SoW8Az7L7S7A17SxmZtUJbbLch9tfq71W646HwTBp+PP5/rpx3z9KlArZOxzv1Xgt+XDEvw1V1G7atLP+13iYe7TM/uuSJN8i2fxPfC8at4z9BKpSe6QOFHzapJoeXWVrspPslSdKFdE0Xvw/F5LPfW9kfZ8S4pHzZOWOH+vJd6M11Wc6ulpzLWqnbwcH2R1I/hr2tXMTaoS22Wtm+Yvy1W1yPzeNoIgeC3+fK6ffszXrwK1QsY+91sFfls+LMFfcxW1qyb9vN8lHuY+PbPvijTJt3gW3wPPq+Y9Qy+RmuQOiRM1ryaJlldX6ar8JEvViXJFFL0Hz++11FPfG2nPt6R41DxpifP3WuLNeF3FqZ6exlyLd3Iz19Dwqz3uR/U1zcvZWPJGV/e1bpq/LFfVIvN72wiC4LX487lufMzVCmH+3G8V+G35sAR/zVXUrpr0836XeJj79My+K9Ik3+JZfA88r5r3DL1EapI7JE7UvJokWl5dpavykyxVJ8oVUfQePL/XUk99b6Q935LiUfOkJc7fa4k343UVp3p6Glu0eC2m52t4c3uH0HhHw3I2lrzR1X2tm+ZvyhGhVetyG0EQvBZ/Pte9j/m69ZBHaX3itwr8gn0Y2V9zFbWrJv283yUe5j49s++KNMm3eBbfA8+r5j1DL5Ga5A6JEzWvJomWV1fpqvwkS9WJckUUvQfP77XUU98bac+3pHjUPGmJ8/da4s14XcWpnp7GFi1ey9O2L+BtjR1F+x21+9lY8i5LLxG6Zv6yHBFatXpbQRB8KP58qHuferj1Td8n8KvyYb3+mquoXfIW1B2vUn1a9emZfVekSb7Fs/geeF417xl6idQkd0icqHk1SbS8ukpX5SdZqk6UK6LoPXh+r6We+t5Ie74lxaPmSUucv9cSb8brKk719DS2aPFaeO3n8G5+7mD+jqr9bCx5l6W5SkPojvn7cj0hsrW3kCAIXog/H+rJpx7Of8eXCfyqfFivv+YqaldN+nm/SzzMfXpm3xVpkm/xLL4HnlfNe4ZeIjXJHRInal5NEi2vrtJV+UmWqhPliih6D57fa6mnvjfSnm9J8ah50hLn77XEm/G6ilM9PY1dWrwZWN05vIOH+4CpN76mjSVv8TOX6AldMP8SubbQ5UKCIAg+DuR7cv3OJNdcRe36b2x/x6tUn1Z9embfFWmSb/EsvgeeV817hl4iNckdEidqXk0SLa+u0lX5SZaqE+WKKHoPnt9rqae+N9Keb0nxqHnSEufvtcSb8bqKUz09jV1avJlqh9txwmoVh6INU5fKmStuN3+olqHKOfMvlJvUNdkNgiD4FwC/J/98YZJrrqJ2/de1v+NVqk+rPj2z74o0ybd4Ft8Dz6vmPUMvkZrkDokTNa8miZZXV+mq/CRL1YlyRRS9B8/vtdRT3xtpz7ekeNQ8aYnz91rizXhdxamensZGLV5OCVu8XfBZwvZQW1JX+xkq7jV/Aaeb34I7chMVsru3kyAIgs8C/J7884VJrrmK2vVf1/6OV6k+rfr0zL4r0iTf4ll8DzyvmvcMvURqkjskTtS8miRaXl2lq/KTLFUnyhVR9B48v9dST31vpD3fkuJR86Qlzt9riTfjdRWnenoaG7V4OT1sMXnBJ8GuLHtTV/sZKu41fxoXmt+CO3JDlcudBEEQfBbIl+T6hUmulQqfXGd6rryi8uCfkkS9FD4LacAz95Lyp+QOcVJVV3fWp4Szms7Pqxmiotz2WiLO/bXnrKr4pFxrVfFsfsb7Ic3wp9XG/JZnIG1U1f2M97wXe7V4P218hMmnmKc4kbpR0URxr/nTuNP8HBfk5o3NGYIgCL4Y8Evyz7cluVYqfHKd6bnyisqDf0oS9VL4LKQBz9xLyp+SO8RJVV3dWZ8Szmo6P69miIpy22uJOPfXnrOq4pNyrVXFs/kZ74c0w59WG/NbnoG0UVX3M97zXmzX4hVN8P4OPXa1vTd1o6KJ4l7zR9Hv/V/9/wxzhr21BEEQfBDgl+Sfb0tyrVT45DrTc+UVlQf/lCTqpfBZSAOeuZeUPyV3iJOqurqzPiWc1XR+Xs0QFeW21xJx7q89Z1XFJ+Vaq4pn8zPeD2mGP6025rc8A2mjqu5nvOe92K7FKxri/R0abCx8Y+peS23FveYPod94xfxc5abcXIIw7K0lCILggwC/JP98W5JrpcIn15meK6+oPPinJFEvhc9CGvDMvaT8KblDnFTV1Z31KeGspvPzaoaoKLe9lohzf+05qyo+KddaVTybn/F+SDP8abUxv+UZSBtVdT/jPe/FCS3e0hBvbs9gb+G7UvdaaivuNX8I/cYr5ucq1+R29baLJwiC4PsAvyH/fFWSa6XCJ9eZniuvqDz4pyRRL4XPQhrwzL2k/Cm5Q5xU1dWd9SnhrKbz82qGqCi3vZaIc3/tOasqPinXWlU8m5/xfkgz/Gm1Mb/lGUgbVXU/4z3vxSEtXtQQ7+zNYHvhW1K3i+op7jV/FP3e8/8ZDvMEQRB8H+A35J+vSnKtVPjkOtNz5RWVB/+UJOql8FlIA565l5Q/JXeIk6q6urM+JZzVdH5ezRAV5bbXEnHurz1nVcUn5VqrimfzM94PaYY/rTbmtzwDaaOq7me85704qsXrmuA9XXmcaHueul1UT3Gv+Qu41vwEp+V28ROevc0EQRB8CuA35J+vSnKtVPjkOtNz5RWVB/+UJOql8FlIA565l5Q/JXeIk6q6urM+JZzVdH5ezRAV5bbXEnHurz1nVcUn5VqrimfzM94PaYY/rTbmtzwDaaOq7me85704rcUbm+ANLXkcanuYetJVY3ev+Tu41nwbR+X2trSXLQiC4GsAvx7/fE+Sa6XCJ9eZniuvqDz4pyRRL4XPQhrwzL2k/Cm5Q5xU1dWd9SnhrKbz82qGqCi3vZaIc3/tOasqPinXWlU8m5/xfkgz/Gm1Mb/lGUgbVXU/4z3vxR0t3lsbb2XmKc5VPUk96aqxu9f8Ndxpvo2jcnsr2ssWBEHwNYBfj3++J8m1UuGT60zPlVdUHvxTkqiXwmchDXjmXlL+lNwhTqrq6s76lHBW0/l5NUNUlNteS8S5v/acVRWflGutKp7Nz3g/pBn+tNqY3/IMpI2qup/xnvfiptZ/KgVW8Q4eOM41PEndXny4vrEE7uEO7jTfw1G5veSEbW85QRAEHwH49fjne5JcKxU+uc70XHlF5cE/JYl6KXwW0oBn7iXlT8kd4qSqru6sTwlnNZ2fVzNERbnttUSc+2vPWVXxSbnWquLZ/Iz3Q5rhT6uN+S3PQNqoqvsZ73kvbmr9Ed2O16qXcLTedurJ7rq+sQRo4BquNd/AOblXlbO3nyAIgvcH/G788yVJrr2Kmidfzp6NuCXMvrGe53lG4sdnUc1M+FctlZdrEeberurEQzGQSTVD/FczemaV2s/7jErXq3BvRJF4Vjx+kmdRfnpZyPzEs0rdc+sd+q524aaWMbAFGxWPRn4JJql7r2BjyRPzw0RvaP5VctubOcQZBEHw6YDfjX++JMm1V1Hz5MvZsxG3hNk31vM8z0j8+CyqmQn/qqXyci3C3NtVnXgoBjKpZoj/akbPrFL7eZ9R6XoV7o0oEs+Kx0/yLMpPLwuZn3hWqXtuvUPf1S7c1PLg3T6tfS50Oux9DFM3+t9Y8tD8RpWG4h3zF+ROMBPOvf0EQUAw/CTCjzZh3kLycegVSK69iponnXs24pYw+8Z6nucZiR+fRTUz4V+1VF6uRZh7u6oTD8VAJtUM8V/N6JlVaj/vMypdr8K9EUXiWfH4SZ5F+ellIfMTzyp1z6136LvahZtaELxk8ip7/Kcz3scwdeMVbCx5aH6vUFXxmvmjcns7qdLurSgIAoVdH0b+0X7KvIXk49BLTa69iponbXs24pYw+8Z6nucZiR+fRTUz4V+1VF6uRZh7u6oTD8VAJtUM8V/N6JlVaj/vMypdr8K9EUXiWfH4SZ5F+ellIfMTzyp1z6136LvahZtaVfC2zavs0Z6Odh/z1NVXsLHkufm9WiXRm+bPyZVqOYGNFQVB8BAbP5IbP+m7eD4Lvbzk2quoedKzZyNuCbNvrOd5npH48VlUMxP+VUvl5VqEuberOvFQDGRSzRD/1YyeWaX28z6j0vUq3BtRJJ4Vj5/kWZSfXhYyP/GsUvfceoe+q124qdUD7xwG2cXzWZinrr6FjSXPzZdQTfpx5g/RHsXGioIgWLH3U7nxw76L57PQC0uuvYqaJyV7NuKWMPvGep7nGYkfn0U1M+FftVRerkWYe7uqEw/FQCbVDPFfzeiZVWo/7zMqXa/CvRFF4lnx+EmeRfnpZSHzE88qdc+td+i72oWbWm3w2kmWLSQfh2vV/Tz7imiUvMV8CaWkb2X+hBxs4zQ2thQEwW9s/1Ru/KRv9/YR6IUl115FzZOSPRtxS5h9Yz3P84zEj8+impnwr1oqL9cizL1d1YmHYiCTaob4r2b0zCq1n/cZla5X4d6IIvGsePwkz6L89LKQ+Ylnlbrn1jv0Xe3CTa0Jqs0PqS4kuoxr1f2XamPJW8yXwGO+m/kTcrCN09jYUhAEv0E+caVPJf/wVnUnET4IT74NRVJy7VXUvNclbMQtYfaN9TzPMxI/PotqZsK/aqm8XIsw93ZVJx6KgUyqGeK/mtEzq9R+3mdUul6FeyOKxLPi8ZM8i/LTy0LmJ55V6p5b79B3tQtHtfYyl5of8my0/SbYlZq/hY0l7zJfAk/6VuZPyMEqTmNjS0EQ/BelzxocLn14Ped2bx8B+rX4v0nJtVdR816XsBG3hNk31vM8z0j8+CyqmQn/qqXyci3C3NtVnXgoBjKpZoj/akbPrFL7eZ9R6XoV7o0oEs+Kx0/yLMpPLwuZn3hWqXtuvUPf1S7MtUgPW1Bqfsiz0fabYFdq+Bb2lryXbaMo0b1s/oQcrOICNhYVBMF/Wr/NycqQs031NV8aha/FXzHJtVdR816XsBG3hNk31vM8z0j8+CyqmQn/qqXyci3C3NtVnXgoBjKpZoj/akbPrFL7eZ9R6XoV7o0oEs+Kx0/yLMpPLwuZn3hWqXtuvUPf1S5MtHgPG63OFS/bfhNsTA1fxMaS97LtjXmN56bnBucdbGkpCIL/ovEpIytDzkNUH4TaN+O+/8/gOf2umlevw7+glbOaXc1Un/J0hLmavdcYz8vbI3nVvGfoJVKT3CFxoubVJNHy6ipdlZ9kqTpRroii9+D5vVa1MTI/b6mqzp1zdd/PunUCE61SFRvdDuUue34TbExNqPaWvJdtb8xrPDc9NzjvYEtLQRD8F72P2NOtKq2Z30j1Qeh9N5JrrlLdVfPqdfgXtHJWs6uZ6lOejjBXs/ca43l5eySvmvcMvURqkjskTtS8miRaXl2lq/KTLFUnyhVR9B48v9eqNkbm5y1V1blzru77WbdOYKJVqmKj26HcZc9vgr2pCdtluXolezJe47npucF5DVuKCoLg/9H7fD3dqtKa+YbDL/jS6H0xkmuuUt1V8+pd+Lezclazq5nqU56OMFez9xrjeXl7JK+a9wy9RGqSOyRO1LyaJFpeXaWr8pMsVSfKFVH0Hjy/16o2RubnLVXVuXOu7vtZt05golWqYqPbodxlz2+CvakJ22W5eiV7Ml7juem5SngTW4oKguA/s9/jfrfKbOYbJr/gS6P3xUiuuUp1V82rd+HfzspZza5mqk95OsJczd5rjOfl7ZG8at4z9BKpSe6QOFHzapJoeXWVrspPslSdKFdE0Xvw/F6r2hiZn7dUVefOubrvZ906gaEWr2Kj26HcZc9vgu2pCeEuue3mNwa8xnPTc5XwJrYUFQTBf97m/zP4j3nD5Bd8afS+GMk1V6nuqnn1LvzbWTmr2dVM9SlPR5ir2XuN8by8PZJXzXuGXiI1yR0SJ2peTRItr67SVflJlqoT5Yooeg+e32tVGyPz85aq6tw5V/f9rFsnMNQqtbHF6lzrmuG3wvbU8HVskdtufmO6jWzXbG8nvIx5UUEQ/Oc9/j/D0w94w+QXfGP0vhXJNVep7qp59S7821k5q9nVTPUpT0eYq9l7jfG8vD2SV817hl4iNckdEidqXk0SLa+u0lX5SZaqE+WKKHoPnt9rVRsj8/OWqurcOVf3/axbJzDUqhYytzoXuuP23XAiNXwjc7kT5ndF28h2x/N2wvsYFhUEwf9j8snyuxs/4A2TX/CN0SuNXHOV6q6aV+/Cv52Vs5pdzVSf8nSEuZq91xjPy9sjedW8Z+glUpPcIXGi5tUk0fLqKl2Vn2SpOlGuiKL34Pm9VrUxMj9vqarOnXN138+6dQJDrWoh7yB0we0b4kRq/lKGcifMb4m2l22YYrvEBc8vVwyCfxmTT5bfhZ9l8ulumPyCb4xeb+Saq1R31bx6F/7trJzV7Gqm+pSnI8zV7L3GeF7eHsmr5j1DL5Ga5A6JEzWvJomWV1fpqvwkS9WJckUUvQfP77WqjZH5eUtVde6cq/t+1q0TmGtVO3m5ymmr74lDqfl7mchdfmXbI5zo5ILEac93UgRBoDD5WPld/ll+aqBh8gu+LnrVkWuuUt1V8+pd+Lezclazq5nqU56OMFez9xrjeXl7JK+a9wy9RGqSOyRO1LyaJFpeXaWr8pMsVSfKFVH0Hjy/16o2RubnLVXVuXOu7vtZt05grsU7aQvt5T/n851xKDV/NRO5m6/sRIRDtbyD54bb99QNgn8Qk4+V34UfZKLeMPkFXxe99sg1V6nuqnn1LvzbWTmr2dVM9SlPR5ir2XuN8by8PZJXzXuGXiI1yR0SJ2peTRItr67SVflJlqoT5Yooeg+e32tVGyPz85aq6tw5V/f9rFsnMNfinfS0tpOfMPn+OJe69IJ6ctde2Wsj9LIc8nzC6jvrBsE/iMnHyu/CDzLRbZj8gq+LUoE/+lS8Xq8qfkbN97bWa59FPSXeuH/fj3LldRUzSXRid3VL0vmMnt979ncIz7rbYya98X566nvZqll8LjKzXvunnpno+ry9ZngbXkU1QJyo7Mqb51czxPkuzLV4J6rVjcy7aCeFvCfOpW68pqrcG5qvKh7iP+d5O+Fe3RPSQfCvYfKZ8rulp166YfILviv4N+HvjOR6VfEzar63tV77LOop8cb9+36UK6+rmEmiE7urW5LOZ/T83rO/Q3jW3R4z6Y3301Pfy1bN4nORmfXaP/XMRNfn7TXD2/AqqgHiRGVX3jy/miHOd2GLFq9FFbWF7YLb7Rg2z3HUzOnsPf7TuFDRQ6ELnveylbA9SxAECpPPlN99ysw/0Q2TX/Bdwb8Jf2ck16uKn1Hzva312mdRT4k37t/3o1x5XcVMEp3YXd2SdD6j5/ee/R3Cs+72mElvvJ+e+l62ahafi8ys1/6pZya6Pm+vGd6GV1ENECcqu/Lm+dUMcb4Lu7R4M+fwQVb/i3nzG1MfJZ/I9fhP405Le7HXarWBj1APgn8KW74ierRQuuHwC74r4Nfgn4zkelXxM2q+t7Ve+yzqKfHG/ft+lCuvq5hJohO7q1uSzmf0/N6zv0N41t0eM+mN99NT38tWzeJzkZn12j/1zETX5+01w9vwKqoB4kRlV948v5ohzndhlxZv5hA+yOpvzJvfmPo0f1uuQX4adyp6lefthIeKOmQgCP4d9D5TT7cILfxEVx1+x7cE/xr8HZNcryp+Rs33ttZrn0U9Jd64f9+PcuV1FTNJdGJ3dUvS+Yye33v2dwjPuttjJr3xfnrqe9mqWXwuMrNe+6eemej6vL1meBteRTVAnKjsypvnVzPE+S5s1OLlnMCn+PyDLc3vSn2avy3XID+Nay3d97yd8FxLhwwEwb+DxseKrEBOMja093T+PcG/Bn/HJNerip9R872t9dpnUU+JN+7f96NceV3FTBKd2F3dknQ+o+f3nv0dwrPu9phJb7yfnvpetmoWn4vMrNf+qWcmuj5vrxnehldRDRAnKrvy5vnVDHG+C3u1eD978REmH2JX81tSX5DoydV7PYtrFb3E83bCBt7BQxD8C2h8rMgK59xI1Zt/T8DvwD8xyfWq4mfUfG9rvfZZ1FPijfv3/ShXXlcxk0Qndle3JJ3P6Pm9Z3+H8Ky7PWbSG++np76XrZrF5yIz67V/6pmJrs/ba4a34VVUA8SJyq68eX41Q5zvwl4t3s9GfIRJhV3Nb0l9QaInV+/1IC63dN/zCc4qDkULgmBF6WMFhzcSlj7yX/P9wL8Dfycl16uKn1Hzva312mdRT4k37t/3o1x5XcVMEp3YXd2SdD6j5/ee/R3Cs+72mElvvJ+e+l62ahafi8ys1/6pZya6Pm+vGd6GV1ENECcqu/Lm+dUMcb4L27V4Rbvw/g4NNjY/T31HpSHXqvYI5hW9JM4Jb1uqeHMbQfCPgHy4Sh/A0ue0pDuJ8EF4/t33KCm5XlX8jJrvba3XPgt/pxP/vh/lyusqZpLoxO7qlqTzGT2/9+zvEJ51t8dMeuP99NT3slWz+FxkZr32Tz0z0fV5e83wNryKaoA4UdmVN8+vZojzXTihxVua483tPcXe5oep76g05FrV7se8n5ckOmFsYxXv7yQIvh7db5cj/5/hp3vuKom+OXphyfWq4mfUfG9rvfZZ+Gud+Pf9KFdeVzGTRCd2V7cknc/o+b1nf4fwrLs9ZtIb76envpetmsXnIjPrtX/qmYmuz9trhrfhVVQDxInKrrx5fjVDnO/CIS1e1ATv7A1ib+3D1NeEqnLddndiSzn3Qx1ytb2NtpM7ZoLg67H366X6ITXzDWNQ9J3RC0uuvQqf987JHf/KVj/zeZLUZyTpiAev6K8JJ88yT0SyeCded9IST+Gd8wZUonXSO/SeCUM1Hb8/d1LV8g6Jeq+f+VbVoVfpKaos69YJHNXidVXxnq4a2FX1ltTXhKpy3Xa3YUszl3MdtbS3h4mTO2aC4F/Axm+Y6ofU8Jdcfc3XQi8vufYqfN47J3f8W1v9zOdJUp+RpCMevKK/Jpw8yzwRyeKdeN1JSzyFd84bUInWSe/QeyYM1XT8/txJVcs7JOq9fuZbVYdepaeosqxbJ3BaizdW7fatLLWxpeRdqW9qleQGBU+xq5PL0Y6a2dvAB/kJgq/Hrg9d40OqVqClL/tC6KUm116Fz3vn5I5/d6uf+TxJ6jOSdMSDV/TXhJNnmSciWbwTrztpiafwznkDKtE66R16z4Shmo7fnzupanmHRL3Xz3yr6tCr9BRVlnXrBO5o8d5Iq29iZgu2JNqV+qZWSW5QcB+72rgc8IKNXal3+blpKQj+EQw/aI1FJfdvfg/w1GtX/tqr8HnvnNzxb3D1M58nSX1Gko548Ir+mnDyLPNEJIt34nUnLfEU3jlvQCVaJ71D75kwVNPx+3MnVS3vkKj3+plvVR16lZ6iyrJuncBNrf9UClzLDIKvR+8Dks9IEATBN6H3J4BcexU+752TO/4P2epnPk+S+owkHfHgFf014eRZ5olIFu/E605a4im8c96ASrROeofeM2GopuP3506qWt4hUe/1M9+qOvQqPUWVZd06gZtaxsDD9oLgHwf5kgmCIAi+DPy0qc6T/CzqedS8d07u+D9qq5/5PEnqM5J0xINX9NeEk2eZJyJZvBOvO2mJp/DOeQMq0TrpHXrPhKGajt+fO6lqeYdEvdfPfKvq0Kv0FFWWdesEbmoFQRAEQRAEHvy0qc6T/CxaPbv6cyM50/oUyv98niT1GUk64sEr+mvCybPME5Es3onXnbTEU3jnvAGVaJ30Dr1nwlBNx+/PnVS1vEOi3utnvlV16FV6iirLunUCN7WCIAiCIAgCD37aVOdJfhatnl39uZGcaX0K5X8+T5L6jCQd8eAV/TXh5FnmiUgW78TrTlriKbxz3oBKtE56h94zYaim4/fnTqpa3iFR7/Uz36o69Co9RZVl3TqBm1pBEARBEASBBz9tqvMkP4tWz67+3EjOtD6F8j+fJ0l9RpKOePCK/ppw8izzRCSLd+J1Jy3xFN45b0AlWie9Q++ZMFTT8ftzJ1Ut75Co9/qZb1UdepWeosqybp3ATa0gCIIgCILAg5821XmSn0WrZ1d/biRnWp9C+Z/Pk6Q+I0lHPHhFf004eZZ5IpLFO/G6k5Z4Cu+cN6ASrZPeofdMGKrp+P25k6qWd0jUe/3Mt6oOvUpPUWVZt07gplYQBEEQBEHgwU+b6jzJz6IrD2Ejfrz6yuZnvGevTtwS//7aZ5k7ITzKufdP+P0kucPVe42tbN6tb8mrEP/ECWmM5K02QxgUZ68ln5G3pBTJDOl/nfGN+Z65Ik9NdLnz07ipFQRBEARBEHjw06Y6T/Kz6MpD2Igfr76y+Rnv2asTt8S/v/ZZ5k4Ij3Lu/RN+P0nucPVeYyubd+tb8irEP3FCGiN5q80QBsXZa8ln5C0pRTJD+l9nfGO+Z67IUxNd7vw0bmoFQRAEQRAEHvy0qc6T/Cy68hA24serr2x+xnv26sQt8e+vfZa5E8KjnHv/hN9PkjtcvdfYyubd+pa8CvFPnJDGSN5qM4RBcfZa8hl5S0qRzJD+1xnfmO+ZK/LURJc7P42bWkEQBEEQBIEHP22q8yQ/i648hI348eorm5/xnr06cUv8+2ufZe6E8Cjn3j/h95PkDlfvNbayebe+Ja9C/BMnpDGSt9oMYVCcvZZ8Rt6SUiQzpP91xjfme+aKPDXR5c5P46ZWEARBEARB4MFPm+o8yc+iKw9hI368+srmZ7xnr07cEv/+2meZOyE8yrn3T/j9JLnD1XuNrWzerW/JqxD/xAlpjOStNkMYFGevJZ+Rt6QUyQzpf53xjfmeuSJPTXS589O4qRUEQRAEQRB48NOmOk/ys+jKQ9iIH6++svkZ79mrE7fEv7/2WeZOCI9y7v0Tfj9J7nD1XmMrm3frW/IqxD9xQhojeavNEAbF2WvJZ+QtKUUyQ/pfZ3xjvmeuyFMTXe78NG5qBUEQBEEQBB78tKnOk/wsuvIQNuLHq69sfsZ79urELfHvr32WuRPCo5x7/4TfT5I7XL3X2Mrm3fqWvArxT5yQxkjeajOEQXH2WvIZeUtKkcyQ/tcZ35jvmSvy1ESXOz+Nm1pBEARBEASBBz9tqvMkP4uuPISN+PHqK5uf8Z69OnFL/Ptrn2XuhPAo594/4feT5A5X7zW2snm3viWvQvwTJ6QxkrfaDGFQnL2WfEbeklIkM6T/dcY35nvmijw10eXOT+OmVhAEQRAEQeDBT5vqPMnPoisPYSN+vPrK5me8Z69O3BL//tpnmTshPMq590/4/SS5w9V7ja1s3q1vyasQ/8QJaYzkrTZDGBRnryWfkbekFMkM6X+d8Y35nrkiT010ufPTuKkVBEEQBEEQePDTpjpP8rPoykPYiB+vvrL5Ge/ZqxO3xL+/9lnmTgiPcu79E34/Se5w9V5jK5t361vyKsQ/cUIaI3mrzRAGxdlryWfkLSlFMkP6X2d8Y75nrshTE13u/DRuagVBEARBEAQe/LSpzpP8LKrYFI+6Q1x5dX+fsCl+0rPnr3KSedKAnyRsvaQqi5/xrhQn6YGn82zKM2foOeGtes8Tb94n90P4FRtRV/xVtnlef1859+rrU+9TgbvlGU/gplYQBEEQBEHgwU+b6jzJz6KKTfGoO8SVV/f3CZviJz17/ionmScN+EnC1kuqsvgZ70pxkh54Os+mPHOGnhPeqvc88eZ9cj+EX7ERdcVfZZvn9feVc6++PvU+FbhbnvEEbmoFQRAEQRAEHvy0qc6T/Cyq2BSPukNceXV/n7ApftKz569yknnSgJ8kbL2kKouf8a4UJ+mBp/NsyjNn6DnhrXrPE2/eJ/dD+BUbUVf8VbZ5Xn9fOffq61PvU4G75RlP4KZWEARBEARB4MFPm+o8yc+iik3xqDvElVf39wmb4ic9e/4qJ5knDfhJwtZLqrL4Ge9KcZIeeDrPpjxzhp4T3qr3PPHmfXI/hF+xEXXFX2Wb5/X3lXOvvj71PhW4W57xBG5qBUEQBEEQBB78tKnOk/wsqtgUj7pDXHl1f5+wKX7Ss+evcpJ50oCfJGy9pCqLn/GuFCfpgafzbMozZ+g54a16zxNv3if3Q/gVG1FX/FW2eV5/Xzn36utT71OBu+UZT+CmVhAEQRAEQeDBT5vqPMnPoopN8ag7xJVX9/cJm+InPXv+KieZJw34ScLWS6qy+BnvSnGSHng6z6Y8c4aeE96q9zzx5n1yP4RfsRF1xV9lm+f195Vzr74+9T4VuFue8QRuagVBEARBEAQe/LSpzpP8LKrYFI+6Q1x5dX+fsCl+0rPnr3KSedKAnyRsvaQqi5/xrhQn6YGn82zKM2foOeGtes8Tb94n90P4FRtRV/xVtnlef1859+rrU+9TgbvlGU/gplYQBEEQBEHgwU+b6jzJz6KKTfGoO8SVV/f3CZviJz17/ionmScN+EnC1kuqsvgZ70pxkh54Os+mPHOGnhPeqvc88eZ9cj+EX7ERdcVfZZvn9feVc6++PvU+FbhbnvEEbmoFQRAEQRAEHvy0qc6T/Cyq2BSPukNceXV/n7ApftKz569yknnSgJ8kbL2kKouf8a4UJ+mBp/NsyjNn6DnhrXrPE2/eJ/dD+BUbUVf8VbZ5Xn9fOffq61PvU4G75RlP4KZWEARBEARB4MFPm+o8yc+iik3xqDvElVf39wmb4ic9e/4qJ5knDfhJwtZLqrL4Ge9KcZIeeDrPpjxzhp4T3qr3PPHmfXI/hF+xEXXFX2Wb5/X3lXOvvj71PhW4W57xBG5qBUEQBEEQBB78tKnOk/wsWj3BVp8SZuWBaFUZ/KTiVLsk1ySL1/UeSCd8VzknT0lexen9cw97Vdb7VU7ylGTxioSnl6uq7hXJ1pqCd04aJk52tdpj8A5XHjXj+9mLm1pBEARBEASBBz9tqvMkP4tWT7DVp4RZeSBaVQY/qTjVLsk1yeJ1vQfSCd9VzslTkldxev/cw16V9X6VkzwlWbwi4enlqqp7RbK1puCdk4aJk12t9hi8w5VHzfh+9uKmVhAEQRAEQeDBT5vqPMnPotUTbPUpYVYeiFaVwU8qTrVLck2yeF3vgXTCd5Vz8pTkVZzeP/ewV2W9X+UkT0kWr0h4ermq6l6RbK0peOekYeJkV6s9Bu9w5VEzvp+9uKkVBEEQBEEQePDTpjpP8rNo9QRbfUqYlQeiVWXwk4pT7ZJckyxe13sgnfBd5Zw8JXkVp/fPPexVWe9XOclTksUrEp5erqq6VyRbawreOWmYONnVao/BO1x51IzvZy9uagVBEARBEAQe/LSpzpP8LFo9wVafEmblgWhVGfyk4lS7JNcki9f1HkgnfFc5J09JXsXp/XMPe1XW+1VO8pRk8YqEp5erqu4VydaagndOGiZOdrXaY/AOVx414/vZi5taQRAEQRAEgQc/barzJD+LVk+w1aeEWXkgWlUGP6k41S7JNcnidb0H0gnfVc7JU5JXcXr/3MNelfV+lZM8JVm8IuHp5aqqe0WytabgnZOGiZNdrfYYvMOVR834fvbiplYQBEEQBEHgwU+b6jzJz6LVE2z1KWFWHohWlcFPKk61S3JNsnhd74F0wneVc/KU5FWc3j/3sFdlvV/lJE9JFq9IeHq5qupekWytKXjnpGHiZFerPQbvcOVRM76fvbipFQRBEARBEHjw06Y6T/KzaPUEW31KmJUHolVl8JOKU+2SXJMsXtd7IJ3wXeWcPCV5Faf3zz3sVVnvVznJU5LFKxKeXq6qulckW2sK3jlpmDjZ1WqPwTtcedSM72cvbmoFQRAEQRAEHvy0qc6T/CxaPcFWnxJm5YFoVRn8pOJUuyTXJIvX9R5IJ3xXOSdPSV7F6f1zD3tV1vtVTvKUZPGKhKeXq6ruFcnWmoJ3ThomTna12mPwDlceNeP72YubWkEQBEEQBIEHP22q8yQ/i1ZPsNWnhFl5IFpVBj+pONUuyTXJ4nW9B9IJ31XOyVOSV3F6/9zDXpX1fpWTPCVZvCLh6eWqqntFsrWm4J2ThomTXa32GLzDlUfN+H724qZWEARBEARB4MFPm+o8yc+iK49i8OpKizsnil6Ft1r1zFslrnrpyBZvhvB4RdKJ39qVi/P47L63OVs1764sfEtdV1Os89VOqu/ivoq643VXh9ytV/TOfdJzuKkVBEEQBEEQePDTpjpP8rPoyqMYvLrS4s6JolfhrVY981aJq146ssWbITxekXTit3bl4jw+u+9tzlbNuysL31LX1RTrfLWT6ru4r6LueN3VIXfrFb1zn/QcbmoFQRAEQRAEHvy0qc6T/Cy68igGr660uHOi6FV4q1XPvFXiqpeObPFmCI9XJJ34rV25OI/P7nubs1Xz7srCt9R1NcU6X+2k+i7uq6g7Xnd1yN16Re/cJz2Hm1pBEARBEASBBz9tqvMkP4uuPIrBqyst7pwoehXeatUzb5W46qUjW7wZwuMVSSd+a1cuzuOz+97mbNW8u7LwLXVdTbHOVzupvov7KuqO110dcrde0Tv3Sc/hplYQBEEQBEHgwU+b6jzJz6Irj2Lw6kqLOyeKXoW3WvXMWyWueunIFm+G8HhF0onf2pWL8/jsvrc5WzXvrix8S11XU6zz1U6q7+K+irrjdVeH3K1X9M590nO4qRUEQRAEQRB48NOmOk/ys+jKoxi8utLizomiV+GtVj3zVomrXjqyxZshPF6RdOK3duXiPD67723OVs27KwvfUtfVFOt8tZPqu7ivou543dUhd+sVvXOf9BxuagVBEARBEAQe/LSpzpP8LLryKAavrrS4c6LoVXirVc+8VeKql45s8WYIj1cknfitXbk4j8/ue5uzVfPuysK31HU1xTpf7aT6Lu6rqDted3XI3XpF79wnPYebWkEQBEEQBIEHP22q8yQ/i648isGrKy3unCh6Fd5q1TNvlbjqpSNbvBnC4xVJJ35rVy7O47P73uZs1by7svAtdV1Nsc5XO6m+i/sq6o7XXR1yt17RO/dJz+GmVhAEQRAEQeDBT5vqPMnPoiuPYvDqSos7J4pehbda9cxbJa566cgWb4bweEXSid/alYvz+Oy+tzlbNe+uLHxLXVdTrPPVTqrv4r6KuuN1V4fcrVf0zn3Sc7ipFQRBEARBEHjw06Y6T/Kz6MqjGLy60uLOiaJX4a1WPfNWiateOrLFmyE8XpF04rd25eI8Prvvbc5WzbsrC99S19UU63y1k+q7uK+i7njd1SF36xW9c5/0HG5qBUEQBEEQBB78tKnOk/ws6tkIj2KrbvGn5E5UovK5KoTN3/c86/3qPO+E86x+FLPaVYm4W98t74F361UUfCI/4z3sxU2tIAiCIAiCwIOfNtV5kp9FPRvhUWzVLf6U3IlKVD5XhbD5+55nvV+d551wntWPYla7KhF367vlPfBuvYqCT+RnvIe9uKkVBEEQBEEQePDTpjpP8rOoZyM8iq26xZ+SO1GJyueqEDZ/3/Os96vzvBPOs/pRzGpXJeJufbe8B96tV1HwifyM97AXN7WCIAiCIAgCD37aVOdJfhb1bIRHsVW3+FNyJypR+VwVwubve571fnWed8J5Vj+KWe2qRNyt75b3wLv1Kgo+kZ/xHvbiplYQBEEQBEHgwU+b6jzJz6KejfAotuoWf0ruRCUqn6tC2Px9z7Per87zTjjP6kcxq12ViLv13fIeeLdeRcEn8jPew17c1AqCIAiCIAg8+GlTnSf5WdSzER7FVt3iT8mdqETlc1UIm7/vedb71XneCedZ/ShmtasScbe+W94D79arKPhEfsZ72IubWkEQBEEQBIEHP22q8yQ/i3o2wqPYqlv8KbkTlah8rgph8/c9z3q/Os874TyrH8WsdlUi7tZ3y3vg3XoVBZ/Iz3gPe3FTKwiCIAiCIPDgp011nuRnUc9GeBRbdYs/JXeiEpXPVSFs/r7nWe9X53knnGf1o5jVrkrE3fpueQ+8W6+i4BP5Ge9hL25qBUEQBEEQBB78tKnOk/ws6tkIj2KrbvGn5E5UovK5KoTN3/c86/3qPO+E86x+FLPaVYm4W98t74F361UUfCI/4z3sxU2tIAiCIAiCwIOfNtV5kp9FPRvhUWzVLf6U3IlKVD5XhbD5+55nvV+d551wntWPYla7KhF367vlPfBuvYqCT+RnvIe9uKkVBEEQBEEQePDTpjpP8rMoP7vyXXWHe+ZOFI+HV1S6ft7n8k48p1chuYgr0oDfVSpVJ1VXhG0yP5n0/smdiecqg+chKbxKzyFpZt4AmfdOFLPvzbvyPFXnxP9e3NQKgiAIgiAIPPhpU50n+VmUn135rrrDPXMnisfDKypdP+9zeSee06uQXMQVacDvKpWqk6orwjaZn0x6/+TOxHOVwfOQFF6l55A0M2+AzHsnitn35l15nqpz4n8vbmoFQRAEQRAEHvy0qc6T/CzKz658V93hnrkTxePhFZWun/e5vBPP6VVILuKKNOB3lUrVSdUVYZvMTya9f3Jn4rnK4HlICq/Sc0iamTdA5r0Txex78648T9U58b8XN7WCIAiCIAgCD37aVOdJfhblZ1e+q+5wz9yJ4vHwikrXz/tc3onn9CokF3FFGvC7SqXqpOqKsE3mJ5PeP7kz8Vxl8DwkhVfpOSTNzBsg896JYva9eVeep+qc+N+Lm1pBEARBEASBBz9tqvMkP4vysyvfVXe4Z+5E8Xh4RaXr530u78RzehWSi7giDfhdpVJ1UnVF2Cbzk0nvn9yZeK4yeB6Swqv0HJJm5g2Qee9EMfvevCvPU3VO/O/FTa0gCIIgCILAg5821XmSn0X52ZXvqjvcM3eieDy8otL18z6Xd+I5vQrJRVyRBvyuUqk6qboibJP5yaT3T+5MPFcZPA9J4VV6Dkkz8wbIvHeimH1v3pXnqTon/vfiptZLYF7xw5eutsgwESXrvRSlOMOZknky3HNI2ijFIX56Vnet37RBijIzpS0DIkEeDWdKKXZVt91hb8aol0ogPKVahimMxHC95NAwG55hUcNaShJBA+Rfxdo/ufYqfp7vqjvcM3eieDy8otL18z6Xd+I5vQrJRVyRBvyuUqk6qboibJP5yaT3T+5MPFcZPA9J4VV6Dkkz8wbIvHeimH1v3pXnqTon/vfiptZLYF7xw5eutsgwESXrvRSlOMOZknky3HNI2ijFIX56Vnet37RBijIzpS0DIkEeDWdKKXZVt91hb8aol0ogPKVahimMxHC95NAwG55hUcNaShJBA+Rfxdo/ufYqfp7vqjvcM3eieDy8otL18z6Xd+I5vQrJRVyRBvyuUqk6qboibJP5yaT3T+5MPFcZPA9J4VV6Dkkz8wbIvHeimH1v3pXnqTon/vfiptZLYF7xw5eutsgwESXrvRSlOMOZknky3HNI2ijFIX56Vnet37RBijIzpS0DIkEeDWdKKXZVt91hb8aol0ogPKVahimMxHC95NAwG55hUcNaShJBA+Rfxdo/ufYqfp7vqjvcM3eieDy8otL18z6Xd+I5vQrJRVyRBvyuUqk6qboibJP5yaT3T+5MPFcZPA9J4VV6Dkkz8wbIvHeimH1v3pXnqTon/vfiptZLYF7xw5eutsgwESXrvRSlOMOZknky3HNI2ijFIX56Vnet37RBijIzpS0DIkEeDWdKKXZVt91hb8aol0ogPKVahimMxHC95NAwG55hUcNaShJBA+Rfxdo/ufYqfp7vqjvcM3eieDy8otL18z6Xd+I5vQrJRVyRBvyuUqk6qboibJP5yaT3T+5MPFcZPA9J4VV6Dkkz8wbIvHeimH1v3pXnqTon/vfiptZLYF7xw5eutsgwESXrvRSlOMOZknky3HNI2ijFIX56Vnet37RBijIzpS0DIkEeDWdKKXZVt91hb8aol0ogPKVahimMxHC95NAwG55hUcNaShJBA+Rfxdo/uV5VqneIT8Lg3Vb/jfm86s5ERT31bN4J8e975rmULuEhnn0nPkU1l7pfbYZk9LrEA38LXoX3pt6Lv+9nvJ9e6p5WtSvvoZeFM3uHZHKdV269w3XrBG5qvQTq1fx+RLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVap3iE/C4N1W/435vOrOREU99WzeCfHve+a5lC7hIZ59Jz5FNZe6X22GZPS6xAN/C16F96bei7/vZ7yfXuqeVrUr76GXhTN7h2RynVduvcN16wRuar0E6tX8fkS2yDARJeu9FKU4w5mSeTLcc0jaKMUhfnpWd63ftEGKMjOlLQMiQR4NZ0opdlW33WFvxqiXSiA8pVqGKYzEcL3k0DAbnmFRw1pKEkED5F/F2j+5XlWqd4hPwuDdVv+N+bzqzkRFPfVs3gnx73vmuZQu4SGefSc+RTWXul9thmT0usQDfwtehfem3ou/72e8n17qnla1K++hl4Uze4dkcp1Xbr3DdesEbmq9BOrV/H5EtsgwESXrvRSlOMOZknky3HNI2ijFIX56Vnet37RBijIzpS0DIkEeDWdKKXZVt91hb8aol0ogPKVahimMxHC95NAwG55hUcNaShJBA+Rfxdo/uV5VqneIT8Lg3Vb/jfm86s5ERT31bN4J8e975rmULuEhnn0nPkU1l7pfbYZk9LrEA38LXoX3pt6Lv+9nvJ9e6p5WtSvvoZeFM3uHZHKdV269w3XrBG5qvQTq1fx+RLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVap3iE/C4N1W/435vOrOREU99WzeCfHve+a5lC7hIZ59Jz5FNZe6X22GZPS6xAN/C16F96bei7/vZ7yfXuqeVrUr76GXhTN7h2RynVduvcN16wRuar0E6tX8fkS2yDARJeu9FKU4w5mSeTLcc0jaKMUhfnpWd63ftEGKMjOlLQMiQR4NZ0opdlW33WFvxqiXSiA8pVqGKYzEcL3k0DAbnmFRw1pKEkED5F/F2j+5XlWqd4hPwuDdVv+N+bzqzkRFPfVs3gnx73vmuZQu4SGefSc+RTWXul9thmT0usQDfwtehfem3ou/72e8n17qnla1K++hl4Uze4dkcp1Xbr3DdesEbmq9BOrV/H5EtsgwESXrvRSlOMOZknky3HNI2ijFIX56Vnet37RBijIzpS0DIkEeDWdKKXZVt91hb8aol0ogPKVahimMxHC95NAwG55hUcNaShJBA+Rfxdo/uV5VqneIT8Lg3Vb/jfm86s5ERT31bN4J8e975rmULuEhnn0nPkU1l7pfbYZk9LrEA38LXoX3pt6Lv+9nvJ9e6p5WtSvvoZeFM3uHZHKdV269w3XrBG5qvQTq1fx+RLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVap3iE/C4N1W/435vOrOREU99WzeCfHve+a5lC7hIZ59Jz5FNZe6X22GZPS6xAN/C16F96bei7/vZ7yfXuqeVrUr76GXhTN7h2RynVduvcN16wRuar0E6tX8fkS2yDARJeu9FKU4w5mSeTLcc0jaKMUhfnpWd63ftEGKMjOlLQMiQR4NZ0opdlW33WFvxqiXSiA8pVqGKYzEcL3k0DAbnmFRw1pKEkED5F/F2j+5XlWqd4hPwuDdVv+N+bzqzkRFPfVs3gnx73vmuZQu4SGefSc+RTWXul9thmT0usQDfwtehfem3ou/72e8n17qnla1K++hl4Uze4dkcp1Xbr3DdesEbmq9BOrV/H5EtsgwESXrvRSlOMOZknky3HNI2ijFIX56Vnet37RBijIzpS0DIkEeDWdKKXZVt91hb8aol0ogPKVahimMxHC95NAwG55hUcNaShJBA+Rfxdo/uV5VqneIT8Lg3Vb/jfm86s5ERT31bN4J8e975rmULuEhnn0nPkU1l7pfbYZk9LrEA38LXoX3pt6Lv+9nvJ9e6p5WtSvvoZeFM3uHZHKdV269w3XrBG5qvQTq1fx+RLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5cH7JFn4DPGvsvj7pEP/1Oeq8r82SzWj51SePY9PylV6Hfr7fKbaMM+uVKpPvduq4nrf73p+7rCahWecqKjJSWMe3rmfIXl34abWS2Be6MNXrLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5cH7JFn4DPGvsvj7pEP/1Oeq8r82SzWj51SePY9PylV6Hfr7fKbaMM+uVKpPvduq4nrf73p+7rCahWecqKjJSWMe3rmfIXl34abWS2Be6MNXrLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5cH7JFn4DPGvsvj7pEP/1Oeq8r82SzWj51SePY9PylV6Hfr7fKbaMM+uVKpPvduq4nrf73p+7rCahWecqKjJSWMe3rmfIXl34abWS2Be6MNXrLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5cH7JFn4DPGvsvj7pEP/1Oeq8r82SzWj51SePY9PylV6Hfr7fKbaMM+uVKpPvduq4nrf73p+7rCahWecqKjJSWMe3rmfIXl34abWS2Be6MNXrLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5cH7JFn4DPGvsvj7pEP/1Oeq8r82SzWj51SePY9PylV6Hfr7fKbaMM+uVKpPvduq4nrf73p+7rCahWecqKjJSWMe3rmfIXl34abWS2Be6MNXrLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5cH7JFn4DPGvsvj7pEP/1Oeq8r82SzWj51SePY9PylV6Hfr7fKbaMM+uVKpPvduq4nrf73p+7rCahWecqKjJSWMe3rmfIXl34abWS2Be6MNXrLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5cH7JFn4DPGvsvj7pEP/1Oeq8r82SzWj51SePY9PylV6Hfr7fKbaMM+uVKpPvduq4nrf73p+7rCahWecqKjJSWMe3rmfIXl34abWS2Be6MNXrLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5cH7JFn4DPGvsvj7pEP/1Oeq8r82SzWj51SePY9PylV6Hfr7fKbaMM+uVKpPvduq4nrf73p+7rCahWecqKjJSWMe3rmfIXl34abWS2Be6MNXrLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5cH7JFn4DPGvsvj7pEP/1Oeq8r82SzWj51SePY9PylV6Hfr7fKbaMM+uVKpPvduq4nrf73p+7rCahWecqKjJSWMe3rmfIXl34abWS2Be6MNXrLbIMBEl670UpTjDmZJ5MtxzSNooxSF+elZ3rd+0QYoyM6UtAyJBHg1nSil2VbfdYW/GqJdKIDylWoYpjMRwveTQMBueYVHDWkoSQQPkX8XaP7leVVZF5eH/2vmjHUGW4+Ya9fu/tHxhQBjs6uQsBkPZVhXX1ZwuRgTJmQ/I3b98tE+ShWuI/1MW/XPSof6qc7n7fzeLm1HvPHnWe3RSfmXWof4517gN8+ynK+5X7da9+Py5ntX7uUM3C8+YXDkpk8Y02rnWkLxb3Lz1K4i/0B//ik9TREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QZQP5VPPsnf+ZXnsrnlN6WO9FTfIPW65/PnLjenleIf5JOZyFKnp1fIftPF09udW+n/TqX7oRsJql5Y7ol4oT/nChPvZ226fZ0Uj6V5OVNktlTCj1LIPt1llO3u9y89SuIfww//hWfpoiYHCXjsxRWnFBjmSfimUPShhWH+JlZ3Rq/aYMUJTTWlICcIJ9CjZViq7p1hzONuG6VQPZYtYQpxIlw3HIoNos9YVFhLdaJMoD8q3j2T/7MrzyVzym9LXeip/gGrdc/nzlxvT2vEP8knc5ClDw7v0L2ny6e3OreTvt1Lt0J2UxS88Z0S8QJ/zlRnno7bdPt6aR8KsnLmySzpxR6lkD26yynbne5eetXEP8YfvwrPk0RMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE2UA+Vfx7J/8mV95Kp9TelvuRE/xDVqvfz5z4np7XiH+STqdhSh5dn6F7D9dPLnVvZ3261y6E7KZpOaN6ZaIE/5zojz1dtqm29NJ+VSSlzdJZk8p9CyB7NdZTt3ucvPWryD+Mfz4V3yaImJylIzPUlhxQo1lnohnDkkbVhziZ2Z1a/ymDVKU0FhTAnKCfAo1Voqt6tYdzjTiulUC2WPVEqYQJ8Jxy6HYLPaERYW1WCfKAPKv4tk/+TO/8lQ+p/S23Ime4hu0Xv985sT19rxC/JN0OgtR8uz8Ctl/unhyq3s77de5dCdkM0nNG9MtESf850R56u20Tbenk/KpJC9vksyeUuhZAtmvs5y63eXmrV9B/GP48a/4NEXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlAHkX8Wzf/JnfuWpfE7pbbkTPcU3aL3++cyJ6+15hfgn6XQWouTZ+RWy/3Tx5Fb3dtqvc+lOyGaSmjemWyJO+M+J8tTbaZtuTyflU0le3iSZPaXQswSyX2c5dbvLzVu/gvjH8ONf8WmKiMlRMj5LYcUJNZZ5Ip45JG1YcYifmdWt8Zs2SFFCY00JyAnyKdRYKbaqW3c404jrVglkj1VLmEKcCMcth2Kz2BMWFdZinSgDyL+KZ//kz/zKU/mc0ttyJ3qKb9B6/fOZE9fb8wrxT9LpLETJs/MrZP/p4smt7u20X+fSnZDNJDVvTLdEnPCfE+Wpt9M23Z5OyqeSvLxJMntKoWcJZL/Ocup2l5u3fgXxj+HHv+LTFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRBpB/Fc/+yZ/5lafyOaW35U70FN+g9frnMyeut+cV4p+k01mIkmfnV8j+08WTW93bab/OpTshm0lq3phuiTjhPyfKU2+nbbo9nZRPJXl5k2T2lELPEsh+neXU7S43b/0K4h/Dj3/FpykiJkfJ+CyFFSfUWOaJeOaQtGHFIX5mVrfGb9ogRQmNNSUgJ8inUGOl2Kpu3eFMI65bJZA9Vi1hCnEiHLccis1iT1hUWIt1ogwg/yqe/ZM/8ytP5XNKb8ud6Cm+Qev1z2dOXG/PK8Q/SaezECXPzq+Q/aeLJ7e6t9N+nUt3QjaT1Lwx3RJxwn9OlKfeTtt0ezopn0ry8ibJ7CmFniWQ/TrLqdtdbt76FcQ/hh//ik9TREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QZQP5VPPsnf+ZXnsrnlN6WO9FTfIPW65/PnLjenleIf5JOZyFKnp1fIftPF09udW+n/TqX7oRsJql5Y7ol4oT/nChPvZ226fZ0Uj6V5OVNktlTCj1LIPt1llO3u9y89SuIfww//hWfpoiYHCXjsxRWnFBjmSfimUPShhWH+JlZ3Rq/aYMUJTTWlICcIJ9CjZViq7p1hzONuG6VQPZYtYQpxIlw3HIoNos9YVFhLdaJMoD8q3j2T/7MrzyVzym9LXeip/gGrdc/nzlxvT2vEP8knc5ClDw7v0L2ny6e3OreTvt1Lt0J2UxS88Z0S8QJ/zlRnno7bdPt6aR8KsnLmySzpxR6lkD26yynbne5eetXEP8YfvwrPk0RMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE2UA+Vfx7J/8+XTl+XPXz2mD/rN7SzfGM2q3xJu+9VTybbwTvY245X4SV6eLpz16/0lJPCeJiDfe81PJvxKfpB+enfeWZHlOnTaT66433QD5+Wy/vkL0p9TayanPXW7e+hVOfzV/fiJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QZQP5VPPsnfz5def7c9XPaoP/s3tKN8YzaLfGmbz2VfBvvRG8jbrmfxNXp4mmP3n9SEs9JIuKN9/xU8q/EJ+mHZ+e9JVmeU6fN5LrrTTdAfj7br68Q/Sm1dnLqc5ebt36F01/Nn5/IFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRBpB/Fc/+yZ9PV54/d/2cNug/u7d0Yzyjdku86VtPJd/GO9HbiFvuJ3F1unjao/eflMRzkoh44z0/lfwr8Un64dl5b0mW59RpM7nuetMNkJ/P9usrRH9KrZ2c+tzl5q1f4fRX8+cnMkXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlAHkX8Wzf/Ln05Xnz10/pw36z+4t3RjPqN0Sb/rWU8m38U70NuKW+0lcnS6e9uj9JyXxnCQi3njPTyX/SnySfnh23luS5Tl12kyuu950A+Tns/36CtGfUmsnpz53uXnrVzj91fz5iUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE2UA+Vfx7J/8+XTl+XPXz2mD/rN7SzfGM2q3xJu+9VTybbwTvY245X4SV6eLpz16/0lJPCeJiDfe81PJvxKfpB+enfeWZHlOnTaT66433QD5+Wy/vkL0p9TayanPXW7e+hVOfzV/fiJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QZQP5VPPsnfz5def7c9XPaoP/s3tKN8YzaLfGmbz2VfBvvRG8jbrmfxNXp4mmP3n9SEs9JIuKN9/xU8q/EJ+mHZ+e9JVmeU6fN5LrrTTdAfj7br68Q/Sm1dnLqc5ebt36F01/Nn5/IFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRBpB/Fc/+yZ9PV54/d/2cNug/u7d0Yzyjdku86VtPJd/GO9HbiFvuJ3F1unjao/eflMRzkoh44z0/lfwr8Un64dl5b0mW59RpM7nuetMNkJ/P9usrRH9KrZ2c+tzl5q1f4fRX8+cnMkXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlAHkX8Wzf/Ln05Xnz10/pw36z+4t3RjPqN0Sb/rWU8m38U70NuKW+0lcnS6e9uj9JyXxnCQi3njPTyX/SnySfnh23luS5Tl12kyuu950A+Tns/36CtGfUmsnpz53uXnrVzj91fz5iUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE2UA+Vfx7J/8+XTl+XPXz2mD/rN7SzfGM2q3xJu+9VTybbwTvY245X4SV6eLpz16/0lJPCeJiDfe81PJvxKfpB+enfeWZHlOnTaT66433QD5+Wy/vkL0p9TayanPXW7e+hVOfzV/fiJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QZQP5VPPsnfz5def7c9XPaoP/s3tKN8YzaLfGmbz2VfBvvRG8jbrmfxNXp4mmP3n9SEs9JIuKN9/xU8q/EJ+mHZ+e9JVmeU6fN5LrrTTdAfj7br68Q/Sm1dnLqc5ebt36F01/Nn5/IFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgr38hWG8nMUXE5CgZn6Ww4oQayzwRzxySNqw4xM/M6tb4TRukKKGxpgTkBPkUaqwUW9WtO5xpxHWrBLLHqiVMIU6E45ZDsVnsCYsKa7FOlFJKKeWDvP6FYL2dxBQRk6NkfJbCihNqLPNEPHNI2rDiED8zq1vjN22QooTGmhKQE+RTqLFSbFW37nCmEdetEsgeq5YwhTgRjlsOxWaxJywqrMU6UUoppZQP8voXgvV2ElNETI6S8VkKK06oscwT8cwhacOKQ/zMrG6N37RBihIaa0pATpBPocZKsVXdusOZRly3SiB7rFrCFOJEOG45FJvFnrCosBbrRCmllFI+yOtfCNbbSUwRMTlKxmcprDihxjJPxDOHpA0rDvEzs7o1ftMGKUporCkBOUE+hRorxVZ16w5nGnHdKoHssWoJU4gT4bjlUGwWe8KiwlqsE6WUUkr5IK9/IVhvJzFFxOQoGZ+lsOKEGss8Ec8ckjasOMTPzOrW+E0bpCihsaYE5AT5FGqsFFvVrTucacR1qwSyx6olTCFOhOOWQ7FZ7AmLCmuxTpRSSinlg7z+hWC9ncQUEZOjZHyWwooTaizzRDxzSNqw4hA/M6tb4zdtkKKExpoSkBPkU6ixUmxVt+5wphHXrRLIHquWMIU4EY5bDsVmsScsKqzFOlFKKaWUD/L6F4L1dhJTREyOkvFZCitOqLHME/HMIWnDikP8zKxujd+0QYoSGmtKQE6QT6HGSrFV3brDmUZct0oge6xawhTiRDhuORSbxZ6wqLAW60QppZRSPsjrXwjW20lMETE5SsZnKaw4ocYyT8Qzh6QNKw7xM7O6NX7TBilKaKwpATlBPoUaK8VWdesOZxpx3SqB7LFqCVOIE+G45VBsFnvCosJarBOllFJK+SCvfyFYbycxRcTkKBmfpbDihBrLPBHPHJI2rDjEz8zq1vhNG6QoobGmBOQE+RRqrBRb1a07nGnEdasEsseqJUwhToTjlkOxWewJiwprsU6UUkop5YO8/oVgvZ3EFBGTo2R8lsKKE2os80Q8c0jasOIQPzOrW+M3bZCihMaaEpAT5FOosVJsVbfucKYR160SyB6rljCFOBGOWw7FZrEnLCqsxTpRSimllA/y+heC9XYSU0RMjpLxWQorTqixzBPxzCFpw4pD/Mysbo3ftEGKEhprSkBOkE+hxkqxVd26w5lGXLdKIHusWsIU4kQ4bjkUm8WesKiwFutEKaWUUj7I618I1ttJTBExOUrGZymsOKHGMk/EM4ekDSsO8TOzujV+0wYpSmisKQE5QT6FGivFVnXrDmcacd0qgeyxaglTiBPhuOVQbBZ7wqLCWqwTpZRSSvkgfSGUUkoppZRSSilli/6eoZRSSimllFJKKVv09wyllFJKKaWUUkrZor9nKKWUUkoppZRSyhb9PUMppZRSSimllFK26O8ZSimllFJKKaWUskV/z1BKKaWUUkoppZQt+nuGUkoppZRSSimlbNHfM5RSSimllFJKKWWL/p6hlFJKKaWUUkopW/T3DKWUUkoppZRSStmiv2copZRSSimllFLKFv09QymllFJKKaWUUrbo7xlKKaWUUkoppZSyRX/PUEoppZRSSimllC36e4ZSSimllFJKKaVs0d8zlFJKKaWUUkopZYv+nuFl/M+D5yetf/57EJ/+LfjrD08aa63Q/98PRRz+cxKnlFJKKaWUUsqPiP/KK/+NkP9mP/2ZLOT/Df68Ila5v6n48VcQpz3ihB4ZyEoppZRSSinl4/T3DC8D/h7gz/+v+eE28j8AeP4SQFj6cS3RnwyfNuifCzOllFJKKaWUUk70P6DehPV/gKBH+LYf/wn99ZcA7u8ZtI2/xhnE/Ieg/8+klFJKKaWUUgj9PcOb+Pd/EfP/MUDyH+D/Ov83+F//zzTICPcGf8/w4//fL/T3DKWUUkoppZSySH/P8CZOv17Qv2f4B08NuSt+Mvg9gzCg/+cTpzgimmhAGy6llFJKKaWU8qS/Z3g3f/3fG1j/m4Ef/0tc7z/9efx7hpO3v46cNuiR/p6hlFJKKaWUUiz6e4Z3s/h7BvFz8X+J8PxfC/zV0l8NiAi7v2eYbS6llFJKKaWUL9PfM7yb/w+/Z3At/dXAP77+R3/PIP5PKkoppZRSSimlPOl/Pb2JwX/Uw/+WJ1d+/P+59XsGGGHwuxH477//z6SUUkoppZRSCP09w5uA/71v/Q8Akl8I6P/q/6uSxBEad5Wm/8+klFJKKaWUUgj9PcPLEP8j/x9/Tv72//p/OzD7PcPJkhh/6nmcU4T+nqGUUkoppZRSFunvGUoppZRSSimllLJFf89QSimllDLjfwFChYhBDQplbmRzdHJlYW0NCmVuZG9iag0KDQoxMCAwIG9iag0KPDwNCi9GaWx0ZXIgL0ZsYXRlRGVjb2RlDQovTGVuZ3RoIDE4NzENCj4+DQpzdHJlYW0NCnicvVlbb+M2Fn434P/AhwJNF6lKStStb51OMjvF7O5sJ4tOgb7QEh2zK4suJSVxf/1+h5Rkp3PZzkANAsS0RZ7Ld875zqH97O169dt6xaMsYZz+aJHGUS5ZKqJCsmq/XsVMJEWU5kImrOCclXHMnF6vfmLteiUY/blbv42XYUMaNmy9cDpevnvw2c169c212lQZExm7wV5vgBcl/EqwHILSsmQ3sOKCfcVufl2vrnDu339KcM5i+QHBcZHBwSCYR0kCAyqouP50FaL4gIqEi1lFmUZlGScxu/kdWq6eL+hJEqezmq95xHkRXLla0hV55kpMcIngydslY5KkxUlLHsk45llQ82ZJX3LxGC+e5gGxv798/frlP18s6ZJMk/cG59WCDsmsPIetTMSYZt89u3r1yc58pBghVxYi6EkijnCVXhGP4rKUUHoPpTeWHZw+KKfZ0Q6OHVT1X3Wr2dY61u3MYa/b/pIesVbrmvWW1Zb1O9rQNPbetLffLmhyXDAJ0vL0IaIFBYOXJsEoiIyLsbbBJaJMy4DFfzrtXXvtTNuzzdD3tmWmDcBsnL3vtCMEDv55vzMd4AJW+MhvaRRt8E+1i5YMJeFCSUu4xEvjMgomXOJSZDMuRTzicm2bOsTcuK4fHayD74Bnp5otU23NBuCnOr+TUueA5AAmG91E7IY+05Vtp2OdzzAPm8Pnru4uvQxs6ZVpO7ax/c6LWthbngZvRZSWhUgnb7NsdBah23eTKbXpjYUxdjt7FMw0LczfK3pKbm+HxvvTO5QPuX1eS0tnQoJe6DMhWTgTJsHAJpEynjMhL7kM4Hy37acMJy8pPD7Al+zQqAph7SkhFLtXx41pGnawQ7XzgKnt1jzQ86lYJnJhHfGJ6r2wjXKVrTXOOQ8tcJ91sEq1CydDkuXB4SySOS+L0WEhs2Skx42GUFV7FzoY0Or608P5kYYDjk5wZuw3QuRjc5vXZMQrfasadjMn5vdzYn4GHPzDlsSpnDtfHheTJWlY+26h21o7Cv2Y26jTIwogJAOKujt2vd5T6XS96YceG3y01a3T2oe7D+0DTHlnkDFnReYLCI9QRG1HGaCmHHiUMB1xzNYOvgr9AXU4NKZSm0aza11fPbA3o/AXg6k1CvZOmcY/XhgvGU9dvIglT8b0SbMsTwJewwEOOP3boLs+Go27p8JoLRoM5RYcbTtDtpH7qj2yqlFmT67ph0p33VQDZ07WGnscGPhONQP8u99p7AjgQeLQ9HSosR04tVZ7hAmvulHHS+htF8QAffQEApKm5DyMGBdfL6pFzlooNUVMg0xIzbAmqOGgudMOPu5Nd3oDUPH+jK4vl06ChM+WZUWcTJaFtU+CtqE40gw1Rg4MuTO3FLIxggd1ROyZqkMtoNqrnXI+brYafN2EMqr6AQ8ptJ4JtqYhYb3Z62bMnIi9MnsTaueT6sQ/PkbsZ9+TYZ6v1YXBoql4vC4kXMqpYvJSjIRLwwAix7bO7kcrp8IICW3aqhlqIhw0IVBRZ6qA4lQoIzNdBpSocyvg7w/aPQ0saGEoR4Du7NbgVWHOc60+fgm4NO2sbNeP44j1hUXJ40WFapoLrjYwt/eiASAGl2bx7OLZBFhWyuwEGM/HvCeqJYJBRyb1NLYfdIU1zVgNpUIY2wkakLCiBg5PvhCwaCRcNfQ768zv2PiYWiL2YwjHkZovcRZxEp6OeTglJz4hsCP2D/Vg9sPehwyKA2j6AYyOCc+0CoJCrGDbFynnS8MlylMHE6WQM034NcGlo9uI/arvdUPsgKtPZezQsb1G8BDzVt9aAEklgtTq3TB2nJAKbPKqMR05jbqiYnlURhH7yZm+120oR4geOs/1VKs12B99smUQbareF24IEnR3+v+V6GdNkh+DqzixqpREsQGusPaj/zstqwnoUIRDgbmpKvzNyO7MxqdcAIqmQZSuviMKs+409COD/FkLdu6Iqs4q+9KjZIc+QDPSttlD+oGy11FsBgyUlSGxoeD9OVwxBudXe+X8II7NC2OWn/g+l2Lm+7AmzMDiIJjOmzFV2B+9nciK8u9gRnCAlG010w3uUYQAtYSJriYOn+8Ub7T+M6ROgIP3Do3usf89dxvczSyzG7p0LY1UeiL7ssiymbuychwnz29RDkOu87y+s/eUS2NvC+OQ9Rfv0Uz1xwEP3uHqDY5CmooFnUhkfvLiL5tvkpTPWi4gYUnRWfoUDmTFSUsWlWlcjt83vvjXkmqK+CmcKU6Q+Rov0tNFqJiuZNdXz6/esl8ulsw2GRdP4J9MxF+VbVJmT+GALE8O4Ka+pGgMWE/gQHZiFSiJiyQ0kYskScpfvvqcL3U+qKpMTli9Q+85DyekLJj/4abwPx998xJCC/bc+n08ynKZCvqlKS1QgXhlP74gq36ABPoajaVZjv84KeL5XbNevaHDZZwiXngVCS7FPCpkzt8jIHskIDsT8K527yBG46JgecFZEn66+pu39urterVe/Q9PIqOqDQplbmRzdHJlYW0NCmVuZG9iag0KDQoxIDAgb2JqDQo8PA0KL1R5cGUgL091dGxpbmVzDQo+Pg0KZW5kb2JqDQoNCjIgMCBvYmoNCjw8DQovQ291bnQgMQ0KL0tpZHMgWyA2IDAgUiBdDQovVHlwZSAvUGFnZXMNCj4+DQplbmRvYmoNCg0KeHJlZg0KMCAzDQowMDAwMDAwMDAwIDY1NTM1IGYNCjAwMDAwNzQ5MDkgMDAwMDAgbg0KMDAwMDA3NDk1MyAwMDAwMCBuDQp0cmFpbGVyDQo8PA0KL1NpemUgMw0KPj4NCnN0YXJ0eHJlZg0KMTgzDQolJUVPRg0K\n";
                            $shipment1->order_id = $orderId;
                            $shipment1->tracking_number = '7987 6668 4568';
                            $shipment1->type = 'FedEx';

                            $shipments[] = $shipment1;

                            // Display the code
                            echo '$item1 = new stdClass;' . PHP_EOL
                               . '$item1->id = $itemId;' . PHP_EOL
                               . PHP_EOL
                               //. '$item2 = new stdClass;' . PHP_EOL
                               //. '$item2->id = \'1004\';' . PHP_EOL
                               //. PHP_EOL
                               . '$items[] = $item1;' . PHP_EOL
                               //. '$items[] = $item2;' . PHP_EOL
                               . PHP_EOL
                               . '$shipment1 = new stdClass;' . PHP_EOL
                               . '$shipment1->items = $items;' . PHP_EOL
                               . '$shipment1->airbill = "JVBERi0xLjINCg0KMyAwIG9iag0KPDwNCi9FIDc0OTA5DQovSCBbIDEwMTEgMTQ2IF0NCi9MIDc1MTQxDQov…";' . PHP_EOL
                               . '$shipment1->order_id = $orderId;' . PHP_EOL
                               . '$shipment1->tracking_number = \'7987 6668 4568\';' . PHP_EOL
                               . '$shipment1->type = \'FedEx\';' . PHP_EOL
                               . PHP_EOL
                               . '$shipments[] = $shipment1;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($shipments);' . PHP_EOL
                            ;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($shipments);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'updateShipment' :
                            $shipmentId = $input->id;

                            // Create the proper format
                            $shipment = new stdClass;
                            $shipment->tracking_number = '7112 3589 5648';
                            $shipment->type = 'FedEx';
                            $shipment->airbill = "JVBERi0xLjINCg0KMyAwIG9iag0KPDwNCi9FIDc0OTA5DQovSCBbIDEwMTEgMTQ2IF0NCi9MIDc1MTQxDQovTGluZWFyaXplZCAxDQovTiAxDQovTyA2DQovVCA3NTAzMQ0KPj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgDQplbmRvYmoNCg0KeHJlZg0KMyA4DQowMDAwMDAwMDEyIDAwMDAwIG4NCjAwMDAwMDA4ODggMDAwMDAgbg0KMDAwMDAwMTAxMSAwMDAwMCBuDQowMDAwMDAxMTU4IDAwMDAwIG4NCjAwMDAwMDE0MDcgMDAwMDAgbg0KMDAwMDAwMTUxNiAwMDAwMCBuDQowMDAwMDAxNjI0IDAwMDAwIG4NCjAwMDAwNzI5NTMgMDAwMDAgbg0KdHJhaWxlcg0KPDwNCi9BQkNwZGYgNzAyOQ0KL0lEIFsgPEIwOTA0Q0EzRDhEQzczNTNFQzdCNkVGOEU4NDEwRUYzPg0KPEVDNDExRDE5QzA4NkYxMzk0OTY0NzYzOTgxQzczMzQ0PiBdDQovUHJldiA3NTAyMQ0KL1Jvb3QgNCAwIFINCi9TaXplIDExDQo+PiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICANCnN0YXJ0eHJlZg0KMA0KJSVFT0YNCg0KNCAwIG9iag0KPDwN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\n";

                            // Display the code
                            echo '$shipment = new stdClass;' . PHP_EOL
                               . '$shipment->tracking_number = \'7112 3589 5648\';' . PHP_EOL
                               . '$shipment->type = \'FedEx\';' . PHP_EOL
                               . '$shipment->airbill = "JVBERi0xLjINCg0KMyAwIG9iag0KPDwNCi9FIDc0OTA5DQovSCBbIDEwMTEgMTQ2IF0NCi9MIDc1MTQxDQov…";' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($shipmentId, $shipment);' . PHP_EOL
                            ;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($shipmentId, $shipment);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                            try {
                                $results = $tevo->createOrder($orderDetails);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'createFulfillmentOrder' :
                            $fulfillment = true;
                            // Purposely "flow" into createOrderCustomer

                        case 'createOrderCustomer' :
                            $fulfillment = (isset($fulfillment)) ? $fulfillment : false;
                            $clientId = $input->clientId;

                            // Create the proper format
                            $item = new stdClass;
                            $item->price = '31.32';
                            $item->ticket_group_id = '10145892';
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

                            /**
                             * If you handle payments yourself and do not need
                             * to run your payments through the TEvo-provided
                             * gateway then you can use 'offline' as the payment
                             * type and just handle the payment stuff on your own.
                             */
                            $payment = new stdClass;
                            $payment->type = 'credit_card';

                            // 'credit_card_id' is now required if you use
                            // 'credit_card' and don't specify all info
                            $payment->credit_card_id = '111';

//                             $credit_card = new stdClass;
//                             $credit_card->number = '4111111111111111';
//                             $credit_card->verification_code = '666';
//                             $credit_card->expiration_month = '12';
//                             $credit_card->expiration_year = '2013';
//                             $credit_card->ip_address = '37.235.140.72';
//                             $credit_card->phone_number_id = 528;
//                             $credit_card->name = 'A. Card';
//                             $credit_card->address_id = 123;
//                             $payment->credit_card = $credit_card;


                            $order1 = new stdClass;
                            $order1->items[] = $item;
                            //$order1->shipping_address = $shippingAddress;
                            //$order1->billing_address = $billingAddress;
                            $order1->payments[] = $payment;
                            $order1->seller_id = $cfg['params']['buyerId'];
                            $order1->client_id = $clientId;
                            $order1->billing_address_id = 46645;
                            $order1->shipping_address_id = 46645;

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
                               . '$payment = new stdClass;' . PHP_EOL
                               . '$payment->type = \'credit_card\';' . PHP_EOL
                               . '$payment->credit_card_id = \'1\';' . PHP_EOL
                               . PHP_EOL
                               . '$order1 = new stdClass;' . PHP_EOL
                               . '$order1->items[] = $item;' . PHP_EOL
                               . '$order1->shipping_address = $shippingAddress;' . PHP_EOL
                               . '$order1->billing_address = $billingAddress;' . PHP_EOL
                               . '$order1->payments[] = $payment;' . PHP_EOL
                               . '$order1->seller_id = $cfg[\'params\'][\'buyerId\'];' . PHP_EOL
                               . '$order1->client_id = $clientId;' . PHP_EOL
                               . PHP_EOL
                               . '$orderDetails[] = $order1;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->createOrder($orderDetails, false);' . PHP_EOL
                            ;

                            // Execute the call
                            try {
                                $results = $tevo->createOrder($orderDetails, (bool) $fulfillment);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'acceptOrder' :
                            $orderId = $input->id;
                            $userId = $input->userId;

                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($orderId, $userId);' . PHP_EOL;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($orderId, $userId);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'rejectOrder' :
                            $orderId = $input->id;
                            $userId = $input->userId;
                            $rejectionReason = $input->rejectionReason;

                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($orderId, $userId, $rejectionReason);' . PHP_EOL;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($orderId, $userId, $rejectionReason);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
                            break;


                        case 'completeOrder' :
                            $orderId = $input->id;

                            // Display the code
                            echo '$results = $tevo->' . $apiMethod . '($orderId);' . PHP_EOL;

                            // Execute the call
                            try {
                                $results = $tevo->$apiMethod($orderId);
                            } catch (Exception $e) {
                                echo '</pre>' . PHP_EOL
                                   . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
                                   . _getRequest($tevo, $apiMethod, true)
                                   . _getResponse($tevo, $apiMethod, true);
                                exit (1);
                            }
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
                        echo _getRequest($tevo, $apiMethod, false);
                        echo _getResponse($tevo, $apiMethod, false);

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
		        <fieldset id="environmentAndCredentials">
                    <legend>Environment and Credentials</legend>

                    <label for="environment">Environment: </label>
                    <br />
                    <select name="environment" id="environment" onchange="changeEnvironment();">
                        <option value="sandbox"<?php if (@$input->environment == 'sandbox') { echo ' selected="selected"';} ?>>Sandbox</option>
                        <option value="staging"<?php if (@$input->environment == 'staging') { echo ' selected="selected"';} ?>>Staging</option>
                        <option value="production"<?php if (@$input->environment == 'production') { echo ' selected="selected"';} ?>>Production</option>
                    </select>

                    <br />
                    <br />
                    <label for="apiToken">API Version: </label>
                    <br />
                    <input name="apiVersion" id="apiVersion" type="text" value="9" size="2" maxlength="2" readonly="readonly" />

                    <br />
                    <br />
                    <label for="apiToken">API Token: </label>
                    <br />
                    <input name="apiToken" id="sandboxApiToken" type="text" value="<?php if (!empty($sandbox['apiToken'])) {echo $sandbox['apiToken'];} ?>" size="40" maxlength="40" />
                    <input name="apiToken" id="stagingApiToken" type="text" value="<?php if (!empty($staging['apiToken'])) {echo $staging['apiToken'];} ?>" size="40" maxlength="40" />
                    <input name="apiToken" id="productionApiToken" type="text" value="<?php if (!empty($production['apiToken'])) {echo $production['apiToken'];} ?>" size="40" maxlength="40" />

                    <br />
                    <br />
                    <label for="secretKey">API Secret: </label>
                    <br />
                    <input name="secretKey" id="sandboxSecretKey" type="text" value="<?php if (!empty($sandbox['secretKey'])) {echo $sandbox['secretKey'];} ?>" size="40" maxlength="40" />
                    <input name="secretKey" id="stagingSecretKey" type="text" value="<?php if (!empty($staging['secretKey'])) {echo $staging['secretKey'];} ?>" size="40" maxlength="40" />
                    <input name="secretKey" id="productionSecretKey" type="text" value="<?php if (!empty($production['secretKey'])) {echo $production['secretKey'];} ?>" size="40" maxlength="40" />

                    <br />
                    <br />
                    <label for="buyerId">Buyer ID (a/k/a officeId): </label>
                    <br />
                    <input name="buyerId" id="sandboxBuyerId" type="text" value="<?php if (!empty($sandbox['buyerId'])) {echo $sandbox['buyerId'];} ?>" size="6" maxlength="5" />
                    <input name="buyerId" id="stagingBuyerId" type="text" value="<?php if (!empty($staging['buyerId'])) {echo $staging['buyerId'];} ?>" size="6" maxlength="5" />
                    <input name="buyerId" id="productionBuyerId" type="text" value="<?php if (!empty($production['buyerId'])) {echo $production['buyerId'];} ?>" size="6" maxlength="5" />
                </fieldset>

		        <fieldset>
		        <legend>Ticket Evolution Framework Demo</legend>

		        <label for="apiMethod" accesskey="m">Framework Method: </label>
                <br />
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
                            <option label="listCategoriesDeleted" value="listCategoriesDeleted">listCategoriesDeleted</option>
                            <option label="showCategory" value="showCategory">showCategory</option>
                        </optgroup>

                        <optgroup label="Configurations Methods">
                            <option label="listConfigurations" value="listConfigurations">listConfigurations</option>
                            <option label="showConfiguration" value="showConfiguration">showConfiguration</option>
                        </optgroup>

                        <optgroup label="Events Methods">
                            <option label="listEvents" value="listEvents">listEvents</option>
                            <option label="listEventsDeleted" value="listEventsDeleted">listEventsDeleted</option>
                            <option label="showEvent" value="showEvent">showEvent</option>
                        </optgroup>

                        <optgroup label="Performers Methods">
                            <option label="listPerformers" value="listPerformers">listPerformers</option>
                            <option label="listPerformersDeleted" value="listPerformersDeleted">listPerformersDeleted</option>
                            <option label="showPerformer" value="showPerformer">showPerformer</option>
                            <option label="searchPerformers" value="searchPerformers">searchPerformers</option>
                        </optgroup>

                        <optgroup label="Search Methods">
                            <option label="search" value="search">Performers & Venues</option>
                        </optgroup>

                        <optgroup label="Venues Methods">
                            <option label="listVenues" value="listVenues">listVenues</option>
                            <option label="listVenuesDeleted" value="listVenuesDeleted">listVenuesDeleted</option>
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
                            <option label="updateShipment" value="updateShipment"<?php echo $disabled;?>>updateShipment</option>
                        </optgroup>

		            <optgroup label="EvoPay Resources">
		            </optgroup>
                        <optgroup label="Accounts Methods">
                            <option label="listEvoPayAccounts" value="listEvoPayAccounts">listEvoPayAccounts</option>
                            <option label="showEvoPayAccount" value="showEvoPayAccount">showEvoPayAccount</option>
                        </optgroup>

                        <optgroup label="Transactions Methods">
                            <option label="listEvoPayTransactions" value="listEvoPayTransactions">listEvoPayTransactions</option>
                            <option label="showEvoPayTransaction" value="showEvoPayTransaction">showEvoPayTransaction</option>
                        </optgroup>

		        </select>

		        <div id="idOption" class="options">
                    <br />
                    <br />
                    <label for="id">ID: </label>
                    <input name="id" id="id" type="text" value="49" size="10" maxlength="9" />
		        </div>

		        <div id="itemOption" class="options">
                    <br />
                    <br />
                    <label for="itemId">Item ID: </label>
                    <input name="itemId" id="itemId" type="text" value="49" size="10" maxlength="7" />
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
		        <input id="submit" type="submit" value="Submit" />
		        <p id="productionWarning" style="background-color:#ffa; border: 1px solid black; padding: .5em; margin: .5em;">It is NOT recommended to use any of the create*() methods when using the Production environment as you will be affecting REAL data.</p>

		        </fieldset>
		    </form>
		</div>

		<footer>

		</footer>
	</div>

    <script type="text/javascript">
    //<![CDATA[

        $(document).ready(function() {
            changeEnvironment();
        });

        function changeEnvironment()
        {
            $('body').css('background-repeat', 'no-repeat');
            $('body').css('background-position', 'top right');
            $('body').css('background-attachment', 'fixed');

            $('input[name="apiToken"]').hide();
            $('input[name="secretKey"]').hide();
            $('input[name="buyerId"]').hide();
            $('#productionWarning').hide();

            var selectedEnvironment = $('#environment').val();


            //alert(selectedEnvironment);
            switch (selectedEnvironment) {
                case 'sandbox':
                    $('body').css('background-image', 'url(images/sandbox-banner.png)');

                    $('#sandboxApiToken').show();
                    $('#sandboxSecretKey').show();
                    $('#sandboxBuyerId').show();

                    $('#submit').val('Submit to Sandbox');
                    break;

                case 'staging':
                    $('body').css('background-image', 'url(images/staging-banner.png)');

                    $('#stagingApiToken').show();
                    $('#stagingSecretKey').show();
                    $('#stagingBuyerId').show();

                    $('#submit').val('Submit to Staging');
                    break;

                case 'production':
                    $('body').css('background-image', 'url(images/production-banner.png)');

                    $('#productionApiToken').show();
                    $('#productionSecretKey').show();
                    $('#productionBuyerId').show();

                    $('#submit').val('Submit to Production');

                    $('#productionWarning').show();
                    break;
            }
        }

        function toggleOptions()
        {
            hideAllOptions();

            var selValue = $('#apiMethod').val();


            //alert(selValue);
            switch (selValue) {
                case 'listTicketGroups':
                    $('#eventIdOption').fadeIn();
                    break;

                case 'listEvoPayTransactions':
                    $('#listOptions').fadeIn();
                    $('#accountIdOption').fadeIn();
                    break;

                case 'showEvoPayTransaction':
                    $('#listOptions').fadeOut();
                    $('#accountIdOption').fadeIn();
                    $('#idOption').fadeIn();
                    break;

                case 'createOrderCustomer':
                    $('#clientIdOption').fadeIn();
                    break;

                case 'createFulfillmentOrder':
                case 'completeOrder':
                case 'createFulfillmentOrder':
                    $('#idOption').fadeIn();
                    break;

                case 'updateShipment':
                case 'createShipment':
                    $('#idOption').fadeIn();
                    $('#itemOption').fadeIn();
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

            $('#environmentAndCredentials input:hidden').attr('disabled', true);
            return true;
        }
    //]]>
    </script>
</body>
</html>

<?php

/**
 * Utility function for returning formatted API request info
 *
 * @param TicketEvolution_Webservice $tevofunction
 * @param string $apiMethod
 * @param bool $isException
 * @return string
 */
function _getRequest($tevo, $apiMethod, $isException=true)
{
    $color = ($isException) ? '#f00' : '#ddd';
    $html = '<h2>Actual request for ' . $apiMethod . '() method</h2>' . PHP_EOL
          . '<pre style="background-color: ' . $color . '">' . PHP_EOL
          . print_r ($tevo->getRestClient()->getHttpClient()->getLastRequest(), true)
          . '</pre><br />' . PHP_EOL;

    return $html;
}


/**
 * Utility function for returning formatted API response info
 *
 * @param TicketEvolution_Webservice $tevofunction
 * @param string $apiMethod
 * @return string
 */
function _getResponse($tevo, $apiMethod, $isException=true)
{
    $color = ($isException) ? '#f00' : '#ddd';
    $html = '<h2>Actual response for ' . $apiMethod . '() method</h2>' . PHP_EOL
          . '<pre style="background-color: ' . $color . '">' . PHP_EOL
          . print_r ($tevo->getRestClient()->getHttpClient()->getLastResponse(), true)
          . '</pre><br />' . PHP_EOL;

    return $html;
}
