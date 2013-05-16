<?php

/**
 * Ticket Evolution PHP Library for use with Zend Framework
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
 * @package     TicketEvolution\Webservice
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


namespace TicketEvolution;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 * @link        http://developer.ticketevolution.com/
 */
class Webservice
{
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
    protected $_apiVersion = '9';


    /**
     * Reference to REST client object
     *
     * @var \Zend_Rest_Client
     */
    protected $_rest = null;


    /**
     * Whether or not to use persistent connections.
     *
     * @var bool
     */
    protected $_usePersistentConnections = true;


    /**
     * Defines how the data is returned.
     * * resultset   = Default. An iterable TicketEvolution\Webservice\Resultset object
     * * json        = The JSON received with no conversion
     * * decodedjson = First performs a decode_json()
     *
     * @var string [resultset,json,decodedjson]
     */
    public $resultType = 'resultset';


    /**
     * Constructs a new Ticket Evolution Web Services Client
     *
     * @param  mixed $config  An array or \Zend_Config object with adapter parameters.
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice
     */
    public function __construct($config)
    {
        if ($config instanceof \Zend_Config) {
            $config = $config->toArray();
        }

        /*
         * Verify that parameters are in an array.
         */
        if (!is_array($config)) {
            throw new namespace\Exception(
                'Parameters must be in an array or a \Zend_Config object'
            );
        }

        /*
         * Verify that an API token has been specified.
         */
        if (!is_string($config['apiToken']) || empty($config['apiToken'])) {
            throw new namespace\Exception(
                'API token must be specified in a string'
            );
        }

        /*
         * Verify that an API secret key has been specified.
         */
        if (!is_string($config['secretKey']) || empty($config['secretKey'])) {
            throw new namespace\Exception(
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

        $this->_apiPrefix = '/v' . $this->_apiVersion . '/';
    }


    /**
     * List Brokerages
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/brokerages#list
     */
    public function listBrokerages(array $options)
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single brokerage by Id
     *
     * @param  int $id The brokerage ID
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/brokerages#show
     */
    public function showBrokerage($id)
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Search for brokerage(s)
     *
     * @param  string $query The query string
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/brokerages#search
     */
    public function searchBrokerages($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new namespace\Exception(
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Clients
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/clients#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single client by Id
     *
     * @param  int $id The Client ID
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/clients#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Search for client(s)
     *
     * @param  string $query The query string
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/clients#search
     */
    public function searchClients($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new namespace\Exception(
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /*
     * Create client(s)
     *
     * @param  array $clients Array of client objects
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/clients#create
     */
    public function createClients(array $clients)
    {
        $body = new \stdClass;
        $body->clients = $clients;
        $options = json_encode($body);

        $endPoint = 'clients';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Update a client
     *
     * @param  int $id The client ID to update
     * @param  object $clientDetails Client object structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/clients#update
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
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Client Companies
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/companies#list
     */
    public function listClientCompanies(array $options)
    {
        $endPoint = 'companies';

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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single client company by Id
     *
     * @param  int $id The Client Company ID
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/companies#show
     */
    public function showClientCompany($id)
    {
        $endPoint = 'companies/' . $id;

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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Create client companies
     *
     * @param  array $companies Array of objects structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/companies#create
     */
    public function createClientCompanies(array $companies)
    {
        $body = new \stdClass;
        $body->companies = $companies;
        $options = json_encode($body);

        $endPoint = 'companies';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Update a client company
     *
     * @param  int $id The client ID to update
     * @param  object $companyDetails Company object structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/companies#update
     */
    public function updateClientCompany($id, $companyDetails)
    {
        $options = json_encode($companyDetails);

        $endPoint = 'companies/' . $id;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'PUT',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Client Addresses
     *
     * @param  int $clientId ID of the specific client
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/addresses#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single client address by Id
     *
     * @param  int $clientId ID of the specific client
     * @param  int $addressId ID of the specific address
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/addresses#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Create client address(es)
     *
     * @param  int $clientId ID of the specific client
     * @param  array $addresses Array of address objects structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/addresses#create
     */
    public function createClientAddresses($clientId, array $addresses)
    {
        $body = new \stdClass;
        $body->addresses = $addresses;
        $options = json_encode($body);

        $endPoint = 'clients/' . $clientId . '/addresses';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Update a single client address
     *
     * @param  int $clientId ID of the specific client
     * @param  int $addressId ID of the specific address
     * @param  object $address Address object structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/addresses#update
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
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Client Phone Numbers
     *
     * @param  int $clientId ID of the specific client
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/phone-numbers#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single client phone number by Id
     *
     * @param  int $clientId ID of the specific client
     * @param  int $phoneNumberId ID of the specific phone number
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/phone-numbers#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Create client phone number(s)
     *
     * @param  int $clientId ID of the specific client
     * @param  array $phoneNumbers Array of phone numbers objects per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/phone-numbers#create
     */
    public function createClientPhoneNumbers($clientId, array $phoneNumbers)
    {
        $body = new \stdClass;
        $body->phone_numbers = $phoneNumbers;
        $options = json_encode($body);

        $endPoint = 'clients/' . $clientId . '/phone_numbers';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Update a single client phone number
     *
     * @param  int $clientId ID of the specific client
     * @param  int $phoneNumberId ID of the specific phone number
     * @param  object $phoneNumberDetails Phone number object structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/phone-numbers#update
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
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Client Email Addresses
     *
     * @param  int $clientId ID of the specific client
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/email-addresses#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single client email address by Id
     *
     * @param  int $clientId ID of the specific client
     * @param  int $emailAddressId ID of the specific email address
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/email-addresses#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Create client email address(es)
     *
     * @param  int $clientId ID of the specific client
     * @param  array $emailAddresses Array of email address objects structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/email-addresses#create
     */
    public function createClientEmailAddresses($clientId, array $emailAddresses)
    {
        $body = new \stdClass;
        $body->email_addresses = $emailAddresses;
        $options = json_encode($body);

        $endPoint = 'clients/' . $clientId . '/email_addresses';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Update a single client email address
     *
     * @param  int $clientId ID of the specific client
     * @param  int $emailAddressId ID of the specific email address
     * @param  object $emailAddressDetails Client object structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/email-addresses#update
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
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Client credit cards
     *
     * @param  int $clientId ID of the specific client
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/credit-cards#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * THIS ENDPOINT DOES NOT YET EXIST!
     *
     * Get a single client credit card by Id
     *
     * NOTE: For PCI compliance, once you create a credit card you can NEVER
     * retrieve the full card number, expiration date or verification code.
     *
     * @param  int $clientId ID of the specific client
     * @param  int $creditCardId ID of the specific credit card
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/credit-cards#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
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
     * @param  array $creditCards Array of credit card objects structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/credit-cards#create
     */
    public function createClientCreditCards($clientId, array $creditCards)
    {
        $body = new \stdClass;
        foreach ($creditCards as $creditCard) {
            /**
             * Strip non-numeric chars from CC number and validate it
             */
            $creditCard->number = $this->_cleanAndValidateCreditCardNumber(
                $creditCard->number
            );
            $body->credit_cards[] = $creditCard;
        }
        $options = json_encode($body);

        $endPoint = 'clients/' . $clientId . '/credit_cards';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * THIS ENDPOINT DOES NOT YET EXIST!
     *
     * Update a single client credit card
     *
     * @param  int $clientId ID of the specific client
     * @param  int $creditCardId ID of the specific email address
     * @param  object $creditCardDetails Client object structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/credit-cards#update
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
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Remove non-numeric characters from credit card number and validate it
     *
     * @param  string $creditCardNumber
     * @throws Webservice\Exception
     * @return string
     */
    protected function _cleanAndValidateCreditCardNumber($creditCardNumber)
    {
        $cleanNumber = preg_replace('/[^0-9]/', '', $creditCardNumber);

        $valid = new \Zend_Validate_CreditCard();
        if ($valid->isValid($cleanNumber)) {
            return $cleanNumber;
        } else {
            throw new namespace\Exception(
                'The credit card provided is not a valid credit card number'
            );
        }

    }


    /**
     * List Offices for a Brokerage
     *
     * @param  array $options Options to use
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/offices#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single office by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/offices#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Search for office(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/offices#search
     */
    public function searchOffices($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new namespace\Exception(
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Users for a Brokerage Office
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/users#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single user by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/users#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Search for user(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/users#search
     */
    public function searchUsers($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new namespace\Exception(
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Active Categories
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/categories#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Categories that have been deleted
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/categories#deleted
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single category by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/categories#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Active Events
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/events#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Events that have been deleted
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/events#deleted
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single event by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/events#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Active Performers
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/performers#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Performers that have been deleted
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/performers#deleted
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single Performer by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/performers#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Search for performer(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/performers#search
     */
    public function searchPerformers($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new namespace\Exception(
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Search
     * Currently searches both performers and venues for a match and will return
     * any combination of such. The type will be denoted in the results.
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/search#list
     */
    public function search($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new namespace\Exception(
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Active Venues
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/venues#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Venues that have been deleted
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/venues#deleted
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single Venue by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/venues#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Search for venue(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/venues#search
     */
    public function searchVenues($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new namespace\Exception(
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Configurations
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/configurations#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single Configuration by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/configurations#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Ticket Groups
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/ticket-groups#list
     */
    public function listTicketGroups(array $options)
    {
        if (!isset($options['event_id'])) {
            throw new namespace\Exception(
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single Ticket by Id Group
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/ticket-groups#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Orders
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/orders#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single order by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/orders#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Create order(s)
     *
     * @param  array $orders Array of order objects as defined by API
     * @param bool $fulfillment Whether this is a fulfillment order or not
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/orders#create_client_order
     */
    public function createOrders(array $orders, $fulfillment=false)
    {
        $body = new \stdClass;
        $body->orders = $orders;
        $options = json_encode($body);

        $endPoint = 'orders';
        if ($fulfillment) {
            $endPoint = 'orders/fulfillments';
        }

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Create Fulfillment order(s)
     *
     * Utility method that calls createOrders()
     *
     * @param  array $order Array of order objects.
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/orders#create_fulfillment_order
     */
    public function createFulfillmentOrders(array $orders)
    {
        return $this->createOrders($orders, true);
    }


    /**
     * Update an order
     *
     * @param  int $orderId ID of the specific order
     * @param  object $orderDetails Order object structured per API example
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/orders#update
     */
    public function updateOrder($orderId, $orderDetails)
    {
        $options = json_encode($orderDetails);

        $endPoint = 'orders/' . $orderId;

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'PUT',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPut($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Accept an order
     *
     * @param int $orderId ID of the order to accept
     * @param int $userId ID of the user who reviewed and accepts this order
     * @throws Webservice\Exception
     * @return bool
     * @link http://developer.ticketevolution.com/endpoints/orders#accept_order
     */
    public function acceptOrder($orderId, $userId)
    {
        $options = json_encode(array('reviewer_id' => $userId));

        $endPoint = 'orders/' . $orderId . '/accept';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        if ($response->isError()) {
            throw new namespace\Exception(
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
     * @throws Webservice\Exception
     * @return bool
     * @link http://developer.ticketevolution.com/endpoints/orders#reject_order
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
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        if ($response->isError()) {
            throw new namespace\Exception(
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
     * @throws Webservice\Exception
     * @return bool
     * @link http://developer.ticketevolution.com/endpoints/orders#complete_order
     */
    public function completeOrder($orderId)
    {
        $endPoint = 'orders/' . $orderId . '/complete';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $options = array();
        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        if ($response->isError()) {
            throw new namespace\Exception(
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
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/shipments#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single shipment by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/shipments#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Create shipment(s)
     *
     * @param  array $shipments Array of shipment objects
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/shipments#create
     */
    public function createShipments(array $shipments)
    {
        $body = new \stdClass;
        $body->shipments = $shipments;
        $options = json_encode($body);

        $endPoint = 'shipments';

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client->getHttpClient()->resetParameters();
        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        $response = $client->restPost($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Update a single shipment
     *
     * @param  object $shipment
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/shipments#update
     */
    public function updateShipment($shipment)
    {
        $options = json_encode($shipment);

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

        return $this->_postProcess($response);
    }


    /**
     * List Quotes
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/quotes#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single quote by Id
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/quotes#show
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Search for quote(s)
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/quotes#search
     */
    public function searchQuotes($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new namespace\Exception(
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List EvoPay Accounts
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/accounts#list
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Get a single EvoPay by Account ID
     *
     * @param  int $id
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/accounts#show
     */
    public function showEvoPayAccount($id)
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List EvoPay Transactions
     *
     * @param  int $accountId EvoPay Account ID
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/transactions#list
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

        return $this->_postProcess($response);
    }


    /**
     * Get a single EvoPay by Id Transaction
     *
     * @param  int $accountId EvoPay Account ID
     * @param  int $transactionId
     * @throws Webservice\Exception
     * @return stdClass
     * @link http://developer.ticketevolution.com/endpoints/transactions#show
     */
    public function showEvoPayTransaction($accountId, $transactionId)
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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Shipping Settings
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/settings#shipping
     */
    public function listSettingsShipping(array $options)
    {
        $endPoint = 'settings/shipping';

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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * List Service Fees Settings
     *
     * @param  array $options Options to use for the search query
     * @throws Webservice\Exception
     * @return TicketEvolution\Webservice\ResultSet
     * @link http://developer.ticketevolution.com/endpoints/settings#service_fees
     */
    public function listSettingsServiceFees(array $options)
    {
        $endPoint = 'settings/service_fees';

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

        $response = $client->restGet($this->_apiPrefix . $endPoint, $options);

        return $this->_postProcess($response);
    }


    /**
     * Returns a reference to the REST client
     *
     * @return \Zend_Rest_Client
     */
    public function getRestClient()
    {
        if ($this->_rest === null) {
            $this->_rest = new \Zend_Rest_Client();

            $httpClient = new \Zend_Http_Client(
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
             * @see \Zend_Http_Client_Adapter_Socket
             */
            $adapter = new \Zend_Http_Client_Adapter_Socket();

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
     * @param \Zend_Rest_Client
     * @return TicketEvolution\Webservice
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
            'User-Agent' => 'TicketEvolution\Webservice',
            'X-Token'   => (string)$apiToken,
            'Accept'    => (string)'application/json',
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
                $this->_baseUri . $this->_apiPrefix,
                $this->_secretKey,
                $action,
                $endPoint,
                $options
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

        return base64_encode(
            \Zend_Crypt_Hmac::compute($secretKey, 'sha256', $signature, \Zend_Crypt_Hmac::BINARY)
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
        $signature = $action . ' ' . preg_replace('/https:\/\//', '', $baseUri) . $endPoint . '?';
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
                $signature .= implode('&', $params);
            } else {
                $signature .= (string) $options;
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
    protected function _postProcess($response)
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
            throw new namespace\Exception(
                'An error occurred sending request. Status code: '
                . $response->getStatus()
            );
        }


        switch ($this->resultType) {
            case 'json':
                return $response->getBody();
                break;

            case 'decodedjson':
                $decodedJson = self::json_decode($response->getBody());
                return $decodedJson;
                break;

            case 'resultset':
            default:
                $decodedJson = self::json_decode($response->getBody());

                // There is a single item, so no need to return a ResultSet
                if (!isset($decodedJson->total_entries)) {
                    return $decodedJson;
                }

                return new Webservice\ResultSet($decodedJson);
        }

        return false;
    }


    /**
     * Utility method used to catch problems decoding the JSON.
     *
     * @param string $string
     * @return mixed
     * @link http://php.net/manual/en/function.json-decode.php
     */
    public static function json_decode($string)
    {
        $decodedJson = json_decode($string);

        if (is_null($decodedJson)) {
            throw new namespace\Exception(
                'An error occurred decoding the JSON received: ' . json_last_error()
            );
        }

        return $decodedJson;
    }


}
