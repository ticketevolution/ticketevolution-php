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
 * @package     TicketEvolution_Webservice
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


/**
 * @category    TicketEvolution
 * @package     TicketEvolution_Webservice
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_Webservice
{
    /**
     * For methods that return data these constants define what class results
     * of each type are passed to.
     */
    const BROKER_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Brokerages';
    const BROKER_CLASS = 'TicketEvolution_Brokerage';

    const CLIENT_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Clients';
    const CLIENT_CLASS = 'TicketEvolution_Client';

    const ADDRESS_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Addresses';
    const ADDRESS_CLASS = 'TicketEvolution_Address_Client';

    const PHONENUMBER_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_PhoneNumbers';
    const PHONENUMBER_CLASS = 'TicketEvolution_PhoneNumber_Client';

    const EMAILADDRESS_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_EmailAddresses';
    const EMAILADDRESS_CLASS = 'TicketEvolution_EmailAddress_Client';

    const CREDITCARD_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_CreditCards';
    const CREDITCARD_CLASS = 'TicketEvolution_CreditCard_Client';

    const OFFICE_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Offices';
    const OFFICE_CLASS = 'TicketEvolution_Office';

    const USER_RESULTSET_CLASS   = 'TicketEvolution_Webservice_ResultSet_Users';
    const USER_CLASS   = 'TicketEvolution_User';

    const CATEGORY_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Categories';
    const CATEGORY_CLASS = 'TicketEvolution_Category';

    const CONFIGURATION_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Configurations';
    const CONFIGURATION_CLASS = 'TicketEvolution_Configuration';

    const EVENT_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Events';
    const EVENT_CLASS = 'TicketEvolution_Event';

    const PERFORMER_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Performers';
    const PERFORMER_CLASS = 'TicketEvolution_Performer';

    const SEARCH_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_SearchResults';
    const SEARCH_CLASS = 'TicketEvolution_SearchRsult';

    const VENUE_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Venues';
    const VENUE_CLASS = 'TicketEvolution_Venue';

    const TICKETGROUP_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_TicketGroups';
    const TICKETGROUP_CLASS = 'TicketEvolution_TicketGroup';

    const ORDER_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Orders';
    const ORDER_CLASS = 'TicketEvolution_Order';

    const QUOTE_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Quotes';
    const QUOTE_CLASS = 'TicketEvolution_Quote';

    const SHIPMENT_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_Shipments';
    const SHIPMENT_CLASS = 'TicketEvolution_Shipment';

    const EVOPAYACCOUNT_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_EvoPayAccounts';
    const EVOPAYACCOUNT_CLASS = 'TicketEvolution_EvoPayAccount';

    const EVOPAYTRANSACTION_RESULTSET_CLASS = 'TicketEvolution_Webservice_ResultSet_EvoPayTransactions';
    const EVOPAYTRANSACTION_CLASS = 'TicketEvolution_EvoPayTransaction';


    /**
     * Ticket Evolution API Token
     *
     * @var string
     * @link http://exchange.ticketevolution.com/brokerage/credentials
     */
    public $apiToken;

    /**
     * Ticket Evolution API Secret Key
     *
     * @var string
     * @link http://exchange.ticketevolution.com/brokerage/credentials
     */
    protected $_secretKey = null;

    /**
     * Base URI for the REST client
     * You should override and use the sandbox (http://api.sandbox.ticketevolution.com)
     * for testing and development
     *
     * @var string
     */
    protected $_baseUri = 'https://api.ticketevolution.com';

    /**
     * API version
     *
     * @var string
     * @link http://api.ticketevolution.com/ Find the current version
     */
    protected $_apiVersion = '8';


    /**
     * Reference to REST client object
     *
     * @var Zend_Rest_Client
     */
    protected $_rest = null;


    /**
     * Whether or not to use persistent connections.
     *
     * @var bool
     */
    protected $_usePersistentConnections = true;


    /**
     * Constructs a new Ticket Evolution Web Services Client
     *
     * @param  mixed $config  An array or Zend_Config object with adapter parameters.
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice
     */
    public function __construct($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        /*
         * Verify that parameters are in an array.
         */
        if (!is_array($config)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'Parameters must be in an array or a Zend_Config object'
            );
        }

        /*
         * Verify that an API token has been specified.
         */
        if (!is_string($config['apiToken']) || empty($config['apiToken'])) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'API token must be specified in a string'
            );
        }

        /*
         * Verify that an API secret key has been specified.
         */
        if (!is_string($config['secretKey']) || empty($config['secretKey'])) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'Secret key must be specified in a string'
            );
        }

        /*
         * See if we need to override the API version.
         */
        if (!empty($config['apiVersion'])) {
            $this->_apiVersion = (string) $config['apiVersion'];
        }

        /*
         * See if we need to override the base URI.
         */
        if (!empty($config['baseUri'])) {
            $this->_baseUri = (string) $config['baseUri'];
        }

        /*
         * See if we need to override the _usePersistentConnections.
         */
        if (isset($config['usePersistentConnections'])) {
            $this->_usePersistentConnections = (bool) $config['usePersistentConnections'];
        }

        $this->apiToken = (string) $config['apiToken'];
        $this->_secretKey = (string) $config['secretKey'];
    }


    /**
     * List Brokerages
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Brokerages
     */
    public function listBrokers(array $options)
    {
        $endPoint = 'brokerages';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::BROKER_RESULTSET_CLASS);
    }


    /**
     * Get a single brokerage by Id
     *
     * @param  int $id The brokerage ID
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Brokerage
     */
    public function showBroker($id)
    {
        $endPoint = 'brokerages/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::BROKER_CLASS);
    }


    /**
     * Search for brokerage(s)
     *
     * @param  string $query The query string
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Brokerages
     */
    public function searchBrokers($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'You must provide a non-empty query string'
            );
        }

        $endPoint = 'brokerages/search';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options['q'] = (string) $query;
        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::BROKER_RESULTSET_CLASS);
    }


    /**
     * List Clients
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Clients
     */
    public function listClients(array $options)
    {
        $endPoint = 'clients';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CLIENT_RESULTSET_CLASS);
    }


    /**
     * Get a single client by Id
     *
     * @param  int $id The Client ID
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Client
     */
    public function showClient($id)
    {
        $endPoint = 'clients/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CLIENT_CLASS);
    }


    /**
     * Search for client(s)
     *
     * @param  string $query The query string
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Clients
     */
    public function searchClients($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'You must provide a non-empty query string'
            );
        }

        $endPoint = 'clients/search';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options['q'] = (string) $query;
        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CLIENT_RESULTSET_CLASS);
    }


    /**
     * Create client(s)
     *
     * @param  stdClass $clientDetails Client data structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Clients
     */
    public function createClient($clientDetails)
    {
        $newClient = new stdClass;
        $newClient->clients[] = $clientDetails;
        $options = json_encode($newClient);

        $endPoint = 'clients';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CLIENT_CLASS);
    }


    /**
     * Update a client
     *
     * @param  int $id The client ID to update
     * @param  stdClass $clientDetails Client data structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Client
     */
    public function updateClient($id, $clientDetails)
    {
        $options = json_encode($clientDetails);

        $endPoint = 'clients/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'PUT',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CLIENT_CLASS);
    }


    /**
     * List Client Addresses
     *
     * @param  int $clientId ID of the specific client
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Addresses
     */
    public function listClientAddresses($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/addresses';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::ADDRESS_RESULTSET_CLASS);
    }


    /**
     * Get a single client address by Id
     *
     * @param  int $clientId ID of the specific client
     * @param  int $addressId ID of the specific address
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Address_Client
     */
    public function showClientAddress($clientId, $addressId)
    {
        $endPoint = 'clients/' . $clientId . '/addresses/' . $addressId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::ADDRESS_CLASS);
    }


    /**
     * Create client address(es)
     *
     * @param  int $clientId ID of the specific client
     * @param  array $addresses Array of address data structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Addresses
     */
    public function createClientAddress($clientId, $addresses)
    {
        $newAddresses = new stdClass;
        foreach ($addresses as $address) {
            $newAddresses->addresses[] = $address;
        }
        $options = json_encode($newAddresses);

        $endPoint = 'clients/' . $clientId . '/addresses';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        return $this->_postProcess($response, self::ADDRESS_RESULTSET_CLASS);
    }


    /**
     * Update a single client address
     *
     * @param  int $clientId ID of the specific client
     * @param  int $addressId ID of the specific address
     * @param  stdClass $address Address data structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Address_Client
     */
    public function updateClientAddress($clientId, $addressId, $address)
    {
        $options = json_encode($address);

        $endPoint = 'clients/' . $clientId . '/addresses/' . $addressId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'PUT',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut('/' . $endPoint, $options);

        return $this->_postProcess($response, self::ADDRESS_CLASS);
    }


    /**
     * List Client Phone Numbers
     *
     * @param  int $clientId ID of the specific client
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_PhoneNumbers
     */
    public function listClientPhoneNumbers($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/phone_numbers';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::PHONENUMBER_RESULTSET_CLASS);
    }


    /**
     * Get a single client phone number by Id
     *
     * @param  int $clientId ID of the specific client
     * @param  int $phoneNumberId ID of the specific phone number
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_PhoneNumber_Client
     */
    public function showClientPhoneNumber($clientId, $phoneNumberId)
    {
        $endPoint = 'clients/' . $clientId . '/phone_numbers/' . $phoneNumberId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::PHONENUMBER_CLASS);
    }


    /**
     * Create a client phone number
     *
     * @param  int $clientId ID of the specific client
     * @param  array $phoneNumbers Array of phone numbers structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_PhoneNumbers
     */
    public function createClientPhoneNumber($clientId, $phoneNumbers)
    {
        $newPhoneNumbers = new stdClass;
        foreach ($phoneNumbers as $phoneNumber) {
            $newPhoneNumbers->phone_numbers[] = $phoneNumber;
        }
        $options = json_encode($newPhoneNumbers);

        $endPoint = 'clients/' . $clientId . '/phone_numbers';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        return $this->_postProcess($response, self::PHONENUMBER_RESULTSET_CLASS);
    }


    /**
     * Update a single client phone number
     *
     * @param  int $clientId ID of the specific client
     * @param  int $phoneNumberId ID of the specific phone number
     * @param  stdClass $phoneNumberDetails Client data structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_PhoneNumber_Client
     */
    public function updateClientPhoneNumber($clientId, $phoneNumberId, $phoneNumberDetails)
    {
        $options = json_encode($phoneNumberDetails);

        $endPoint = 'clients/' . $clientId . '/phone_numbers/' . $phoneNumberId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'PUT',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut('/' . $endPoint, $options);

        return $this->_postProcess($response, self::PHONENUMBER_CLASS);
    }


    /**
     * List Client Email Addresses
     *
     * @param  int $clientId ID of the specific client
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_EmailAddresses
     */
    public function listClientEmailAddresses($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/email_addresses';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EMAILADDRESS_RESULTSET_CLASS);
    }


    /**
     * Get a single client email address by Id
     *
     * @param  int $clientId ID of the specific client
     * @param  int $emailAddressId ID of the specific email address
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_EmailAddress_Client
     */
    public function showClientEmailAddress($clientId, $emailAddressId)
    {
        $endPoint = 'clients/' . $clientId . '/email_addresses/' . $emailAddressId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EMAILADDRESS_CLASS);
    }


    /**
     * Create client email address(es)
     *
     * @param  int $clientId ID of the specific client
     * @param  array $emailAddresses Array of email addresses structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_EmailAddresses
     */
    public function createClientEmailAddress($clientId, $emailAddresses)
    {
        $newEmailAddresses = new stdClass;
        foreach ($emailAddresses as $emailAddress) {
            $newEmailAddresses->email_addresses[] = $emailAddress;
        }
        $options = json_encode($newEmailAddresses);

        $endPoint = 'clients/' . $clientId . '/email_addresses';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EMAILADDRESS_RESULTSET_CLASS);
    }


    /**
     * Update a single client email address
     *
     * @param  int $clientId ID of the specific client
     * @param  int $emailAddressId ID of the specific email address
     * @param  stdClass $emailAddressDetails Client data structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_EmailAddress_Client
     */
    public function updateClientEmailAddress($clientId, $emailAddressId, $emailAddressDetails)
    {
        $options = json_encode($emailAddressDetails);

        $endPoint = 'clients/' . $clientId . '/email_addresses/' . $emailAddressId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'PUT',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EMAILADDRESS_CLASS);
    }


    /**
     * List Client credit cards
     *
     * @param  int $clientId ID of the specific client
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_CreditCards
     */
    public function listClientCreditCards($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/credit_cards';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CREDITCARD_RESULTSET_CLASS);
    }


    /**
     * Get a single client credit card by Id
     *
     * NOTE: For PCI compliance, once you create a credit card you can NEVER
     * retrieve the full card number, expiration date or verification code.
     *
     * @param  int $clientId ID of the specific client
     * @param  int $creditCardId ID of the specific credit card
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_EmailAddress_Client
     */
    public function showClientCreditCard($clientId, $creditCardId)
    {
        $endPoint = 'clients/' . $clientId . '/credit_cards/' . $creditCardId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CREDITCARD_CLASS);
    }


    /**
     * Create client credit card(s)
     *
     *  NOTE: Currently the API only supports creating a single card at a time.
     *        If you pass in more than one credit card to POST, it will just
     *        ignore everything after the first one.
     *        This will change in a future release to allow multiples.
     *
     * @param  int $clientId ID of the specific client
     * @param  array $creditCards Array of credit cards structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_EmailAddresses
     */
    public function createClientCreditCard($clientId, $creditCards)
    {
        $newCreditCards = new stdClass;
        foreach ($creditCards as $creditCard) {
            /**
             * Strip non-numeric chars from CC number and validate it
             */
            $creditCard->number = $this->_cleanAndValidateCreditCardNumber(
                $creditCard->number
            );
            $newCreditCards->credit_cards[] = $creditCard;
        }
        $options = json_encode($newCreditCards);

        $endPoint = 'clients/' . $clientId . '/credit_cards';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CREDITCARD_RESULTSET_CLASS);
    }


    /**
     * Update a single client credit card
     *
     * @param  int $clientId ID of the specific client
     * @param  int $creditCardId ID of the specific email address
     * @param  stdClass $emailAddressDetails Client data structured per API example
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_EmailAddress_Client
     */
    public function updateClientCreditCard($clientId, $creditCardId, $creditCardDetails)
    {
        /**
         * Strip non-numeric chars from CC number and validate it
         */
        $creditCardDetails->number = $this->_cleanAndValidateCreditCardNumber(
            $creditCardDetails->number
        );
        $options = json_encode($creditCardDetails);

        $endPoint = 'clients/' . $clientId . '/email_addresses/' . $creditCardId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'PUT',
            $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CREDITCARD_CLASS);
    }


    /**
     * Remove non-numeric characters from credit card number and validate length
     *
     * NOTE: This does NOT validate the card against the Luhn algorithm.
     *
     * @param  string $creditCardNumber
     * @throws TicketEvolution_Webservice_Exception
     * @return string
     */
    protected function _cleanAndValidateCreditCardNumber($creditCardNumber)
    {
        $cleanNumber = preg_replace('/[^0-9]/', '', $creditCardNumber);

        /**
         * @see Zend_Validate_CreditCard
         */
        require_once 'Zend/Validate/CreditCard.php';

        $valid = new Zend_Validate_CreditCard();
        if ($valid->isValid($cleanNumber)) {
            return $cleanNumber;
        } else {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'The credit card provided is not a valid credit card number'
            );
        }

    }


    /**
     * List Offices for a Brokerage
     *
     * @param  array $options Options to use
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Offices
     */
    public function listOffices(array $options)
    {
        $endPoint = 'offices';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::OFFICE_RESULTSET_CLASS);
    }


    /**
     * Get a single office by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Office
     */
    public function showOffice($id)
    {
        $endPoint = 'offices/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::OFFICE_CLASS);
    }


    /**
     * Search for office(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Offices
     */
    public function searchOffices($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'You must provide a non-empty query string'
            );
        }

        $endPoint = 'offices/search';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options['q'] = (string) $query;
        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::OFFICE_RESULTSET_CLASS);
    }


    /**
     * List Users for a Brokerage Office
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Users
     */
    public function listUsers(array $options)
    {
        $endPoint = 'users';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::USER_RESULTSET_CLASS);
    }


    /**
     * Get a single user by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_User
     */
    public function showUser($id)
    {
        $endPoint = 'users/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::USER_CLASS);
    }


    /**
     * Search for user(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Users
     */
    public function searchUsers($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'You must provide a non-empty query string'
            );
        }

        $endPoint = 'users/search';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options['q'] = (string) $query;
        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::USER_RESULTSET_CLASS);
    }


    /**
     * List Categories
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Categories
     */
    public function listCategories(array $options)
    {
        $endPoint = 'categories';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CATEGORY_RESULTSET_CLASS);
    }


    /**
     * List Categories that have been deleted
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Categories
     */
    public function listCategoriesDeleted(array $options)
    {
        $endPoint = 'categories/deleted';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CATEGORY_RESULTSET_CLASS);
    }


    /**
     * Get a single category by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Category
     */
    public function showCategory($id)
    {
        $endPoint = 'categories/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CATEGORY_CLASS);
    }


    /**
     * List Events
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Events
     */
    public function listEvents(array $options)
    {
        $endPoint = 'events';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EVENT_RESULTSET_CLASS);
    }


    /**
     * List Events that have been deleted
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Events
     */
    public function listEventsDeleted(array $options)
    {
        $endPoint = 'events/deleted';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EVENT_RESULTSET_CLASS);
    }


    /**
     * Get a single event by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Event
     */
    public function showEvent($id)
    {
        $endPoint = 'events/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EVENT_CLASS);
    }


    /**
     * List Performers
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Performers
     */
    public function listPerformers(array $options)
    {
        $endPoint = 'performers';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::PERFORMER_RESULTSET_CLASS);
    }


    /**
     * List Performers that have been deleted
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Performers
     */
    public function listPerformersDeleted(array $options)
    {
        $endPoint = 'performers/deleted';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::PERFORMER_RESULTSET_CLASS);
    }


    /**
     * Get a single Performer by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Performer
     */
    public function showPerformer($id)
    {
        $endPoint = 'performers/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::PERFORMER_CLASS);
    }


    /**
     * Search for performer(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Performers
     */
    public function searchPerformers($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'You must provide a non-empty query string'
            );
        }

        $endPoint = 'performers/search';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options['q'] = (string) $query;
        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::PERFORMER_RESULTSET_CLASS);
    }


    /**
     * Search
     * Currently searches both performers and venues for a match and will return
     * any combination of such. The type will be denoted in the results.
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_SearchResults
     */
    public function search($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'You must provide a non-empty query string'
            );
        }

        $endPoint = 'search';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options['q'] = (string) $query;
        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::SEARCH_RESULTSET_CLASS);
    }


    /**
     * List Venues
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Venues
     */
    public function listVenues(array $options)
    {
        $endPoint = 'venues';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::VENUE_RESULTSET_CLASS);
    }


    /**
     * List Venues that have been deleted
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Venues
     */
    public function listVenuesDeleted(array $options)
    {
        $endPoint = 'venues/deleted';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::VENUE_RESULTSET_CLASS);
    }


    /**
     * Get a single Venue by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Venue
     */
    public function showVenue($id)
    {
        $endPoint = 'venues/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::VENUE_CLASS);
    }


    /**
     * Search for venue(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Venues
     */
    public function searchVenues($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'You must provide a non-empty query string'
            );
        }

        $endPoint = 'venues/search';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options['q'] = (string) $query;
        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::VENUE_RESULTSET_CLASS);
    }


    /**
     * List Configurations
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Configurations
     */
    public function listConfigurations(array $options)
    {
        $endPoint = 'configurations';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CONFIGURATION_RESULTSET_CLASS);
    }


    /**
     * Get a single Configuration by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Configuration
     */
    public function showConfiguration($id)
    {
        $endPoint = 'configurations/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::CONFIGURATION_CLASS);
    }


    /**
     * List Ticket Groups
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_TicketGroups
     */
    public function listTicketGroups(array $options)
    {
        if (!isset($options['event_id'])) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                '"event_id" is a required parameter'
            );
        }

        $endPoint = 'ticket_groups';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::TICKETGROUP_RESULTSET_CLASS);
    }


    /**
     * Get a single Ticket by Id Group
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_TicketGroup
     */
    public function showTicketGroup($id)
    {
        $endPoint = 'ticket_groups/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::TICKETGROUP_CLASS);
    }


    /**
     * List Orders
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Orders
     */
    public function listOrders(array $options)
    {
        $endPoint = 'orders';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::ORDER_RESULTSET_CLASS);
    }


    /**
     * Get a single order by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Order
     */
    public function showOrder($id)
    {
        $endPoint = 'orders/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::ORDER_CLASS);
    }


    /**
     * Create order(s)
     *
     * @param  array $orders Multiple items per order is not currently supported
     *      by the API.
     * @param bool $fulfillment Whether this is a fulfillment order or not
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Orders
     */
    public function createOrder(array $orders, $fulfillment=false)
    {
        $newOrders = new stdClass;
        foreach ($orders as $order) {
            $newOrders->orders = $orders;
        }
        $options = json_encode($newOrders);

        $endPoint = 'orders';
        if ($fulfillment) {
            $endPoint = 'orders/fulfillments';
        }

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri, $this->_secretKey, 'POST', $endPoint, $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        return $this->_postProcess($response, self::ORDER_RESULTSET_CLASS);
    }


    /**
     * Create Fulfillment order(s)
     *
     * Utility method that calls createOrder()
     *
     * @param  array $order Can be either an array with details for a single order
                            or an array of arrays for multiple orders.
                            Multiple items per order is not currently supported
                            by the API.
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Orders
     */
    public function createFulfillmentOrder(array $orders)
    {
        return $this->createOrder($orders, true);
    }


    /**
     * Accept an order
     *
     * @param int $orderId ID of the order to accept
     * @param int $userId ID of the user who reviewed and accepts this order
     * @throws TicketEvolution_Webservice_Exception
     * @return bool
     */
    public function acceptOrder($orderId, $userId)
    {
        $options = json_encode(array('reviewer_id' => $userId));

        $endPoint = 'orders/' . $orderId . '/accept';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri, $this->_secretKey, 'POST', $endPoint, $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        if ($response->isError()) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'An error occurred sending request. Status code: '
                . $response->getStatus()
            );
        }

        return true;
    }


    /**
     * Reject an order
     *
     * @param int $orderId ID of the order to accept
     * @param int $userId ID of the user who reviewed and rejects this order
     * @throws TicketEvolution_Webservice_Exception
     * @return bool
     */
    public function rejectOrder($orderId, $userId, $reason)
    {
        $allowedReasons = array(
            'Tickets No Longer Available',
            'Tickets Priced Incorrectly',
            'Duplicate Order',
            'Fraudulent Order',
        );
        if (!in_array($reason, $allowedReasons)) {
            throw new OutOfBoundsException(
                'The rejection reason you provided is not allowed. '
                . 'Rejection reason must be one of: ' . implode(', ', $allowedReasons)
            );
        }

        $rejection = array(
            'reviewer_id' => $userId,
            'rejection_reason' => $reason,
        );
        $options = json_encode($rejection);

        $endPoint = 'orders/' . $orderId . '/reject';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri, $this->_secretKey, 'POST', $endPoint, $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        if ($response->isError()) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'An error occurred sending request. Status code: '
                . $response->getStatus()
            );
        }

        return true;
    }


    /**
     * Complete an order
     *
     * @param int $orderId ID of the order to accept
     * @param int $userId ID of the user who reviewed and rejects this order
     * @throws TicketEvolution_Webservice_Exception
     * @return bool
     */
    public function completeOrder($orderId)
    {
        $endPoint = 'orders/' . $orderId . '/complete';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $this->_requestSignature = self::computeSignature(
            $this->_baseUri, $this->_secretKey, 'POST', $endPoint, $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        if ($response->isError()) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'An error occurred sending request. Status code: '
                . $response->getStatus()
            );
        }

        return true;
    }


    /**
     * List Shipments
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Shipments
     */
    public function listShipments(array $options)
    {
        $endPoint = 'shipments';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::SHIPMENT_RESULTSET_CLASS);
    }


    /**
     * Get a single shipment by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Shipment
     */
    public function showShipment($id)
    {
        $endPoint = 'shipments/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::SHIPMENT_CLASS);
    }


    /**
     * Create shipment(s)
     *
     * @param  array $shipments
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Shipments
     */
    public function createShipment(array $shipments)
    {
        $newShipments = new stdClass;
        foreach ($shipments as $shipment) {
            $newShipments->shipments = $shipments;
        }
        $options = json_encode($newShipments);

        $endPoint = 'shipments';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri, $this->_secretKey, 'POST', $endPoint, $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost('/' . $endPoint, $options);

        return $this->_postProcess($response, self::SHIPMENT_RESULTSET_CLASS);
    }


    /**
     * Update a single shipment
     *
     * @param  array $shipments
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Shipment
     */
    public function updateShipment(array $shipments)
    {
        $newShipments = new stdClass;
        foreach ($shipments as $shipment) {
            $newShipments->shipments = $shipments;
        }
        $options = json_encode($newShipments);

        $endPoint = 'shipments';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array();
        $options = $this->_prepareOptions('PUT', $endPoint, $options, $defaultOptions);

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut('/' . $endPoint, $options);

        return $this->_postProcess($response, self::SHIPMENT_CLASS);
    }


    /**
     * List Quotes
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Quotes
     */
    public function listQuotes(array $options)
    {
        $endPoint = 'quotes';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::QUOTE_RESULTSET_CLASS);
    }


    /**
     * Get a single quote by Id
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Quote
     */
    public function showQuote($id)
    {
        $endPoint = 'quotes/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::QUOTE_CLASS);
    }


    /**
     * Search for quote(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_Quotes
     */
    public function searchQuotes($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'You must provide a non-empty query string'
            );
        }

        $endPoint = 'quotes/search';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options['q'] = (string) $query;
        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::QUOTE_RESULTSET_CLASS);
    }


    /**
     * List EvoPay Accounts
     *
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_EvoPayAccounts
     */
    public function listEvoPayAccounts(array $options)
    {
        $endPoint = 'accounts';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EVOPAYACCOUNT_RESULTSET_CLASS);
    }


    /**
     * Get a single EvoPay by Account ID
     *
     * @param  int $id
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_EvoPayAccount
     */
    public function showEvopayaccount($id)
    {
        $endPoint = 'accounts/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EVOPAY_ACCOUNT_CLASS);
    }


    /**
     * List EvoPay Transactions
     *
     * @param  int $accountId EvoPay Account ID
     * @param  array $options Options to use for the search query
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_Webservice_ResultSet_EvoPayTransactions
     */
    public function listEvoPayTransactions($accountId, array $options)
    {
        $endPoint = 'accounts/' . $accountId . '/transactions';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array(
            'page'  => '1',
            'per_page' => '100'
        );
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet($endPoint, $options);

        return $this->_postProcess($response, self::EVOPAYTRANSACTION_CLASS);
    }


    /**
     * Get a single EvoPay by Id Transaction
     *
     * @param  int $accountId EvoPay Account ID
     * @param  int $transactionId
     * @throws TicketEvolution_Webservice_Exception
     * @return TicketEvolution_EvoPayTransaction
     */
    public function showEvoPayTransacations($accountId, $transactionId)
    {
        $endPoint = 'accounts/' . $accountId . '/transactions/' . $transactionId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $defaultOptions = array();
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restGet('/' . $endPoint, $options);

        return $this->_postProcess($response, self::EVOPAY_TRANSACTION_CLASS);
    }


    /**
     * Returns a reference to the REST client
     *
     * @return Zend_Rest_Client
     */
    public function getRestClient()
    {
        if ($this->_rest === null) {
            /**
             * @see Zend_Rest_Client
             */
            require_once 'Zend/Rest/Client.php';
            $this->_rest = new Zend_Rest_Client();

            /**
             * @see Zend_Http_Client
             */
            require_once 'Zend/Http/Client.php';
            $httpClient = new Zend_Http_Client(
                $this->_baseUri,
                array(
                    'keepalive' => $this->_usePersistentConnections
                )
            );


            /**
             * The Ticket Evolution Sandbox uses a self-signed certificate which,
             * by default is not allowed. If we are using https in the sandbox lets
             * tweak the options to allow this self-signed certificate.
             *
             * @link http://framework.zend.com/manual/en/zend.http.client.adapters.html Example 2
             */
            if (strpos($this->_baseUri, 'sandbox') !== false) {
                $streamOptions = array(
                    // Verify server side certificate,
                    // Accept self-signed SSL certificate
                    'ssl' => array(
                        //'verify_peer' => true,
                        'allow_self_signed' => true,
                    )
                );
            } else {
                $streamOptions = array();
            }

            /**
             * Create an adapter object and attach it to the HTTP client
             *
             * @see Zend_Http_Client_Adapter_Socket
             */
            require_once 'Zend/Http/Client/Adapter/Socket.php';
            $adapter = new Zend_Http_Client_Adapter_Socket();

            $adapterConfig = array (
                'persistent'    => $this->_usePersistentConnections,
            );
            $adapter->setConfig($adapterConfig);

            $httpClient->setAdapter($adapter);

            // Pass the streamOptions array to setStreamContext()
            $adapter->setStreamContext($streamOptions);

            $this->_rest->setHttpClient($httpClient);
        }
        return $this->_rest;
    }


    /**
     * Set REST client
     *
     * @param Zend_Rest_Client
     * @return TicketEvolution_Webservice
     */
    public function setRestClient(Zend_Rest_Client $client)
    {
        $this->_rest = $client;
        return $this;
    }


    /**
     * Set special headers for request
     *
     * @param  string  $apiToken
     * @param  string  $apiVersion
     * @param  string  $requestSignature
     * @return void
     */
    protected function _setHeaders($apiToken, $apiVersion, $requestSignature=null)
    {
        $headers = array(
            'User-Agent' => 'TicketEvolution_Webservice',
            'X-Token'   => (string)$apiToken,
            'Accept'    => (string)'application/vnd.ticketevolution.api+json; version=' . $apiVersion
        );
        if (!empty($requestSignature)) {
            $headers['X-Signature'] = (string)$requestSignature;
        }
        $this->_rest->getHttpClient()->setHeaders($headers);
    }


    /**
     * Prepare options for request
     *
     * @param  string $action         Action to perform [GET|POST|PUT|DELETE]
     * @param  array  $endPoint       The endPoint
     * @param  array  $options        User supplied options
     * @param  array  $defaultOptions Default options
     * @return array
     */
    protected function _prepareOptions($action, $endPoint, array $options, array $defaultOptions)
    {
        $options = array_merge($defaultOptions, $options);
        ksort($options);

        if ($this->_secretKey !== null) {
            $this->_requestSignature = self::computeSignature(
                $this->_baseUri, $this->_secretKey, (string)$action, (string)$endPoint, $options
            );
        }
        return $options;
    }

    /**
     * Compute Signature for X-Signature header
     *
     * @param  string $baseUri
     * @param  string $secretKey
     * @param  string $action
     * @param  string $endPoint
     * @param  array $options
     * @return string
     */
    static public function computeSignature($baseUri, $secretKey, $action, $endPoint, $options)
    {
        $signature = self::buildRawSignature($baseUri, $action, $endPoint, $options);

        /**
         * @see Zend_Crypt_Hmac
         */
        require_once 'Zend/Crypt/Hmac.php';

        return base64_encode(
            Zend_Crypt_Hmac::compute($secretKey, 'sha256', $signature, Zend_Crypt_Hmac::BINARY)
        );
    }

    /**
     * Build the Raw Signature Text
     *
     * @param  string $baseUri
     * @param  string $action       One of [GET|POST|PUT|DELETE]
     * @param  string $endPoint
     * @param  array $options
     * @return string
     */
    static public function buildRawSignature($baseUri, $action, $endPoint, $options)
    {
        $signature = $action . ' ' . preg_replace('/https?:\/\//', '', $baseUri) . '/' . $endPoint;
        if (!empty($options)) {
            if (is_array($options)) {
                // Turn the $options into GET parameters
                ksort($options);
                $params = array();
                foreach ($options AS $k => $v) {
                    //$params[] = $k . '=' . rawurlencode($v);
                    $params[] = urlencode($k) . '=' . urlencode($v);
                    //$params[] = $k . '=' . $v;
                }
                $signature .= '?' . implode('&', $params);
            } else {
                $signature .= '?' . (string) $options;
            }
        }
        return $signature;
    }


    /**
     * Allows post-processing logic to be applied.
     * Subclasses may override this method.
     *
     * @param string $responseBody The response body to process
     * @param string $returnAsClass The type of class an individual record
     *     should be returned as
     * @return void
     */
    protected function _postProcess($response, $returnAsClass=null)
    {

        /**
         * Uncomment for debugging to see the actual request and response
         * or in your code use
         * $tevo->getRestClient()->getHttpClient()->getLastRequest() and
         * $tevo->getRestClient()->getHttpClient()->getLastResponse()
         */
        /**
        echo PHP_EOL;
        var_dump($this->getRestClient()->getHttpClient()->getLastRequest());
        echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
        echo PHP_EOL;
        var_dump($this->getRestClient()->getHttpClient()->getLastResponse());
        echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
         */


        if ($response->isError()) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'An error occurred sending request. Status code: '
                . $response->getStatus()
            );
        }

        $decodedJson = json_decode($response->getBody(), false);
        if (is_null($decodedJson)) {
            /**
             * @see TicketEvolution_Webservice_Exception
             */
            require_once 'TicketEvolution/Webservice/Exception.php';
            throw new TicketEvolution_Webservice_Exception(
                'An error occurred decoding the JSON received: ' . json_last_error()
            );
        }

        if (is_null($returnAsClass)) {
            return $decodedJson;
        } else {
            /*
             * Load $returnAsClass. This throws an exception if the specified
             * class cannot be loaded.
             */
            if (!class_exists($returnAsClass)) {
                /**
                 * @see Zend_Loader
                 */
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($returnAsClass);
            }

            /*
             * Create an instance of the item's class.
             */
            return new $returnAsClass($decodedJson);
        }
    }


}
