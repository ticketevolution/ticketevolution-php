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
// $options = array(
//     'page'                          => 1,
//     'per_page'                      => 100,
//     //'updated_at.gte'                => '2012-01-31T19:55:38Z',
//     //'updated_at.lte'                => '2010-07-30T17:41:48Z',
//     //'brokerage_id'                  => 613,
//     //'updated_at.gte'                => '2012-01-08',
//     //'last_event_occurs_at.gte'      => '2012-01-08',
//     //'event_id'                      => 136957,
//     //'price.gte'                     => 220,
//     //'price.lte'                     => 500,
//     //'name'                          => 'Main Office',
//     //'address[locality]'             => 'Scottsdale',
//     //'performances[performer_id]'    => 14155,
//     'performer_id'                  => 14155,
//     //'category_id'                   => 55,
//     //'only_with_upcoming_events'     => 'true',
// );


/**
 * If the form has been submitted filter & validate the input for safety.
 * This is just good practice.
 */
if (isset($_REQUEST['apiMethod'])) {
    /**
     * Filter/validate the input
     *
     * @see Zend_Filter_Input
     */
    require_once 'Zend/Filter/Input.php';

    $filters = array(
        '*' => array(
            'StringTrim',
            'StripTags',
            'StripNewlines',
        )
    );
    $validators = array(
        'apiMethod' => array(
            'Alpha',
            'presence'          => 'required',
            'allowEmpty'        => false,
            'allowWhiteSpace'   => false,
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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $input = new Zend_Filter_Input($filters, $validators, $_POST);
    } else {
        $input = new Zend_Filter_Input($filters, $validators, $_GET);
    }
    //var_dump($_GET);

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

	<title>Ticket Evolution Framework Demo for PHP with Zend Framework</title>
	<meta name="description" content="Demonstration of the Ticket Evolution Framework for PHP with Zend Framework">
	<meta name="author" content="J Cobb <j+ticketevolution@teamonetickets.com>">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="css/style.css?v=2">
	<link type="text/css" href="css/humanity/jquery-ui-1.8.22.custom.css" rel="stylesheet" />
	<link type="text/css" href="css/anytime.c.css" rel="stylesheet" />
	<link type="text/css" href="css/jquery.tagsinput.css" rel="stylesheet" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/demo.js"></script>
    <script type="text/javascript" src="js/anytime.c.js"></script>
    <script type="text/javascript" src="js/jquery.tagsinput.min.js"></script>

</head>
<body>
	<div id="container">
		<header>

		</header>

		<div id="main" role="main">
		    <h1>Demonstration of the Ticket Evolution Framework for PHP with Zend Framework</h1>
		    <p>This is a quick demo of the Ticket Evolution Framework for PHP with Zend Framework which is used to access the <a href="http://developer.ticketevolution.com/overview">Ticket Evolution Web Services API</a>. <a href="http://framework.zend.com/">Zend Framework</a> is an easy-to-use PHP framework that can be used in whole or in parts regardless of whether you program in MVC or procedural style. Simply make sure that the Zend Framework <code>/library</code> folder is in your PHP <code>include_path</code>.</p>
		    <p>All of the <code>list*()</code> methods will return a <code>TicketEvolution_Webservice_ResultSet</code> object with can be easily iterated using simple loops. If you prefer PHP’s <a href="http://www.php.net/manual/en/spl.iterators.php">built-in SPL iterators</a> you will be hapy to know that <code>TicketEvolution_Webservice_ResultSet</code> implements <a href="http://www.php.net/manual/en/class.seekableiterator.php">SeekableIterator</a>.</p>

		    <?php
		        if (isset($input)) {
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
                       . PHP_EOL
                       . '/**' . PHP_EOL
                       . ' * Create a Zend_Config object to pass to TicketEvolution_Webservice' . PHP_EOL
                       . ' */' . PHP_EOL
                       . '$config = new Zend_Config($cfg);' . PHP_EOL
                       . PHP_EOL
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
                    $options = _getOptions($input);
                    //var_dump($options);

                    switch ($apiMethod) {
                        case 'listBrokerages' :
                        case 'listClients' :
                        case 'listClientCompanies' :
                        case 'listUsers' :
                        case 'listSettingsShipping' :
                        case 'listSettingsServiceFees' :
                        case 'listCategories' :
                        case 'listCategoriesDeleted' :
                        case 'listConfigurations' :
                        case 'listEvents' :
                        case 'listEventsDeleted' :
                        case 'listPerformers' :
                        case 'listPerformersDeleted' :
                        case 'listVenues' :
                        case 'listVenuesDeleted' :
                        case 'listTicketGroups' :
                        case 'listOrders' :
                        case 'listQuotes' :
                        case 'listShipments' :
                        case 'listEvoPayAccounts' :
                            _outputListCode($apiMethod, $options);
                            $results = _doList($tevo, $apiMethod, $options);
                            break;

                        case 'showBrokerage' :
                            $showId = $options['brokerage_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'searchBrokerages' :
                            $queryString = $options['q'];
                            unset($options['q']);
                            _outputSearchCode($apiMethod, $queryString, $options);
                            $results = _doSearch($tevo, $apiMethod, $queryString, $options);
                            break;

                        case 'showClient' :
                            $showId = $options['client_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'createClients' :
                            $client = new stdClass;
                            $client->name = $options['name'];
                            $client->office_id = $options['office_id'];
                            $client->tags = array(explode(',', $options['tags']));

                            // Clients must be passed in an array, even if there is only one
                            $clients[] = $client;

                            // Display the code
                            echo '$client = new stdClass;' . PHP_EOL
                               . '$client->name = \'' . $options['name'] . '\';' . PHP_EOL
                               . '$client->office_id = \'' . $options['office_id'] . '\';' . PHP_EOL
                               . '$client->tags = array(explode(\',\', ' . $options['tags'] . '));' . PHP_EOL
                               . PHP_EOL
                               . '// Clients must be passed in an array, even if there is only one' . PHP_EOL
                               . '$clients[] = $client;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($clients);' . PHP_EOL
                            ;

                            $results = _doCreate($tevo, $apiMethod, $clients);
                            break;

                        case 'updateClient' :
                            $updateId = $options['client_id'];

                            $client = new stdClass;
                            $client->name = $options['name'];
                            $client->office_id = $options['office_id'];
                            $client->primary_shipping_address_id = $options['primary_shipping_address_id'];
                            $client->primary_credit_card_id = $options['primary_credit_card_id'];
                            $client->tags = array(explode(',', $options['tags']));

                            // Display the code
                            echo '$client = new stdClass;' . PHP_EOL
                               . '$client->name = \'' . $options['name'] . '\';' . PHP_EOL
                               . '$client->office_id = \'' . $options['office_id'] . '\';' . PHP_EOL
                               . '$client->primary_shipping_address_id = \'' . $options['primary_shipping_address_id'] . '\';' . PHP_EOL
                               . '$client->primary_credit_card_id = \'' . $options['primary_credit_card_id'] . '\';' . PHP_EOL
                               . '$client->tags = array(explode(\',\', ' . $options['tags'] . '));' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '(' . $updateId . ', $client);' . PHP_EOL
                            ;

                            $results = _doUpdate($tevo, $apiMethod, $updateId, $client);
                            break;

                        case 'showClientCompany' :
                            $showId = $options['company_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'createClientCompanies' :
                            $company = new stdClass;
                            $company->name = $options['name'];

                            // Companies must be passed in an array, even if there is only one
                            $companies[] = $company;


                            // Display the code
                            echo '$company = new stdClass;' . PHP_EOL
                               . '$company->name = \'' . $options['name'] . '\';' . PHP_EOL
                               . PHP_EOL
                               . '// Companies must be passed in an array, even if there is only one' . PHP_EOL
                               . '$companies[] = $company;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($companies);' . PHP_EOL
                            ;

                            $results = _doCreate($tevo, $apiMethod, $companies);
                            break;

                        case 'updateClientCompany' :
                            $updateId = $options['company_id'];

                            $company = new stdClass;
                            $company->name = $options['name'];

                            // Display the code
                            echo '$company = new stdClass;' . PHP_EOL
                               . '$company->name = \'' . $options['name'] . '\';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '(' . $updateId . ', $company);' . PHP_EOL
                            ;

                            $results = _doUpdate($tevo, $apiMethod, $updateId, $company);
                            break;

                        case 'listClientAddresses':
                        case 'listClientPhoneNumbers' :
                        case 'listClientEmailAddresses' :
                        case 'listClientCreditCards' :
                            $listId = $options['client_id'];
                            unset($options['client_id']);
                            _outputListByIdCode($apiMethod, $listId, $options);
                            $results = _doListById($tevo, $apiMethod, $listId, $options);
                            break;

                        case 'showClientAddress':
                            $client_id = $options['client_id'];
                            $showId = $options['address_id'];

                            _outputShowByIdCode($apiMethod, $client_id, $showId);
                            $results = _doShowById($tevo, $apiMethod, $client_id, $showId);
                            break;

                        case 'createClientAddresses':
                            $client_id = $options['client_id'];

                            $address = new stdClass;
                            $address->label = $options['label'];
                            $address->name = $options['name'];
                            $address->company = $options['company'];
                            $address->street_address = $options['street_address'];
                            $address->extended_address = $options['extended_address'];
                            $address->locality = $options['locality'];
                            $address->region = $options['region'];
                            $address->postal_code = $options['postal_code'];
                            $address->country_code = $options['country_code'];
                            $address->primary = (bool) $options['primary'];

                            // Addresses must be passed in an array, even if there is only one
                            $addresses[] = $address;

                            // Display the code
                            echo '$address = new stdClass;' . PHP_EOL
                               . '$address->label = \'' . $options['label'] . '\';' . PHP_EOL
                               . '$address->name = \'' . $options['name'] . '\';' . PHP_EOL
                               . '$address->company = \'' . $options['company'] . '\';' . PHP_EOL
                               . '$address->street_address = \'' . $options['street_address'] . '\';' . PHP_EOL
                               . '$address->extended_address = \'' . $options['extended_address'] . '\';' . PHP_EOL
                               . '$address->locality = \'' . $options['locality'] . '\';' . PHP_EOL
                               . '$address->region = \'' . $options['region'] . '\';' . PHP_EOL
                               . '$address->postal_code = \'' . $options['postal_code'] . '\';' . PHP_EOL
                               . '$address->country_code = \'' . $options['country_code'] . '\';' . PHP_EOL
                               . '$address->primary = (bool) ' . $options['primary'] . ';' . PHP_EOL
                               . PHP_EOL
                               . '// Addresses must be passed in an array, even if there is only one' . PHP_EOL
                               . '$addresses[] = $address;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '($addresses);' . PHP_EOL
                            ;

                            $results = _doCreateById($tevo, $apiMethod, $client_id, $addresses);
                            break;

                        case 'updateClientAddress' :
                            $itemId = $options['client_id'];
                            $updateId = $options['address_id'];

                            $address = new stdClass;
                            $address->label = $options['label'];
                            $address->name = $options['name'];
                            $address->company = $options['company'];
                            $address->street_address = $options['street_address'];
                            $address->extended_address = $options['extended_address'];
                            $address->locality = $options['locality'];
                            $address->region = $options['region'];
                            $address->postal_code = $options['postal_code'];
                            $address->country_code = $options['country_code'];
                            $address->primary = (bool) $options['primary'];

                            // Display the code
                            echo '$address = new stdClass;' . PHP_EOL
                               . '$address->label = \'' . $options['label'] . '\';' . PHP_EOL
                               . '$address->name = \'' . $options['name'] . '\';' . PHP_EOL
                               . '$address->company = \'' . $options['company'] . '\';' . PHP_EOL
                               . '$address->street_address = \'' . $options['street_address'] . '\';' . PHP_EOL
                               . '$address->extended_address = \'' . $options['extended_address'] . '\';' . PHP_EOL
                               . '$address->locality = \'' . $options['locality'] . '\';' . PHP_EOL
                               . '$address->region = \'' . $options['region'] . '\';' . PHP_EOL
                               . '$address->postal_code = \'' . $options['postal_code'] . '\';' . PHP_EOL
                               . '$address->country_code = \'' . $options['country_code'] . '\';' . PHP_EOL
                               . '$address->primary = (bool) ' . $options['primary'] . ';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '(' . $itemId . ', ' . $updateId . ', $address);' . PHP_EOL
                            ;

                            $results = _doUpdateById($tevo, $apiMethod, $itemId, $updateId, $address);
                            break;

                        case 'showClientPhoneNumber':
                            $client_id = $options['client_id'];
                            $showId = $options['phone_number_id'];

                            _outputShowByIdCode($apiMethod, $client_id, $showId);
                            $results = _doShowById($tevo, $apiMethod, $client_id, $showId);
                            break;

                        case 'createClientPhoneNumbers':
                            $client_id = $options['client_id'];

                            $phoneNumber = new stdClass;
                            $phoneNumber->label = $options['label'];
                            $phoneNumber->country_code = $options['country_code'];
                            $phoneNumber->number = $options['number'];
                            $phoneNumber->extension = $options['extension'];

                            // Phone Numbers must be passed in an array, even if there is only one
                            $phoneNumbers[] = $phoneNumber;

                            // Display the code
                            echo '$phoneNumber = new stdClass;' . PHP_EOL
                               . '$phoneNumber->label = \'' . $options['label'] . '\';' . PHP_EOL
                               . '$phoneNumber->country_code = \'' . $options['country_code'] . '\';' . PHP_EOL
                               . '$phoneNumber->number = \'' . $options['number'] . '\';' . PHP_EOL
                               . '$phoneNumber->extension = \'' . $options['extension'] . '\';' . PHP_EOL
                               . PHP_EOL
                               . '// Phone Numbers must be passed in an array, even if there is only one' . PHP_EOL
                               . '$phoneNumbers[] = $phoneNumber;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '(' . $client_id . ', $phoneNumbers);' . PHP_EOL
                            ;

                            $results = _doCreateById($tevo, $apiMethod, $client_id, $phoneNumbers);
                            break;

                        case 'updateClientPhoneNumber' :
                            $itemId = $options['client_id'];
                            $updateId = $options['phone_number_id'];

                            $phoneNumber = new stdClass;
                            $phoneNumber->label = $options['label'];
                            $phoneNumber->country_code = $options['country_code'];
                            $phoneNumber->number = $options['number'];
                            $phoneNumber->extension = $options['extension'];

                            // Display the code
                            echo '$phoneNumber = new stdClass;' . PHP_EOL
                               . '$phoneNumber->label = \'' . $options['label'] . '\';' . PHP_EOL
                               . '$phoneNumber->country_code = \'' . $options['country_code'] . '\';' . PHP_EOL
                               . '$phoneNumber->number = \'' . $options['number'] . '\';' . PHP_EOL
                               . '$phoneNumber->extension = \'' . $options['extension'] . '\';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '(' . $itemId . ', ' . $updateId . ', $phoneNumber);' . PHP_EOL
                            ;

                            $results = _doUpdateById($tevo, $apiMethod, $itemId, $updateId, $phoneNumber);
                            break;

                        case 'showClientEmailAddress':
                            $client_id = $options['client_id'];
                            $showId = $options['email_address_id'];

                            _outputShowByIdCode($apiMethod, $client_id, $showId);
                            $results = _doShowById($tevo, $apiMethod, $client_id, $showId);
                            break;

                        case 'createClientEmailAddresses':
                            $client_id = $options['client_id'];

                            $emailAddress = new stdClass;
                            $emailAddress->label = $options['label'];
                            $emailAddress->address = $options['address'];

                            // Email Addresses must be passed in an array, even if there is only one
                            $emailAddresses[] = $emailAddress;

                            // Display the code
                            echo '$emailAddress = new stdClass;' . PHP_EOL
                               . '$emailAddress->label = \'' . $options['label'] . '\';' . PHP_EOL
                               . '$emailAddress->address = \'' . $options['address'] . '\';' . PHP_EOL
                               . PHP_EOL
                               . '// Email Addresses must be passed in an array, even if there is only one' . PHP_EOL
                               . '$emailAddresses[] = $emailAddress;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '(' . $client_id . ', $emailAddresses);' . PHP_EOL
                            ;

                            $results = _doCreateById($tevo, $apiMethod, $client_id, $emailAddresses);
                            break;

                        case 'updateClientEmailAddress' :
                            $itemId = $options['client_id'];
                            $updateId = $options['email_address_id'];

                            $emailAddress = new stdClass;
                            $emailAddress->label = $options['label'];
                            $emailAddress->address = $options['address'];

                            // Email Addresses must be passed in an array, even if there is only one
                            $emailAddresses[] = $emailAddress;

                            // Display the code
                            echo '$emailAddress = new stdClass;' . PHP_EOL
                               . '$emailAddress->label = \'' . $options['label'] . '\';' . PHP_EOL
                               . '$emailAddress->address = \'' . $options['address'] . '\';' . PHP_EOL
                               . PHP_EOL
                               . '// Email Addresses must be passed in an array, even if there is only one' . PHP_EOL
                               . '$emailAddresses[] = $emailAddress;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '(' . $itemId . ', ' . $updateId . ', $emailAddresses);' . PHP_EOL
                            ;

                            $results = _doUpdateById($tevo, $apiMethod, $itemId, $updateId, $emailAddresses);
                            break;

                        case 'showClientCreditCard':
                            $client_id = $options['client_id'];
                            $showId = $options['credit_card_id'];

                            _outputShowByIdCode($apiMethod, $client_id, $showId);
                            $results = _doShowById($tevo, $apiMethod, $client_id, $showId);
                            break;

                        case 'createClientCreditCards':
                            $client_id = $options['client_id'];

                            $creditCard = new stdClass;
                            $creditCard->address_id = $options['address_id'];
                            $creditCard->phone_number_id = $options['phone_number_id'];
                            $creditCard->name = $options['name'];
                            $creditCard->number = $options['number'];
                            $creditCard->expiration_month = $options['expiration_month'];
                            $creditCard->expiration_year = $options['expiration_year'];
                            $creditCard->verification_code = $options['verification_code'];
                            $creditCard->ip_address = $options['ip_address'];

                            // Credit Cards must be passed in an array, even if there is only one
                            $creditCards[] = $creditCard;

                            // Display the code
                            echo '$creditCard = new stdClass;' . PHP_EOL
                               . '$creditCard->address_id = \'' . $options['address_id'] . '\';' . PHP_EOL
                               . '$creditCard->phone_number_id = \'' . $options['phone_number_id'] . '\';' . PHP_EOL
                               . '$creditCard->name = \'' . $options['name'] . '\';' . PHP_EOL
                               . '$creditCard->number = \'' . $options['number'] . '\';' . PHP_EOL
                               . '$creditCard->expiration_month = \'' . $options['expiration_month'] . '\';' . PHP_EOL
                               . '$creditCard->expiration_year = \'' . $options['expiration_year'] . '\';' . PHP_EOL
                               . '$creditCard->verification_code = \'' . $options['verification_code'] . '\';' . PHP_EOL
                               . '$creditCard->ip_address = \'' . $options['ip_address'] . '\';' . PHP_EOL
                               . PHP_EOL
                               . '// Credit Cards must be passed in an array, even if there is only one' . PHP_EOL
                               . '$creditCards[] = $creditCard;' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '(' . $client_id . ', $creditCards);' . PHP_EOL
                            ;

                            $results = _doCreateById($tevo, $apiMethod, $client_id, $creditCards);
                            break;

                        case 'updateClientCreditCard' :
                            $itemId = $options['client_id'];
                            $updateId = $options['credit_card_id'];

                            $creditCard = new stdClass;
                            $creditCard->address_id = $options['address_id'];
                            $creditCard->phone_number_id = $options['phone_number_id'];
                            $creditCard->name = $options['name'];
                            $creditCard->number = $options['number'];
                            $creditCard->expiration_month = $options['expiration_month'];
                            $creditCard->expiration_year = $options['expiration_year'];
                            $creditCard->verification_code = $options['verification_code'];
                            $creditCard->ip_address = $options['ip_address'];

                            // Display the code
                            echo '$creditCard = new stdClass;' . PHP_EOL
                               . '$creditCard->address_id = \'' . $options['address_id'] . '\';' . PHP_EOL
                               . '$creditCard->phone_number_id = \'' . $options['phone_number_id'] . '\';' . PHP_EOL
                               . '$creditCard->name = \'' . $options['name'] . '\';' . PHP_EOL
                               . '$creditCard->number = \'' . $options['number'] . '\';' . PHP_EOL
                               . '$creditCard->expiration_month = \'' . $options['expiration_month'] . '\';' . PHP_EOL
                               . '$creditCard->expiration_year = \'' . $options['expiration_year'] . '\';' . PHP_EOL
                               . '$creditCard->verification_code = \'' . $options['verification_code'] . '\';' . PHP_EOL
                               . '$creditCard->ip_address = \'' . $options['ip_address'] . '\';' . PHP_EOL
                               . PHP_EOL
                               . '$results = $tevo->' . $apiMethod . '(' . $itemId . ', ' . $updateId . ', $creditCard);' . PHP_EOL
                            ;

                            $results = _doUpdateById($tevo, $apiMethod, $itemId, $updateId, $creditCard);
                            break;

                        case 'showOffice' :
                            $showId = $options['office_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'searchQuotes' :
                        case 'searchOffices' :
                        case 'searchUsers' :
                        case 'searchPerformers' :
                        case 'searchVenues' :
                        case 'search' :
                            $queryTerm = $options['q'];
                            _outputSearchCode($apiMethod, $queryTerm, $options);
                            $results = _doSearch($tevo, $apiMethod, $queryTerm, $options);
                            break;

                        case 'showUser' :
                            $showId = $options['user_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'showCategory' :
                            $showId = $options['category_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'showConfiguration' :
                            $showId = $options['configuration_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'showEvent' :
                            $showId = $options['event_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'showPerformer' :
                            $showId = $options['performer_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'showVenue' :
                            $showId = $options['venue_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'showTicketGroup' :
                            $showId = $options['ticket_group_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'showOrder' :
                            $showId = $options['order_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'showQuote' :
                            $showId = $options['quote_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'showShipment' :
                            $showId = $options['shipment_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'createShipments' :
                            if ($_FILES['airbill']['error'] == 0) {
                                $options['airbill'] = base64_encode(file_get_contents($_FILES['airbill']['tmp_name']));
                            } else {
                                // Throw Exception
                            }

                            echo 'if ($_FILES[\'airbill\'][\'error\'] == 0) {' . PHP_EOL
                               . '    $options[\'airbill\'] = base64_encode(file_get_contents($_FILES[\'airbill\'][\'tmp_name\']));' . PHP_EOL
                               . '} else {' . PHP_EOL
                               . '    throw new Exception(\'You had an error uploading your airbill.\');' . PHP_EOL
                               . '}' . PHP_EOL
                               . PHP_EOL
                            ;
                            _outputListCode($apiMethod, $options);

                            $results = _doCreate($tevo, $apiMethod, $options);
                            break;

                        case 'updateShipment' :
                            if ($_FILES['airbill']['error'] == 0) {
                                $options['airbill'] = base64_encode(file_get_contents($_FILES['airbill']['tmp_name']));
                            } else {
                                // Throw Exception
                            }

                            echo 'if ($_FILES[\'airbill\'][\'error\'] == 0) {' . PHP_EOL
                               . '    $options[\'airbill\'] = base64_encode(file_get_contents($_FILES[\'airbill\'][\'tmp_name\']));' . PHP_EOL
                               . '} else {' . PHP_EOL
                               . '    throw new Exception(\'You had an error uploading your airbill.\');' . PHP_EOL
                               . '}' . PHP_EOL
                               . PHP_EOL
                            ;
                            _outputListCode($apiMethod, $options);

                            $results = _doUpdate($tevo, $apiMethod, $options);
                            break;

                        case 'showEvoPayAccount' :
                            $showId = $options['account_id'];
                            _outputShowCode($apiMethod, $showId);
                            $results = _doShow($tevo, $apiMethod, $showId);
                            break;

                        case 'listEvoPayTransactions' :
                            $listId = $options['account_id'];
                            unset($options['account_id']);
                            _outputListByIdCode($apiMethod, $listId, $options);
                            $results = _doListById($tevo, $apiMethod, $listId, $options);
                            break;

                        case 'showEvoPayTransaction' :
                            $accountId = $options['account_id'];
                            $transactionId = $options['transaction_id'];
                            _outputShowByIdCode($apiMethod, $accountId, $transactionId);
                            $results = _doShowById($tevo, $apiMethod, $accountId, $transactionId);
                            break;

                        case 'createOrdersClient' :
                            $payment = new stdClass;
                            $payment->type = $options['payments'];

                            unset($options['payments']);
                            $options['payments'][] = $payment;

                            echo '$options = array(' . PHP_EOL;
                            foreach( $options as $key => $val) {
                                if (!is_array($val) && !is_object($val)) {
                                    echo '    \'' . $key . '\' => ' . $val . ',' . PHP_EOL;
                                }
                            }
                            echo ');' . PHP_EOL . PHP_EOL;

                            $item = new stdClass;
                            $item->ticket_group_id = $_REQUEST['items'][0]['ticket_group_id'];
                            $item->quantity = (int) $_REQUEST['items'][0]['quantity'];
                            $item->price = $_REQUEST['items'][0]['price'];
                            $items[] = $item;

                            if (!empty($_REQUEST['items'][1]['ticket_group_id'])) {
                                $item = new stdClass;
                                $item->ticket_group_id = $_REQUEST['items'][1]['ticket_group_id'];
                                $item->quantity = (int) $_REQUEST['items'][1]['quantity'];
                                $item->price = $_REQUEST['items'][1]['price'];
                                $items[] = $item;
                            }
                            $options['items'] = $items;

                            if (empty($_REQUEST['shipping_address_id'])) {
                                $address = new stdClass;
                                $address->label = $_REQUEST['shipping_address']['label'];
                                $address->name = $_REQUEST['shipping_address']['name'];
                                $address->company = $_REQUEST['shipping_address']['company'];
                                $address->street_address = $_REQUEST['shipping_address']['street_address'];
                                $address->extended_address = $_REQUEST['shipping_address']['extended_address'];
                                $address->locality = $_REQUEST['shipping_address']['locality'];
                                $address->region = $_REQUEST['shipping_address']['region'];
                                $address->postal_code = $_REQUEST['shipping_address']['postal_code'];
                                $address->country_code = $_REQUEST['shipping_address']['country_code'];

                                $options['shipping_address'] = $address;

                                unset($options['shipping_address_id']);
                            } else {
                                unset($options['shipping_address']);
                            }

                            if (empty($_REQUEST['billing_address_id'])) {
                                $address = new stdClass;
                                $address->label = $_REQUEST['billing_address']['label'];
                                $address->name = $_REQUEST['billing_address']['name'];
                                $address->company = $_REQUEST['billing_address']['company'];
                                $address->street_address = $_REQUEST['billing_address']['street_address'];
                                $address->extended_address = $_REQUEST['billing_address']['extended_address'];
                                $address->locality = $_REQUEST['billing_address']['locality'];
                                $address->region = $_REQUEST['billing_address']['region'];
                                $address->postal_code = $_REQUEST['billing_address']['postal_code'];
                                $address->country_code = $_REQUEST['billing_address']['country_code'];

                                $options['billing_address'] = $address;

                                unset($options['billing_address_id']);
                            } else {
                                unset($options['billing_address']);
                            }

                            $orders[] = $options;

                            echo '$payment = new stdClass;' . PHP_EOL
                               . '$payment->type = ' . $_REQUEST['payments'] . ';' . PHP_EOL

                               . '$options[\'payments\'][] = $payment;' . PHP_EOL
                               . PHP_EOL
                            ;

                            echo '$item = new stdClass;' . PHP_EOL
                               . '$item->ticket_group_id = ' . $_REQUEST['items'][0]['ticket_group_id'] . ';' . PHP_EOL
                               . '$item->quantity = (int) ' . $_REQUEST['items'][0]['quantity'] . ';' . PHP_EOL
                               . '$item->price = ' . $_REQUEST['items'][0]['price'] . ';' . PHP_EOL
                               . '$options[\'items\'][] = $item;' . PHP_EOL
                               . PHP_EOL
                            ;
                            if (!empty($_REQUEST['items'][1]['ticket_group_id'])) {
                                echo '$item = new stdClass;' . PHP_EOL
                                   . '$item->ticket_group_id = ' . $_REQUEST['items'][1]['ticket_group_id'] . ';' . PHP_EOL
                                   . '$item->quantity = (int) ' . $_REQUEST['items'][1]['quantity'] . ';' . PHP_EOL
                                   . '$item->price = ' . $_REQUEST['items'][1]['price'] . ';' . PHP_EOL
                                   . '$options[\'items\'][] = $item;' . PHP_EOL
                                   . PHP_EOL
                                ;
                            }

                            if (empty($_REQUEST['shipping_address_id'])) {
                                echo '$address = new stdClass;' . PHP_EOL
                                   . '$address->label = \'' . $_REQUEST['shipping_address']['label'] . '\';' . PHP_EOL
                                   . '$address->name = \'' . $_REQUEST['shipping_address']['name'] . '\';' . PHP_EOL
                                   . '$address->company = \'' . $_REQUEST['shipping_address']['company'] . '\';' . PHP_EOL
                                   . '$address->street_address = \'' . $_REQUEST['shipping_address']['street_address'] . '\';' . PHP_EOL
                                   . '$address->extended_address = \'' . $_REQUEST['shipping_address']['extended_address'] . '\';' . PHP_EOL
                                   . '$address->locality = \'' . $_REQUEST['shipping_address']['locality'] . '\';' . PHP_EOL
                                   . '$address->region = \'' . $_REQUEST['shipping_address']['region'] . '\';' . PHP_EOL
                                   . '$address->postal_code = \'' . $_REQUEST['shipping_address']['postal_code'] . '\';' . PHP_EOL
                                   . '$address->country_code = \'' . $_REQUEST['shipping_address']['country_code'] . '\';' . PHP_EOL
                                   . PHP_EOL
                                   . '$options[\'shipping_address\'] = $address;' . PHP_EOL
                                   . PHP_EOL
                                ;
                            }

                            if (empty($_REQUEST['billing_address_id'])) {
                                echo '$address = new stdClass;' . PHP_EOL
                                   . '$address->label = \'' . $_REQUEST['billing_address']['label'] . '\';' . PHP_EOL
                                   . '$address->name = \'' . $_REQUEST['billing_address']['name'] . '\';' . PHP_EOL
                                   . '$address->company = \'' . $_REQUEST['billing_address']['company'] . '\';' . PHP_EOL
                                   . '$address->street_address = \'' . $_REQUEST['billing_address']['street_address'] . '\';' . PHP_EOL
                                   . '$address->extended_address = \'' . $_REQUEST['billing_address']['extended_address'] . '\';' . PHP_EOL
                                   . '$address->locality = \'' . $_REQUEST['billing_address']['locality'] . '\';' . PHP_EOL
                                   . '$address->region = \'' . $_REQUEST['billing_address']['region'] . '\';' . PHP_EOL
                                   . '$address->postal_code = \'' . $_REQUEST['billing_address']['postal_code'] . '\';' . PHP_EOL
                                   . '$address->country_code = \'' . $_REQUEST['billing_address']['country_code'] . '\';' . PHP_EOL
                                   . PHP_EOL
                                   . '$options[\'billing_address\'] = $address;' . PHP_EOL
                                   . PHP_EOL
                                ;
                            }

                            echo '$orders[] = $options;' . PHP_EOL;

                            //var_dump($orders);
                            $results = _doCreate($tevo, 'createOrders', $orders);
                            break;


                        case 'updateOrder' :
                            $updateId = $options['order_id'];

                            $order = new stdClass;
                            $order->po_number = $options['po_number'];
                            $order->invoice_number = $options['invoice_number'];
                            $order->instructions = $options['instructions'];

                            if (empty($_REQUEST['shipping_address_id'])) {
                                $address = new stdClass;
                                $address->label = $_REQUEST['shipping_address']['label'];
                                $address->name = $_REQUEST['shipping_address']['name'];
                                $address->company = $_REQUEST['shipping_address']['company'];
                                $address->street_address = $_REQUEST['shipping_address']['street_address'];
                                $address->extended_address = $_REQUEST['shipping_address']['extended_address'];
                                $address->locality = $_REQUEST['shipping_address']['locality'];
                                $address->region = $_REQUEST['shipping_address']['region'];
                                $address->postal_code = $_REQUEST['shipping_address']['postal_code'];
                                $address->country_code = $_REQUEST['shipping_address']['country_code'];

                                $order->shipping_address = $address;

                            } else {
                                $order->shipping_address_id = $options['shipping_address_id'];
                            }

                            if (empty($_REQUEST['billing_address_id'])) {
                                $address = new stdClass;
                                $address->label = $_REQUEST['billing_address']['label'];
                                $address->name = $_REQUEST['billing_address']['name'];
                                $address->company = $_REQUEST['billing_address']['company'];
                                $address->street_address = $_REQUEST['billing_address']['street_address'];
                                $address->extended_address = $_REQUEST['billing_address']['extended_address'];
                                $address->locality = $_REQUEST['billing_address']['locality'];
                                $address->region = $_REQUEST['billing_address']['region'];
                                $address->postal_code = $_REQUEST['billing_address']['postal_code'];
                                $address->country_code = $_REQUEST['billing_address']['country_code'];

                                $order->billing_address = $address;

                            } else {
                                $order->billing_address_id = $options['billing_address_id'];
                            }


                            echo '$order = new stdClass;' . PHP_EOL
                               . '$order->po_number = ' . $options['po_number'] . ';' . PHP_EOL
                               . '$order->invoice_number = ' . $options['invoice_number'] . ';' . PHP_EOL
                               . '$order->instructions = \'' . $options['instructions'] . '\';' . PHP_EOL
                               . PHP_EOL
                               ;
                            if (empty($_REQUEST['shipping_address_id'])) {
                                echo '$address = new stdClass;' . PHP_EOL
                                   . '$address->label = \'' . $_REQUEST['shipping_address']['label'] . '\';' . PHP_EOL
                                   . '$address->name = \'' . $_REQUEST['shipping_address']['name'] . '\';' . PHP_EOL
                                   . '$address->company = \'' . $_REQUEST['shipping_address']['company'] . '\';' . PHP_EOL
                                   . '$address->street_address = \'' . $_REQUEST['shipping_address']['street_address'] . '\';' . PHP_EOL
                                   . '$address->extended_address = \'' . $_REQUEST['shipping_address']['extended_address'] . '\';' . PHP_EOL
                                   . '$address->locality = \'' . $_REQUEST['shipping_address']['locality'] . '\';' . PHP_EOL
                                   . '$address->region = \'' . $_REQUEST['shipping_address']['region'] . '\';' . PHP_EOL
                                   . '$address->postal_code = \'' . $_REQUEST['shipping_address']['postal_code'] . '\';' . PHP_EOL
                                   . '$address->country_code = \'' . $_REQUEST['shipping_address']['country_code'] . '\';' . PHP_EOL
                                   . PHP_EOL
                                   . '$order->shipping_address = $address;' . PHP_EOL
                                   . PHP_EOL
                                ;
                            } else {
                                echo '$order->shipping_address_id = ' . $options['shipping_address_id'] . ';' . PHP_EOL;
                            }

                            if (empty($_REQUEST['billing_address_id'])) {
                                echo '$address = new stdClass;' . PHP_EOL
                                   . '$address->label = \'' . $_REQUEST['billing_address']['label'] . '\';' . PHP_EOL
                                   . '$address->name = \'' . $_REQUEST['billing_address']['name'] . '\';' . PHP_EOL
                                   . '$address->company = \'' . $_REQUEST['billing_address']['company'] . '\';' . PHP_EOL
                                   . '$address->street_address = \'' . $_REQUEST['billing_address']['street_address'] . '\';' . PHP_EOL
                                   . '$address->extended_address = \'' . $_REQUEST['billing_address']['extended_address'] . '\';' . PHP_EOL
                                   . '$address->locality = \'' . $_REQUEST['billing_address']['locality'] . '\';' . PHP_EOL
                                   . '$address->region = \'' . $_REQUEST['billing_address']['region'] . '\';' . PHP_EOL
                                   . '$address->postal_code = \'' . $_REQUEST['billing_address']['postal_code'] . '\';' . PHP_EOL
                                   . '$address->country_code = \'' . $_REQUEST['billing_address']['country_code'] . '\';' . PHP_EOL
                                   . PHP_EOL
                                   . '$order->billing_address = $address;' . PHP_EOL
                                   . PHP_EOL
                                ;
                            } else {
                                echo '$order->billing_address_id = ' . $options['billing_address_id'] . ';' . PHP_EOL;
                            }

                            echo '$results = $tevo->' . $apiMethod . '(' . $updateId . ', $order);' . PHP_EOL
                               ;


                            //var_dump($orders);
                            $results = _doUpdate($tevo, 'updateOrder', $updateId, $order);
                            break;


                        case 'acceptOrder' :
                            $orderId = $options['order_id'];
                            $reviewerId = $options['reviewer_id'];

                            echo '$results = $tevo->' . $apiMethod . '(' . $orderId . ', ' . $reviewerId . ');' . PHP_EOL;

                            $results = _doOther($tevo, 'acceptOrder', $orderId, $reviewerId);
                            break;


                        case 'rejectOrder' :
                            $orderId = $options['order_id'];
                            $reviewerId = $options['reviewer_id'];
                            $rejectionReason = $options['rejection_reason'];

                            echo '$results = $tevo->' . $apiMethod . '(' . $orderId . ', ' . $reviewerId . ', \'' . $rejectionReason . '\');' . PHP_EOL;

                            $results = _doOther($tevo, 'rejectOrder', $orderId, $reviewerId, $rejectionReason);
                            break;


                        case 'completeOrder' :
                            $orderId = $options['order_id'];

                            echo '$results = $tevo->' . $apiMethod . '(' . $orderId . ');' . PHP_EOL;

                            $results = _doOther($tevo, 'completeOrder', $orderId);
                            break;












                        default:

                            // Display the code
                            echo '$options = array(' . PHP_EOL;
                            foreach ($options as $key => $val) {
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
		    <form action="index.php" method="get" target="_top" id="APItest" onsubmit="checkForm();" enctype="multipart/form-data">
		        <fieldset id="environmentAndCredentials">
                    <legend>Environment and Credentials</legend>

                    <div>
                        <label>Environment:</label>
                        <select name="environment" id="environment" onchange="changeEnvironment();">
                            <option value="sandbox"<?php if (@$input->environment == 'sandbox') { echo ' selected="selected"';} ?>>Sandbox</option>
                            <option value="staging"<?php if (@$input->environment == 'staging') { echo ' selected="selected"';} ?>>Staging</option>
                            <option value="production"<?php if (@$input->environment == 'production') { echo ' selected="selected"';} ?>>Production</option>
                        </select>
                    </div>

                    <div>
                        <label for="apiVersion">API Version:</label>
                        <input name="apiVersion" id="apiVersion" type="text" value="9" size="2" pattern=".{1,2}" readonly="readonly" />
                    </div>

                    <div>
                        <label>API Token:</label>
                        <input class="apiToken" name="apiToken" id="sandboxApiToken" type="text" value="<?php if (!empty($sandbox['apiToken'])) {echo $sandbox['apiToken'];} ?>" pattern=".{30,40}" />
                        <input class="apiToken" name="apiToken" id="stagingApiToken" type="text" value="<?php if (!empty($staging['apiToken'])) {echo $staging['apiToken'];} ?>" pattern=".{30,40}" />
                        <input class="apiToken" name="apiToken" id="productionApiToken" type="text" value="<?php if (!empty($production['apiToken'])) {echo $production['apiToken'];} ?>" pattern=".{30,40}" />
                    </div>

                    <div>
                        <label>API Secret:</label>
                        <input class="secretKey" name="secretKey" id="sandboxSecretKey" type="text" value="<?php if (!empty($sandbox['secretKey'])) {echo $sandbox['secretKey'];} ?>" pattern=".{40,50}" />
                        <input class="secretKey" name="secretKey" id="stagingSecretKey" type="text" value="<?php if (!empty($staging['secretKey'])) {echo $staging['secretKey'];} ?>" pattern=".{40,50}" />
                        <input class="secretKey" name="secretKey" id="productionSecretKey" type="text" value="<?php if (!empty($production['secretKey'])) {echo $production['secretKey'];} ?>" pattern=".{40,50}" />
                    </div>

                    <div>
                        <label>Buyer ID (a/k/a officeId):</label>
                        <input name="buyerId" id="sandboxBuyerId" type="text" value="<?php if (!empty($sandbox['buyerId'])) {echo $sandbox['buyerId'];} ?>" pattern=".{1,6}" />
                        <input name="buyerId" id="stagingBuyerId" type="text" value="<?php if (!empty($staging['buyerId'])) {echo $staging['buyerId'];} ?>" pattern=".{1,6}" />
                        <input name="buyerId" id="productionBuyerId" type="text" value="<?php if (!empty($production['buyerId'])) {echo $production['buyerId'];} ?>" pattern=".{1,6}" />
                    </div>
                </fieldset>

		        <fieldset>
                    <legend>Method</legend>

                    <div>
                        <label for="apiMethod" accesskey="m">Framework Method:</label>
                        <select id="apiMethod" name="apiMethod" size="1" onchange="toggleOptions();">
                            <option label="Select a method…" value="">Select a method…</option>

                            <optgroup label="Brokerage Resources">
                                <optgroup label="Brokerages Methods">
                                    <option label="listBrokerages()" value="listBrokerages">listBrokerages()</option>
                                    <option label="showBrokerage()" value="showBrokerage">showBrokerage()</option>
                                    <option label="searchBrokerages()" value="searchBrokerages">searchBrokerages()</option>
                                </optgroup>

                                <optgroup label="Settings Methods">
                                    <option label="listSettingsShipping()" value="listSettingsShipping">listSettingsShipping()</option>
                                    <option label="listSettingsServiceFees()" value="listSettingsServiceFees">listSettingsServiceFees()</option>
                                </optgroup>

                                <optgroup label="Offices Methods">
                                    <option label="listOffices()" value="listOffices">listOffices()</option>
                                    <option label="showOffice()" value="showOffice">showOffice()</option>
                                    <option label="searchOffices()" value="searchOffices">searchOffices()</option>
                                </optgroup>

                                <optgroup label="Users Methods">
                                    <option label="listUsers()" value="listUsers">listUsers()</option>
                                    <option label="showUser()" value="showUser">showUser()</option>
                                    <option label="searchUsers()" value="searchUsers">searchUsers()</option>
                                </optgroup>
                            </optgroup>

                            <optgroup label="Clients Resources">
                                <optgroup label="Clients Methods">
                                    <option label="listClients()" value="listClients">listClients()</option>
                                    <option label="showClient()" value="showClient">showClient()</option>
                                    <option label="createClients()" value="createClients">createClients()</option>
                                    <option label="updateClient()" value="updateClient">updateClient()</option>
                                </optgroup>

                                <optgroup label="Client Company Methods">
                                    <option label="listClientCompanies()" value="listClientCompanies">listClientCompanies()</option>
                                    <option label="showClientCompany()" value="showClientCompany">showClientCompany()</option>
                                    <option label="createClientCompanies()" value="createClientCompanies">createClientCompanies()</option>
                                    <option label="updateClientCompany()" value="updateClientCompany">updateClientCompany()</option>
                                </optgroup>

                                <optgroup label="Client Address Methods">
                                    <option label="listClientAddresses()" value="listClientAddresses">listClientAddresses()</option>
                                    <option label="showClientAddress()" value="showClientAddress">showClientAddress()</option>
                                    <option label="createClientAddresses()" value="createClientAddresses">createClientAddresses()</option>
                                    <option label="updateClientAddress()" value="updateClientAddress">updateClientAddress()</option>
                                </optgroup>

                                <optgroup label="Client Phone Number Methods">
                                    <option label="listClientPhoneNumbers()" value="listClientPhoneNumbers">listClientPhoneNumbers()</option>
                                    <option label="showClientPhoneNumber()" value="showClientPhoneNumber">showClientPhoneNumber()</option>
                                    <option label="createClientPhoneNumbers()" value="createClientPhoneNumbers">createClientPhoneNumbers()</option>
                                    <option label="updateClientPhoneNumber()" value="updateClientPhoneNumber">updateClientPhoneNumber()</option>
                                </optgroup>

                                <optgroup label="Client Email Address Methods">
                                    <option label="listClientEmailAddresses()" value="listClientEmailAddresses">listClientEmailAddresses()</option>
                                    <option label="showClientEmailAddress()" value="showClientEmailAddress">showClientEmailAddress()</option>
                                    <option label="createClientEmailAddresses()" value="createClientEmailAddresses">createClientEmailAddresses()</option>
                                    <option label="updateClientEmailAddress()" value="updateClientEmailAddress">updateClientEmailAddress()</option>
                                </optgroup>

                                <optgroup label="Client Credit Card Methods">
                                    <option label="listClientCreditCards()" value="listClientCreditCards">listClientCreditCards()</option>
                                    <?php
                                        // This endpoint does not (yet?) exist
                                        //<option label="showClientCreditCard()" value="showClientCreditCard">showClientCreditCard()</option>
                                    ?>
                                    <option label="createClientCreditCards()" value="createClientCreditCards">createClientCreditCards()</option>
                                    <?php
                                        // This endpoint does not (yet?) exist
                                        //<option label="updateClientCreditCard()" value="updateClientCreditCard">updateClientCreditCard()</option>
                                    ?>
                                </optgroup>
                            </optgroup>


                            <optgroup label="Catalog Resources">
                                <optgroup label="Categories Methods">
                                    <option label="listCategories()" value="listCategories">listCategories()</option>
                                    <option label="listCategoriesDeleted()" value="listCategoriesDeleted">listCategoriesDeleted()</option>
                                    <option label="showCategory()" value="showCategory">showCategory()</option>
                                </optgroup>

                                <optgroup label="Configurations Methods">
                                    <option label="listConfigurations()" value="listConfigurations">listConfigurations()</option>
                                    <option label="showConfiguration()" value="showConfiguration">showConfiguration()</option>
                                </optgroup>

                                <optgroup label="Events Methods">
                                    <option label="listEvents()" value="listEvents">listEvents()</option>
                                    <option label="listEventsDeleted()" value="listEventsDeleted">listEventsDeleted()</option>
                                    <option label="showEvent()" value="showEvent">showEvent()</option>
                                </optgroup>

                                <optgroup label="Performers Methods">
                                    <option label="listPerformers()" value="listPerformers">listPerformers()</option>
                                    <option label="listPerformersDeleted()" value="listPerformersDeleted">listPerformersDeleted()</option>
                                    <option label="showPerformer()" value="showPerformer">showPerformer()</option>
                                    <option label="searchPerformers()" value="searchPerformers">searchPerformers()</option>
                                </optgroup>

                                <optgroup label="Search Methods">
                                    <option label="search()" value="search">Performers & Venues()</option>
                                </optgroup>

                                <optgroup label="Venues Methods">
                                    <option label="listVenues()" value="listVenues">listVenues()</option>
                                    <option label="listVenuesDeleted()" value="listVenuesDeleted">listVenuesDeleted()</option>
                                    <option label="showVenue()" value="showVenue">showVenue()</option>
                                    <option label="searchVenues()" value="searchVenues">searchVenues()</option>
                                </optgroup>
                            </optgroup>

                            <optgroup label="Inventory Resources">
                                <optgroup label="Ticket Groups">
                                    <option label="listTicketGroups()" value="listTicketGroups">listTicketGroups()</option>
                                    <option label="showTicketGroup()" value="showTicketGroup">showTicketGroup()</option>
                                </optgroup>

                                <optgroup label="Orders Methods">
                                    <option label="listOrders()" value="listOrders">listOrders()</option>
                                    <option label="showOrder()" value="showOrder">showOrder()</option>
                                    <option label="createOrders() (EvoPay)" value="createOrdersEvoPay" disabled="disabled">createOrders() (EvoPay)</option>
                                    <option label="createOrders() (Client)" value="createOrdersClient">createOrders() (Client)</option>
                                    <option label="createFulfillmentOrders()" value="createFulfillmentOrders" disabled="disabled">createFulfillmentOrders()</option>
                                    <option label="updateOrder()" value="updateOrder">updateOrder()</option>
                                    <option label="acceptOrder()" value="acceptOrder">acceptOrder()</option>
                                    <option label="rejectOrder()" value="rejectOrder">rejectOrder()</option>
                                    <option label="completeOrder()" value="completeOrder">completeOrder()</option>
                                </optgroup>

                                <optgroup label="Quotes Methods">
                                    <option label="listQuotes()" value="listQuotes">listQuotes()</option>
                                    <option label="showQuote()" value="showQuote">showQuote()</option>
                                    <option label="searchQuotes()" value="searchQuotes">searchQuotes()</option>
                                </optgroup>

                                <optgroup label="Shipments Methods">
                                    <option label="listShipments()" value="listShipments">listShipments()</option>
                                    <option label="showShipment()" value="showShipment">showShipment()</option>
                                    <option label="createShipments()" value="createShipments">createShipments()</option>
                                    <option label="updateShipment()" value="updateShipment">updateShipment()</option>
                                    <option label="createAirbill()" value="createAirbill" disabled="disabled">createAirbill()</option>
                                </optgroup>
                            </optgroup>

                            <optgroup label="EvoPay Resources">
                                <optgroup label="Accounts Methods">
                                    <option label="listEvoPayAccounts()" value="listEvoPayAccounts">listEvoPayAccounts()</option>
                                    <option label="showEvoPayAccount()" value="showEvoPayAccount">showEvoPayAccount()</option>
                                </optgroup>

                                <optgroup label="Transactions Methods">
                                    <option label="listEvoPayTransactions()" value="listEvoPayTransactions">listEvoPayTransactions()</option>
                                    <option label="showEvoPayTransaction()" value="showEvoPayTransaction">showEvoPayTransaction()</option>
                                </optgroup>
                            </optgroup>

                        </select>
                    </div>
		        </fieldset>

		        <fieldset id="listParameters" class="options">
                    <legend>List Parameters</legend>

                        <div class="list">
                            <label for="page">page:</label>
                            <input name="page" id="page" type="number" value="1" min="1" step="1" />
                        </div>

                        <div class="list">
                            <label for="per_page">per_page:</label>
                            <input name="per_page" id="per_page" type="number" value="10" min="1" max="100" step="1" />
                        </div>
		        </fieldset>

		        <fieldset id="methodInput" class="options">
                    <legend>Method Parameters</legend>

                    <div class="listBrokerages listUsers listVenues listVenuesDeleted listPerformers listPerformersDeleted listEvents listEventsDeleted listConfigurations listCategories listCategoriesDeleted listQuotes listClients createClients updateClient listClientCompanies createClientCompanies updateClientCompany listClientAddresses createClientAddresses updateClientAddress createClientCreditCards updateClientCreditCard">
                        <label for="name">name:</label>
                        <input name="name" id="name" type="text" value="" />
                    </div>

                    <div class="listBrokerages">
                        <label for="abbreviation">abbreviation:</label>
                        <input name="abbreviation" id="abbreviation" type="text" value="" />
                    </div>

                    <div class="listBrokerages listUsers listQuotes">
                        <label for="email">email:</label>
                        <input name="email" id="email" type="email" value="" />
                    </div>

                    <div class="listClients">
                        <label for="created_at">created_at:</label>
                        <select name="created_at_operator" id="created_at_operator">
                            <option value="eq">=</option>
                            <option value="not_eq">≠</option>
                            <option value="gt">&#62;</option>
                            <option value="gte" selected="selected">≥</option>
                            <option value="lt">&#60;</option>
                            <option value="lte">≤</option>
                        </select>
                        <input name="created_at" id="created_at" class="date-time" type="text" value="" />
                    </div>

                    <div class="listBrokerages listOffices listUsers listVenues listVenuesDeleted listPerformers listPerformersDeleted listEvents listEventsDeleted listConfigurations listCategories listCategoriesDeleted listTicketGroups listShipments listQuotes listOrders listClients listClientCompanies listClientPhoneNumbers listClientAddresses listClientEmailAddresses listClientCreditCards listEvoPayAccounts listEvoPayTransactions">
                        <label for="updated_at">updated_at:</label>
                        <select name="updated_at_operator" id="updated_at_operator">
                            <option value="eq">=</option>
                            <option value="not_eq">≠</option>
                            <option value="gt">&#62;</option>
                            <option value="gte" selected="selected">≥</option>
                            <option value="lt">&#60;</option>
                            <option value="lte">≤</option>
                        </select>
                        <input name="updated_at" id="updated_at" class="date-time" type="text" value="" />
                    </div>

                    <div class="listEvents listEventsDeleted">
                        <label for="occurs_at">occurs_at:</label>
                        <select name="occurs_at_operator" id="occurs_at_operator">
                            <option value="eq">=</option>
                            <option value="not_eq">≠</option>
                            <option value="gt">&#62;</option>
                            <option value="gte" selected="selected">≥</option>
                            <option value="lt">&#60;</option>
                            <option value="lte">≤</option>
                        </select>
                        <input name="occurs_at" id="occurs_at" class="date-time" type="text" value="" />
                    </div>

                    <div class="listVenuesDeleted listPerformersDeleted listEventsDeleted listCategoriesDeleted">
                        <label for="deleted_at">deleted_at:</label>
                        <select name="deleted_at_operator" id="deleted_at_operator">
                            <option value="gt">&#62;</option>
                            <option value="gte" selected="selected">≥</option>
                            <option value="lt">&#60;</option>
                            <option value="lte">≤</option>
                        </select>
                        <input name="deleted_at" id="deleted_at" class="date-time" type="text" value="" />
                    </div>

                    <div class="searchBrokerages searchOffices searchUsers searchVenues searchPerformers searchQuotes search">
                        <label for="q">Search Term (q):</label>
                        <input name="q" id="q" type="text" value="Front Row" />
                    </div>

                    <div class="showBrokerage listOffices listUsers listClients">
                        <label for="brokerage_id">brokerage_id:</label>
                        <input name="brokerage_id" id="brokerage_id" type="text" value="32" />
                    </div>

                    <div class="listOffices">
                        <label for="isMain">Is Main? (main):</label>
                        <input name="main" id="isMain" type="checkbox" value="1" />
                    </div>

                    <div class="listPerformers">
                        <label for="only_with_upcoming_events">only_with_upcoming_events:</label>
                        <input name="only_with_upcoming_events" id="only_with_upcoming_events" type="checkbox" value="1" />
                    </div>

                    <div class="showOffice listUsers listEvents listTicketGroups listClients createClients updateClient">
                        <label for="office_id">office_id:</label>
                        <input name="office_id" id="office_id" type="text" value="223" />
                    </div>

                    <div class="showUser listQuotes">
                        <label for="user_id">user_id:</label>
                        <input name="user_id" id="user_id" type="text" value="50" />
                    </div>

                    <div class="showVenue listPerformers listPerformersDeleted listEvents listEventsDeleted listConfigurations">
                        <label for="venue_id">venue_id:</label>
                        <input name="venue_id" id="venue_id" type="text" value="7648" />
                    </div>

                    <div class="showPerformer listEvents listEventsDeleted">
                        <label for="performer_id">performer_id:</label>
                        <input name="performer_id" id="performer_id" type="text" value="10638" />
                    </div>

                    <div class="listEvents">
                        <label for="primary_performer">primary_performer:</label>
                        <input name="primary_performer" id="primary_performer" type="checkbox" value="true" />
                    </div>

                    <div class="listEvents">
                        <label for="non_primary_id">non_primary_id:</label>
                        <input name="non_primary_id" id="non_primary_id" type="text" value="" />
                    </div>

                    <div class="listEvents">
                        <label for="by_time">by_time:</label>
                        <select name="by_time" id="by_time">
                            <option value="">Select One…</option>
                            <option value="day">day</option>
                            <option value="night">night</option>
                        </select>
                    </div>

                    <div class="search">
                        <label for="types">types:</label>
                        <select name="types" id="types">
                            <option value="performers">performers</option>
                            <option value="venues">venues</option>
                            <option value="offices">offices</option>
                            <option value="clients">clients</option>
                        </select>
                    </div>

                    <div class="listEvents listPerformers listVenues">
                        <label for="order_by">order_by:</label>
                        <input name="order_by" id="order_by" type="text" value="" />
                    </div>

                    <div class="listEvents listVenues">
                        <label for="ip">ip:</label>
                        <input name="ip" id="ip" type="text" value="" />
                    </div>

                    <div class="listEvents listVenues">
                        <label for="lat">lat:</label>
                        <input name="lat" id="lat" type="text" value="" />
                    </div>

                    <div class="listEvents listVenues">
                        <label for="lon">lon:</label>
                        <input name="lon" id="lon" type="text" value="" />
                    </div>

                    <div class="listEvents listVenues">
                        <label for="city_state">city_state:</label>
                        <input name="city_state" id="city_state" type="text" value="" />
                    </div>

                    <div class="listEvents listVenues">
                        <label for="radius">radius:</label>
                        <input name="radius" id="radius" type="text" value="" />
                    </div>

                    <div class="listEvents listVenues">
                        <label for="postal_code">postal_code:</label>
                        <input name="postal_code" id="postal_code" type="text" value="" />
                    </div>

                    <div class="showCategory listPerformers listEvents listEventsDeleted">
                        <label for="category_id">category_id:</label>
                        <input name="category_id" id="category_id" type="text" value="20" />
                    </div>

                    <div class="listPerformers listVenues">
                        <label for="first_letter">first_letter:</label>
                        <input name="first_letter" id="first_letter" type="text" value="" />
                    </div>

                    <div class="showConfiguration listEvents listEventsDeleted">
                        <label for="configuration_id">configuration_id:</label>
                        <input name="configuration_id" id="configuration_id" type="text" value="15029" />
                    </div>

                    <div class="showEvent listTicketGroups listQuotes">
                        <label for="event_id">event_id:</label>
                        <input name="event_id" id="event_id" type="text" value="301599" />
                    </div>

                    <div class="listCategories listCategoriesDeleted">
                        <label for="parent_id">parent_id:</label>
                        <input name="parent_id" id="parent_id" type="text" value="19" />
                    </div>

                    <div class="showTicketGroup">
                        <label for="ticket_group_id">ticket_group_id:</label>
                        <input name="ticket_group_id" id="ticket_group_id" type="text" value="15894788" />
                    </div>

                    <div class="showQuote">
                        <label for="quote_id">quote_id:</label>
                        <input name="quote_id" id="quote_id" type="text" value="" />
                    </div>

                    <div class="listConfigurations">
                        <label for="capacity">capacity:</label>
                        <input name="capacity" id="capacity" type="text" value="" />
                    </div>

                    <div class="listTicketGroups">
                        <label for="section">section:</label>
                        <input name="section" id="section" type="text" value="" />
                    </div>

                    <div class="listTicketGroups">
                        <label for="row">row:</label>
                        <input name="row" id="row" type="text" value="" />
                    </div>

                    <div class="listTicketGroups">
                        <label for="eticket">eticket:</label>
                        <input name="eticket" id="eticket" type="checkbox" value="1" />
                    </div>

                    <div class="listTicketGroups">
                        <label for="quantity">quantity:</label>
                        <input name="quantity" id="quantity" type="number" value="4" min="1" />
                    </div>

                    <div class="listTicketGroups">
                        <label for="ticketgroup_type">(ticketgroup) type:</label>
                        <select name="type" id="ticketgroup_type">
                            <option value="">All</option>
                            <option value="event" selected="selected">event</option>
                            <option value="parking">parking</option>
                        </select>
                    </div>

                    <div class="listTicketGroups">
                        <label for="price">price (maximum):</label>
                        <input name="price" id="price" type="text" value="" />
                    </div>

                    <div class="listShipments createShipments updateShipment">
                        <label for="tracking_number">tracking_number:</label>
                        <input name="tracking_number" id="tracking_number" type="text" value="" />
                    </div>

                    <div class="listShipments">
                        <label for="client_order_id">client_order_id:</label>
                        <input name="client_order_id" id="client_order_id" type="text" value="" />
                    </div>

                    <div class="listShipments">
                        <label for="partner_order_id">partner_order_id:</label>
                        <input name="partner_order_id" id="partner_order_id" type="text" value="" />
                    </div>

                    <div class="listShipments createShipments updateShipment">
                        <label for="shipment_type">shipment_type:</label>
                        <select name="shipment_type" id="shipment_type">
                            <option value="">Select One…</option>
                            <option value="FedEx">FedEx</option>
                            <option value="UPS">UPS</option>
                            <option value="Courier">Courier</option>
                            <option value="WillCall">Will Call</option>
                        </select>
                    </div>

                    <div class="listShipments">
                        <label for="shipment_state">(shipment) state:</label>
                        <select name="state" id="shipment_state">
                            <option value="">Select One…</option>
                            <option value="pending">pending</option>
                            <option value="in_transit">in_transit</option>
                            <option value="delivered">delivered</option>
                            <option value="returned">returned</option>
                            <option value="exception">exception</option>
                        </select>
                    </div>

                    <div class="listShipments createShipments showOrder acceptOrder rejectOrder updateOrder completeOrder listEvoPayTransactions">
                        <label for="order_id">order_id:</label>
                        <input name="order_id" id="order_id" type="text" value="" />
                    </div>

                    <div class="showShipment updateShipment">
                        <label for="shipment_id">shipment_id:</label>
                        <input name="shipment_id" id="shipment_id" type="text" value="" />
                    </div>

                    <div class="createShipments updateShipment">
                        <label for="airbill">airbill:</label>
                        <input name="airbill" id="airbill" type="file" />
                    </div>

                    <div class="createShipments">
                        <label for="items">items:</label>
                        <input name="items" id="items" type="text" value="" />
                    </div>

                    <div class="createShipments updateShipment">
                        <label for="service_type">service_type:
                        <br /><span class="hint">Service type for this shipment, as returned from a call to get rates.</span></label>
                        <select name="service_type" id="service_type">
                            <option value="FEDEX_GROUND_HOME">Ground Home Delivery</option>
                            <option value="FEDEX_GROUND">FedEx Ground</option>
                            <option value="FEDEX_FIRST_OVERN">First Overnight</option>
                            <option value="8">FedEx 2 Day Saturday Delivery</option>
                            <option value="10">International Ground</option>
                            <option value="FEDEX_INTNL_ECO">International Economy</option>
                            <option value="FEDEX_INTNL_PRI">International Priority</option>
                            <option value="FEDEX_INTNL_FIRST">International First</option>
                            <option value="14">International Priority Saturday Delivery</option>
                            <option value="FEDEX_EXPRESS_SAV">FedEx Express Saver</option>
                            <option value="PRIORITY_OVERNIGHT_SATURDAY_DELIVERY">Priority Overnight Saturday Delivery</option>
                            <option value="FEDEX_PRIORITY_OVERN">Priority Overnight</option>
                            <option value="FEDEX_2DAY">FedEx 2 Day</option>
                            <option value="FEDEX_STANDARD_OVERN">Standard Overnight</option>
                        </select>
                    </div>

                    <div class="createShipments">
                        <label for="phone_number_attributes">phone_number_attributes:
                        <br /><span class="hint">An array of information regarding the phone number.</span></label>
                        <input name="phone_number_attributes" id="phone_number_attributes" type="text" value="" disabled="disabled" />
                    </div>

                    <div class="createShipments">
                        <label for="address_attributes">address_attributes:
                        <br /><span class="hint">Use this if you are creating a new address.</span></label>
                        <input name="address_attributes" id="address_attributes" type="text" value="" disabled="disabled" />
                    </div>

                    <div class="createShipments">
                        <label for="ship_to_name">ship_to_name:</label>
                        <input name="ship_to_name" id="ship_to_name" type="text" value="" />
                    </div>

                    <div class="createShipments">
                        <label for="ship_to_company_name">ship_to_company_name:</label>
                        <input name="ship_to_company_name" id="ship_to_company_name" type="text" value="" />
                    </div>

                    <div class="createShipments">
                        <label for="signature_type">signature_type:</label>
                        <select name="state" id="signature_type">
                            <option value="SERVICE_DEFAULT">SERVICE_DEFAULT</option>
                            <option value="NO_SIGNATURE_REQUIRED">NO_SIGNATURE_REQUIRED</option>
                            <option value="ADULT">ADULT</option>
                            <option value="DIRECT">DIRECT</option>
                            <option value="INDIRECT">INDIRECT</option>
                        </select>
                    </div>

                    <div class="createShipments">
                        <label for="service_type">service_type:</label>
                        <select name="state" id="service_type">
                            <option value="1">Ground Home Delivery</option>
                            <option value="2">FedEx Ground</option>
                            <option value="7">First Overnight</option>
                            <option value="8">FedEx 2 Day Saturday Delivery</option>
                            <option value="10">International Ground</option>
                            <option value="11">International Economy</option>
                            <option value="12">International Priority</option>
                            <option value="13">International First</option>
                            <option value="14">International Priority Saturday Delivery</option>
                            <option value="3">FedEx Express Saver</option>
                            <option value="9">Priority Overnight Saturday Delivery</option>
                            <option value="6">Priority Overnight</option>
                            <option value="4">FedEx 2 Day</option>
                            <option value="5">Standard Overnight</option>
                        </select>
                    </div>

                    <div class="listOrders">
                        <label for="buyer_id">buyer_id:
                        <br /><span class="hint">Office ID of the buyer.</span></label>
                        <input name="buyer_id" id="buyer_id" type="text" value="" />
                    </div>

                    <div class="listOrders createOrdersClient">
                        <label for="seller_id">seller_id:
                        <br /><span class="hint">Office ID of the seller.</span></label>
                        <input name="seller_id" id="seller_id" type="text" value="" />
                    </div>

                    <div class="listOrders">
                        <label for="order_state">(order) state:</label>
                        <select name="state" id="order_state">
                            <option value="">Select One…</option>
                            <option value="pending">pending</option>
                            <option value="accepted">accepted</option>
                            <option value="rejected">rejected</option>
                            <option value="cancelled">cancelled</option>
                            <option value="expired">expired</option>
                            <option value="pending_substitution">pending_substitution</option>
                        </select>
                    </div>

                    <div class="listOrders createOrdersClient updateOrder">
                        <label for="po_number">po_number:</label>
                        <input name="po_number" id="po_number" type="text" value="" />
                    </div>

                    <div class="listOrders createOrdersClient updateOrder">
                        <label for="invoice_number">invoice_number:</label>
                        <input name="invoice_number" id="invoice_number" type="text" value="" />
                    </div>

                    <div class="createOrdersClient">
                        <fieldset>
                            <legend>Item 1</legend>

                            <div class="createOrdersClient">
                                <label for="item_1_ticket_group_id">ticket_group_id:</label>
                                <input name="items[0][ticket_group_id]" id="item_1_ticket_group_id" type="text" value="15894788" />
                            </div>

                            <div class="createOrdersClient">
                                <label for="item_1_quantity">quantity:</label>
                                <input name="items[0][quantity]" id="item_1_quantity" type="text" value="2" />
                            </div>

                            <div class="createOrdersClient">
                                <label for="item_1_price">price:</label>
                                <input name="items[0][price]" id="item_1_price" type="text" value="1295.00" />
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend>Item 2 (optional)</legend>

                            <div class="createOrdersClient">
                                <label for="item_2_ticket_group_id">ticket_group_id:</label>
                                <input name="items[1][ticket_group_id]" id="item_2_ticket_group_id" type="text" value="15894807" />
                            </div>

                            <div class="createOrdersClient">
                                <label for="item_2_quantity">quantity:</label>
                                <input name="items[1][quantity]" id="item_2_quantity" type="text" value="2" />
                            </div>

                            <div class="createOrdersClient">
                                <label for="item_2_price">price:</label>
                                <input name="items[1][price]" id="item_2_price" type="text" value="1272.99" />
                            </div>
                        </fieldset>

                    </div>

                    <div class="createOrdersClient">
                        <label for="payments">payments:</label>
                        <select name="payments" id="payments">
                            <option value="offline">offline</option>
                            <option value="credit_card">credit_card</option>
                        </select>
                    </div>

                    <div class="createOrdersClient updateOrder">
                        <label for="shipping_address_id">shipping_address_id:</label>
                        <input name="shipping_address_id" id="shipping_address_id" type="text" value="" />
                    </div>

                    <div class="createOrdersClient updateOrder" id="client_order_shipping_address">
                        <fieldset>
                            <legend>Shipping Address</legend>

                            <div class="createOrdersClient updateOrder">
                                <label for="shipping_address_label">label:</label>
                                <input name="shipping_address[label]" id="shipping_address_label" type="text" value="Work" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="shipping_address_name">name:</label>
                                <input name="shipping_address[name]" id="shipping_address_name" type="text" value="Moe Szyslak" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="shipping_address_company">company:</label>
                                <input name="shipping_address[company]" id="shipping_address_company" type="text" value="Moe’s Tavern" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="shipping_address_street_address">street_address:</label>
                                <input name="shipping_address[street_address]" id="shipping_address_street_address" type="text" value="555 Evergreen Terrace" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="shipping_address_extended_address">extended_address:</label>
                                <input name="shipping_address[extended_address]" id="shipping_address_extended_address" type="text" value="Suite 666" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="shipping_address_locality">locality:</label>
                                <input name="shipping_address[locality]" id="shipping_address_locality" type="text" value="Springfield" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="shipping_address_region">region:</label>
                                <input name="shipping_address[region]" id="shipping_address_region" type="text" value="MG" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="shipping_address_postal_code">postal_code:</label>
                                <input name="shipping_address[postal_code]" id="shipping_address_postal_code" type="text" value="58008-0000" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="shipping_address_country_code">country_code:</label>
                                <input name="shipping_address[country_code]" id="shipping_address_country_code" type="text" value="US" />
                            </div>

                        </fieldset>

                    </div>

                    <div class="createOrdersClient updateOrder">
                        <label for="billing_address_id">billing_address_id:</label>
                        <input name="billing_address_id" id="billing_address_id" type="text" value="" />
                    </div>

                    <div class="createOrdersClient updateOrder" id="client_order_billing_address">
                        <fieldset>
                            <legend>billing Address</legend>

                            <div class="createOrdersClient updateOrder">
                                <label for="billing_address_label">label:</label>
                                <input name="billing_address[label]" id="billing_address_label" type="text" value="Home" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="billing_address_name">name:</label>
                                <input name="billing_address[name]" id="billing_address_name" type="text" value="Ned Flanders" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="billing_address_company">company:</label>
                                <input name="billing_address[company]" id="billing_address_company" type="text" value="" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="billing_address_street_address">street_address:</label>
                                <input name="billing_address[street_address]" id="billing_address_street_address" type="text" value="744 Evergreen Terrace" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="billing_address_extended_address">extended_address:</label>
                                <input name="billing_address[extended_address]" id="billing_address_extended_address" type="text" value="" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="billing_address_locality">locality:</label>
                                <input name="billing_address[locality]" id="billing_address_locality" type="text" value="Springfield" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="billing_address_region">region:</label>
                                <input name="billing_address[region]" id="billing_address_region" type="text" value="MG" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="billing_address_postal_code">postal_code:</label>
                                <input name="billing_address[postal_code]" id="billing_address_postal_code" type="text" value="58008-0000" />
                            </div>

                            <div class="createOrdersClient updateOrder">
                                <label for="billing_address_country_code">country_code:</label>
                                <input name="billing_address[country_code]" id="billing_address_country_code" type="text" value="US" />
                            </div>

                        </fieldset>

                    </div>

                    <div class="createOrdersClient">
                        <label for="shipping">shipping:</label>
                        <input name="shipping" id="shipping" type="text" value="12.95" />
                    </div>

                    <div class="createOrdersClient">
                        <label for="service_fee">service_fee:</label>
                        <input name="service_fee" id="service_fee" type="text" value="22.50" />
                    </div>

                    <div class="createOrdersClient">
                        <label for="tax">tax:</label>
                        <input name="tax" id="tax" type="text" value="0.00" />
                    </div>

                    <div class="createOrdersClient">
                        <label for="additional_expense">additional_expense:</label>
                        <input name="additional_expense" id="additional_expense" type="text" value="0.00" />
                    </div>

                    <div class="createOrdersClient updateOrder">
                        <label for="instructions">instructions:</label>
                        <textarea name="instructions" id="instructions"></textarea>
                    </div>

                    <div class="acceptOrder rejectOrder">
                        <label for="reviewer_id">reviewer_id:
                        <br /><span class="hint">The user ID of the reviewer who belongs to the brokerage who received the order.</span></label>
                        <input name="reviewer_id" id="reviewer_id" type="text" value="" />
                    </div>

                    <div class="rejectOrder">
                        <label for="rejection_reason">rejection_reason:</label>
                        <select name="rejection_reason" id="rejection_reason">
                            <option label="Tickets No Longer Available" value="Tickets No Longer Available">Tickets No Longer Available</option>
                            <option label="Tickets Priced Incorrectly" value="Tickets Priced Incorrectly">Tickets Priced Incorrectly</option>
                            <option label="Duplicate Order" value="Duplicate Order">Duplicate Order</option>
                            <option label="Fraudulent Order" value="Fraudulent Order">Fraudulent Order</option>
                            <option label="This Reason Is Invalid" value="This Reason Is Invalid">This Reason Is Invalid</option>
                        </select>
                    </div>

                    <div class="showClient updateClient listClientPhoneNumbers showClientPhoneNumber createClientPhoneNumbers updateClientPhoneNumber listClientEmailAddresses showClientEmailAddress createClientEmailAddresses updateClientEmailAddress listClientAddresses showClientAddress createClientAddresses updateClientAddress createClientCreditCards listClientCreditCards showClientCreditCard updateClientCreditCard createOrdersClient">
                        <label for="client_id">client_id:</label>
                        <input name="client_id" id="client_id" type="text" value="" />
                    </div>

                    <div class="showClientCompany updateClientCompany listClients">
                        <label for="company_id">company_id:</label>
                        <input name="company_id" id="company_id" type="text" value="" />
                    </div>

                    <div class="showClientPhoneNumber updateClientPhoneNumber createClientCreditCards updateClientCreditCard">
                        <label for="phone_number_id">phone_number_id:</label>
                        <input name="phone_number_id" id="phone_number_id" type="text" value="" />
                    </div>

                    <div class="listClientPhoneNumbers createClientPhoneNumbers updateClientPhoneNumber listClientAddresses createClientAddresses updateClientAddress">
                        <label for="country_code">country_code:</label>
                        <input name="country_code" id="country_code" type="text" value="" />
                    </div>

                    <div class="listClientPhoneNumbers createClientPhoneNumbers updateClientPhoneNumber createClientCreditCards updateClientCreditCard">
                        <label for="number">number:</label>
                        <input name="number" id="number" type="text" value="" />
                    </div>

                    <div class="listClientPhoneNumbers createClientPhoneNumbers updateClientPhoneNumber">
                        <label for="extension">extension:</label>
                        <input name="extension" id="extension" type="text" value="" />
                    </div>

                    <div class="listClientPhoneNumbers createClientPhoneNumbers updateClientPhoneNumber listClientEmailAddresses createClientEmailAddresses updateClientEmailAddress listClientAddresses createClientAddresses updateClientAddress">
                        <label for="label">label:</label>
                        <input name="label" id="label" type="text" value="" />
                    </div>

                    <div class="listClientEmailAddresses createClientEmailAddresses updateClientEmailAddress">
                        <label for="address">(email) address:</label>
                        <input name="address" id="address" type="email" value="" />
                    </div>

                    <div class="showClientAddress updateClientAddress createClientCreditCards updateClientCreditCard createShipments">
                        <label for="address_id">address_id:</label>
                        <input name="address_id" id="address_id" type="text" value="" />
                    </div>

                    <div class="showClientEmailAddress updateClientEmailAddress">
                        <label for="email_address_id">email_address_id:</label>
                        <input name="email_address_id" id="email_address_id" type="text" value="" />
                    </div>

                    <div class="updateClient">
                        <label for="primary_shipping_address_id">primary_shipping_address_id:</label>
                        <input name="primary_shipping_address_id" id="primary_shipping_address_id" type="text" value="" />
                    </div>

                    <div class="listClientAddresses createClientAddresses updateClientAddress">
                        <label for="company">company:</label>
                        <input name="company" id="company" type="text" value="" />
                    </div>

                    <div class="listClientAddresses createClientAddresses updateClientAddress">
                        <label for="street_address">street_address:</label>
                        <input name="street_address" id="street_address" type="text" value="" />
                    </div>

                    <div class="listClientAddresses createClientAddresses updateClientAddress">
                        <label for="extended_address">extended_address:</label>
                        <input name="extended_address" id="extended_address" type="text" value="" />
                    </div>

                    <div class="listClientAddresses createClientAddresses updateClientAddress">
                        <label for="locality">locality:</label>
                        <input name="locality" id="locality" type="text" value="" />
                    </div>

                    <div class="listClientAddresses createClientAddresses updateClientAddress">
                        <label for="region">region:</label>
                        <input name="region" id="region" type="text" value="" />
                    </div>

                    <div class="listClientAddresses createClientAddresses updateClientAddress">
                        <label for="postal_code">postal_code:</label>
                        <input name="postal_code" id="postal_code" type="text" value="" />
                    </div>

                    <div class="listClientAddresses createClientAddresses updateClientAddress">
                        <label for="isPrimary">Is primary? (primary):</label>
                        <input name="primary" id="isPrimary" type="checkbox" value="1" />
                    </div>

                    <div class="listEvents">
                        <label for="unique_performers">unique_performers:</label>
                        <input name="unique_performers" id="unique_performers" type="checkbox" value="1" />
                    </div>

                    <div class="showClientCreditCard updateClientCreditCard">
                        <label for="credit_card_id">credit_card_id:</label>
                        <input name="credit_card_id" id="credit_card_id" type="text" value="" />
                    </div>

                    <div class="updateClient">
                        <label for="primary_credit_card_id">primary_credit_card_id:</label>
                        <input name="primary_credit_card_id" id="primary_credit_card_id" type="text" value="" />
                    </div>

                    <div class="createClientCreditCards updateClientCreditCard">
                        <label for="expiration_month">expiration_month:</label>
                        <input name="expiration_month" id="expiration_month" type="text" value="" pattern="\d{2}" />
                    </div>

                    <div class="createClientCreditCards updateClientCreditCard">
                        <label for="expiration_year">expiration_year:</label>
                        <input name="expiration_year" id="expiration_year" type="text" value="" pattern="\d{4}" />
                    </div>

                    <div class="createClientCreditCards updateClientCreditCard">
                        <label for="ip_address">ip_address:</label>
                        <input name="ip_address" id="ip_address" type="text" value="" />
                    </div>

                    <div class="createClientCreditCards updateClientCreditCard">
                        <label for="verification_code">verification_code:</label>
                        <input name="verification_code" id="verification_code" type="text" value="" pattern="\d{3,4}" />
                    </div>

                    <div class="listEvoPayAccounts">
                        <label for="balance">balance:
                        <br /><span class="hint">Account balance without dollar sign. e.g. “100.00”.</span></label>
                        <input name="balance" id="balance" type="text" value="" placeholder="1080.00" pattern="\d+\.\d{2}" />
                    </div>

                    <div class="listEvoPayAccounts">
                        <label for="currency">currency:</label>
                        <input name="currency" id="currency" type="text" value="" placeholder="USD" />
                    </div>

                    <div class="showEvoPayAccount listEvoPayTransactions showEvoPayTransaction">
                        <label for="account_id">(EvoPay) account_id:</label>
                        <input name="account_id" id="account_id" type="text" value="" />
                    </div>

                    <div class="listEvoPayTransactions">
                        <label for="amount">amount:
                        <br /><span class="hint">Amount of transaction without dollar sign. e.g. “100.00”.</span></label>
                        <input name="amount" id="amount" type="text" value="" placeholder="1080.00" pattern="\d+\.\d{2}" />
                    </div>

                    <div class="listEvoPayTransactions">
                        <label for="evopay_transaction_type">(EvoPay Transaction) type:</label>
                        <select name="type" id="evopay_transaction_type">
                            <option value="">Select One…</option>
                            <option value="Credit">Credit</option>
                            <option value="etc">etc</option>
                        </select>
                    </div>

                    <div class="showEvoPayTransaction">
                        <label for="transaction_id">(EvoPay) transaction_id:</label>
                        <input name="transaction_id" id="transaction_id" type="text" value="" />
                    </div>

                    <div class="listSettingsShipping">
                        <label for="show_on_pos">show_on_pos:</label>
                        <input name="show_on_pos" id="show_on_pos" type="checkbox" value="1" />
                    </div>

                    <div class="listSettingsShipping">
                        <label for="show_on_site">show_on_site:</label>
                        <input name="show_on_site" id="show_on_site" type="checkbox" value="1" />
                    </div>



                    <div class="listEvents">
                        <label for="order_by">order_by:</label>
                        <input name="order_by" id="order_by" type="text" value="" />
                    </div>

                    <div class="createClients updateClient" id="wrapper">
                        <label for="tags">tags:</label>
                        <input name="tags" id="tags" type="text" value="" />
                    </div>



		        </fieldset>

                <input id="submit" type="submit" value="Submit" class="button" />
                <p id="productionWarning" style="background-color:#ffa; border: 1px solid black; padding: .5em; margin: .5em;">It is NOT recommended to use any of the create*() methods when using the Production environment as you will be affecting REAL data.</p>
		    </form>
		</div>

		<footer>

		</footer>
	</div>

</body>
</html>

<?php

/**
 * Utility function for putting the submitted options into an array
 *
 * @param Zend_Filter_Input $input
 * @return array
 */
function _getOptions($input)
{
    $options = array();

    $dateFields = array(
        'updated_at',
        'occurs_at',
        'deleted_at',
        'created_at',
    );

    /**
     * We get the unknown $input variables because we
     * don't want to have to specify each one in the
     * $validators for this demo.
     */
    $unknown = $input->getUnknown();
    foreach ($unknown as $key => $value) {
        if ($value !== '' && stristr($key, '_operator') === false) {
//        if (stristr($key, '_operator') === false) {
            if (in_array($key, $dateFields)) {
                $operatorKey = $key . '_operator';
                $options[$key . '.' . $unknown[$operatorKey]] = $value;
            } else {
                $options[$key] = $value;
            }
        }
    }

    return $options;
}


/**
 * Utility function for outputting PHP code for demo purposes
 *
 * @param string $apiMethod
 * @param int $showId
 */
function _outputShowCode($apiMethod, $showId)
{
    echo '$results = $tevo->' . $apiMethod . '(' . $showId . ');' . PHP_EOL;
}


/**
 * Utility function for outputting PHP code for demo purposes
 *
 * @param string $apiMethod
 * @param int $itemId
 * @param int $showId
 */
function _outputShowByIdCode($apiMethod, $itemId, $showId)
{
    echo '$results = $tevo->' . $apiMethod . '(' . $itemId . ', ' . $showId . ');' . PHP_EOL;
}


/**
 * Utility function for outputting PHP code for demo purposes
 *
 * @param string $apiMethod
 * @param array $options
 */
function _outputOptionsCode($options)
{
    echo '$options = array(' . PHP_EOL;
    foreach( $options as $key => $val) {
        if (!is_array($val) && !is_object($val) && is_numeric($val)) {
            echo '    \'' . $key . '\' => ' . $val . ',' . PHP_EOL;
        } elseif (!is_array($val) && !is_object($val) && !is_numeric($val)) {
            echo '    \'' . $key . '\' => \'' . $val . '\',' . PHP_EOL;
        }
    }
    echo ');' . PHP_EOL . PHP_EOL
    ;
}


/**
 * Utility function for outputting PHP code for demo purposes
 *
 * @param string $apiMethod
 * @param array $options
 */
function _outputListCode($apiMethod, $options)
{
    _outputOptionsCode($options);

    echo '$results = $tevo->' . $apiMethod . '($options);' . PHP_EOL;
}


/**
 * Utility function for outputting PHP code for demo purposes
 *
 * @param string $apiMethod
 * @param int $listId
 * @param array $options
 */
function _outputListByIdCode($apiMethod, $listId, $options)
{
    echo '$options = array(' . PHP_EOL;
    foreach( $options as $key => $val) {
        echo '    \'' . $key . '\' => ' . $val . ',' . PHP_EOL;
    }
    echo ');' . PHP_EOL
       . PHP_EOL
       . '$results = $tevo->' . $apiMethod . '(' . $listId . ', $options);' . PHP_EOL
    ;
}


/**
 * Utility function for outputting PHP code for demo purposes
 *
 * @param string $apiMethod
 * @param string $queryString
 * @param array $options
 */
function _outputSearchCode($apiMethod, $queryString, $options)
{
    _outputOptionsCode($options);

    echo '$results = $tevo->' . $apiMethod . '(\'' . $queryString . '\', $options);' . PHP_EOL;
}


/**
 * Utility function for performing show*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param int $showId
 * @return stdClass
 */
function _doShow(TicketEvolution_Webservice $tevo, $apiMethod, $showId)
{
    // Execute the call
    try {
        $results = $tevo->$apiMethod($showId);
    } catch (Exception $e) {
        echo '</pre>' . PHP_EOL
           . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
           . _getRequest($tevo, $apiMethod, true)
           . _getResponse($tevo, $apiMethod, true);
        exit (1);
    }

    return $results;
}


/**
 * Utility function for performing show*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param int $itemId
 * @param int $showId
 * @return stdClass
 */
function _doShowById(TicketEvolution_Webservice $tevo, $apiMethod, $itemId, $showId)
{
    // Execute the call
    try {
        $results = $tevo->$apiMethod($itemId, $showId);
    } catch (Exception $e) {
        echo '</pre>' . PHP_EOL
           . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
           . _getRequest($tevo, $apiMethod, true)
           . _getResponse($tevo, $apiMethod, true);
        exit (1);
    }

    return $results;
}


/**
 * Utility function for performing list*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param array $options
 * @return stdClass
 */
function _doList(TicketEvolution_Webservice $tevo, $apiMethod, array $options)
{
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

    return $results;
}


/**
 * Utility function for performing list*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param int $listId
 * @param array $options
 * @return stdClass
 */
function _doListById(TicketEvolution_Webservice $tevo, $apiMethod, $listId, array $options)
{
    // Execute the call
    try {
        $results = $tevo->$apiMethod($listId, $options);
    } catch (Exception $e) {
        echo '</pre>' . PHP_EOL
           . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
           . _getRequest($tevo, $apiMethod, true)
           . _getResponse($tevo, $apiMethod, true);
        exit (1);
    }

    return $results;
}


/**
 * Utility function for performing search*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param string $queryString
 * @param array $options
 * @return stdClass
 */
function _doSearch(TicketEvolution_Webservice $tevo, $apiMethod, $queryString, $options)
{
    // Execute the call
    try {
        $results = $tevo->$apiMethod($queryString, $options);
    } catch (Exception $e) {
        echo '</pre>' . PHP_EOL
           . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
           . _getRequest($tevo, $apiMethod, true)
           . _getResponse($tevo, $apiMethod, true);
        exit (1);
    }

    return $results;
}


/**
 * Utility function for performing create*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param stdClass $properties
 * @return stdClass
 */
function _doCreate(TicketEvolution_Webservice $tevo, $apiMethod, $properties)
{
    // Execute the call
    try {
        $results = $tevo->$apiMethod($properties);
    } catch (Exception $e) {
        echo '</pre>' . PHP_EOL
           . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
           . _getRequest($tevo, $apiMethod, true)
           . _getResponse($tevo, $apiMethod, true);
        exit (1);
    }

    return $results;
}


/**
 * Utility function for performing create*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param int $itemId
 * @param array $properties
 * @return stdClass
 */
function _doCreateById(TicketEvolution_Webservice $tevo, $apiMethod, $itemId, array $properties)
{
    // Execute the call
    try {
        $results = $tevo->$apiMethod($itemId, $properties);
    } catch (Exception $e) {
        echo '</pre>' . PHP_EOL
           . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
           . _getRequest($tevo, $apiMethod, true)
           . _getResponse($tevo, $apiMethod, true);
        exit (1);
    }

    return $results;
}


/**
 * Utility function for performing update*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param int $updateId
 * @param stdClass $properties
 * @return stdClass
 */
function _doUpdate(TicketEvolution_Webservice $tevo, $apiMethod, $updateId, $properties)
{
    // Execute the call
    try {
        $results = $tevo->$apiMethod($updateId, $properties);
    } catch (Exception $e) {
        echo '</pre>' . PHP_EOL
           . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
           . _getRequest($tevo, $apiMethod, true)
           . _getResponse($tevo, $apiMethod, true);
        exit (1);
    }

    return $results;
}


/**
 * Utility function for performing update*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param int $itemId
 * @param int $updateId
 * @param array $properties
 * @return stdClass
 */
function _doUpdateById(TicketEvolution_Webservice $tevo, $apiMethod, $itemId, $updateId, $properties)
{
    // Execute the call
    try {
        $results = $tevo->$apiMethod($itemId, $updateId, $properties);
    } catch (Exception $e) {
        echo '</pre>' . PHP_EOL
           . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
           . _getRequest($tevo, $apiMethod, true)
           . _getResponse($tevo, $apiMethod, true);
        exit (1);
    }

    return $results;
}


/**
 * Utility function for performing update*() calls
 *
 * @param TicketEvolution_Webservice $tevo
 * @param string $apiMethod
 * @param int $itemId
 * @param int $updateId
 * @param array $properties
 * @return stdClass
 */
function _doOther(TicketEvolution_Webservice $tevo, $apiMethod, $param1, $param2=null, $param3=null)
{
    // Execute the call
    try {
        if (!is_null($param3)) {
            $results = $tevo->$apiMethod($param1, $param2, $param3);
        } elseif (!is_null($param2)) {
            $results = $tevo->$apiMethod($param1, $param2);
        } else {
            $results = $tevo->$apiMethod($param1);
        }
    } catch (Exception $e) {
        echo '</pre>' . PHP_EOL
           . '<h1>Exception thrown trying to perform API request</h1>' . PHP_EOL
           . _getRequest($tevo, $apiMethod, true)
           . _getResponse($tevo, $apiMethod, true);
        exit (1);
    }

    return $results;
}


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
