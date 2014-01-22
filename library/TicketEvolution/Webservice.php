<?php

/**
 * Ticket Evolution PHP Client Library
 *
 * LICENSE
 *
 * This source file is subject to the new BSD (3-Clause) License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://choosealicense.com/licenses/bsd-3-clause/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@ticketevolution.com so we can send you a copy immediately.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2013 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */


namespace TicketEvolution;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice
 * @copyright   Copyright (c) 2013 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 * @link        http://developer.ticketevolution.com/
 */
class Webservice
{
    /**
     * Ticket Evolution PHP Client Version
     *
     * @var     string
     * @link    https://github.com/ticketevolution/ticketevolution-php/releases
     */
    const VERSION = '2.0.7';

    /**
     * Ticket Evolution API Token
     *
     * @var     string
     * @link    http://exchange.ticketevolution.com/brokerage/credentials
     */
    public $apiToken;

    /**
     * Ticket Evolution API Secret Key
     *
     * @var     string
     * @link    http://exchange.ticketevolution.com/brokerage/credentials
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
     * @var     string
     * @link    http://api.ticketevolution.com/ Find the current version
     */
    protected $_apiVersion = '9';


    /**
     * Reference to REST client object
     *
     * @var     Zend_Rest_Client
     */
    protected $_rest = null;


    /**
     * Whether or not to use persistent connections.
     *
     * @var     bool
     */
    protected $_usePersistentConnections = true;


    /**
     * Defines how the data is returned.
     * * resultset   = Default. An iterable TicketEvolution\Webservice\Resultset object
     * * json        = The JSON received with no conversion
     * * decodedjson = First performs a decode_json()
     *
     * @var     string  [resultset,json,decodedjson]
     */
    public $resultType = 'resultset';


    /**
     * Constructs a new Ticket Evolution Web Services Client
     *
     * @param   mixed   $config     An array or Zend_Config object with adapter parameters.
     * @return  TicketEvolution\Webservice
     */
    public function __construct($config)
    {
        if ($config instanceof \Zend_Config) {
            $config = $config->toArray();
        }

        // Verify that parameters are in an array.
        if (!is_array($config)) {
            throw new ApiException(
                'Parameters must be in an array or a Zend_Config object'
            );
        }

        // Verify that an API token has been specified.
        if (!is_string($config['apiToken']) || empty($config['apiToken'])) {
            throw new ApiException(
                'API token must be specified in a string. '
                . '(HINT: You can generate API credentials from the TicketEvolution Settings interface. See https://settings.ticketevolution.com/brokerage/credentials for details, or email support@ticketevolution.com if you have any questions.'
            );
        }

        // Verify that an API secret key has been specified.
        if (!is_string($config['secretKey']) || empty($config['secretKey'])) {
            throw new ApiException(
                'Secret key must be specified in a string. '
                . '(HINT: You can generate API credentials from the TicketEvolution Settings interface. See https://settings.ticketevolution.com/brokerage/credentials for details, or email support@ticketevolution.com if you have any questions.'
            );
        }

        // See if we need to override the API version.
        if (!empty($config['apiVersion'])) {
            $this->_apiVersion = (string) $config['apiVersion'];
        }

        // See if we need to override the base URI.
        if (!empty($config['baseUri'])) {
            $this->_baseUri = (string) $config['baseUri'];
        }

        // See if we need to override the _usePersistentConnections.
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
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/brokerages#list
     */
    public function listBrokerages(array $options)
    {
        $endPoint = 'brokerages';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single brokerage by Id
     *
     * @param   int     $id     The brokerage ID
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/brokerages#show
     */
    public function showBrokerage($id)
    {
        $endPoint = 'brokerages/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search for brokerage(s)
     *
     * @param   string   $query      The query string
     * @param   array    $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/brokerages#search
     */
    public function searchBrokerages($query, array $options)
    {
        $endPoint = 'brokerages/search';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Clients
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/clients#list
     */
    public function listClients(array $options)
    {
        $endPoint = 'clients';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client by Id
     *
     * @param   int     $id     The Client ID
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/clients#show
     */
    public function showClient($id)
    {
        $endPoint = 'clients/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search for client(s)
     *
     * @param   string  $query      The query string
     * @param   array   $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/clients#search
     */
    public function searchClients($query, array $options)
    {
        $endPoint = 'clients/search';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create client(s)
     *
     * @param   array   $clients    Array of client objects
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/clients#create
     */
    public function createClients(array $clients)
    {
        $endPoint = 'clients';

        $body = new \stdClass();
        $body->clients = $clients;
        $options = json_encode($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Update a client
     *
     * @param   int     $id             The client ID to update
     * @param   object  $clientDetails   Client object structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/clients#update
     */
    public function updateClient($id, $clientDetails)
    {
        $endPoint = 'clients/' . $id;

        $options = json_encode($clientDetails);

        return $this->_postProcess(
            $this->_put($endPoint, $options)
        );
    }


    /**
     * List Client Companies
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/companies#list
     */
    public function listClientCompanies(array $options)
    {
        $endPoint = 'companies';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client company by Id
     *
     * @param   int     $id     The Client Company ID
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/companies#show
     */
    public function showClientCompany($id)
    {
        $endPoint = 'companies/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create client companies
     *
     * @param   array   $companies  Array of objects structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/companies#create
     */
    public function createClientCompanies(array $companies)
    {
        $endPoint = 'companies';

        $body = new \stdClass();
        $body->companies = $companies;
        $options = json_encode($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Update a client company
     *
     * @param   int     $id             The client ID to update
     * @param   object  $companyDetails  Company object structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/companies#update
     */
    public function updateClientCompany($id, $companyDetails)
    {
        $endPoint = 'companies/' . $id;

        $options = json_encode($companyDetails);

        return $this->_postProcess(
            $this->_put($endPoint, $options)
        );
    }


    /**
     * List Client Addresses
     *
     * @param   int     $clientId   ID of the specific client
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/addresses#list
     */
    public function listClientAddresses($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/addresses';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client address by Id
     *
     * @param   int     $clientId   ID of the specific client
     * @param   int     $addressId  ID of the specific address
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/addresses#show
     */
    public function showClientAddress($clientId, $addressId)
    {
        $endPoint = 'clients/' . $clientId . '/addresses/' . $addressId;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create client address(es)
     *
     * @param   int     $clientId   ID of the specific client
     * @param   array   $addresses  Array of address objects structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/addresses#create
     */
    public function createClientAddresses($clientId, array $addresses)
    {
        $endPoint = 'clients/' . $clientId . '/addresses';

        $body = new \stdClass();
        $body->addresses = $addresses;
        $options = json_encode($body);
        unset($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Update a single client address
     *
     * @param   int     $clientId   ID of the specific client
     * @param   int     $addressId  ID of the specific address
     * @param   object  $address    Address object structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/addresses#update
     */
    public function updateClientAddress($clientId, $addressId, $address)
    {
        $endPoint = 'clients/' . $clientId . '/addresses/' . $addressId;

        $options = json_encode($address);

        return $this->_postProcess(
            $this->_put($endPoint, $options)
        );
    }


    /**
     * List Client Phone Numbers
     *
     * @param   int     $clientId   ID of the specific client
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/phone-numbers#list
     */
    public function listClientPhoneNumbers($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/phone_numbers';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client phone number by Id
     *
     * @param   int     $clientId       ID of the specific client
     * @param   int     $phoneNumberId  ID of the specific phone number
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/phone-numbers#show
     */
    public function showClientPhoneNumber($clientId, $phoneNumberId)
    {
        $endPoint = 'clients/' . $clientId . '/phone_numbers/' . $phoneNumberId;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create client phone number(s)
     *
     * @param   int     $clientId       ID of the specific client
     * @param   array   $phoneNumbers   Array of phone numbers objects per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/phone-numbers#create
     */
    public function createClientPhoneNumbers($clientId, array $phoneNumbers)
    {
        $endPoint = 'clients/' . $clientId . '/phone_numbers';

        $body = new \stdClass();
        $body->phone_numbers = $phoneNumbers;
        $options = json_encode($body);
        unset($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Update a single client phone number
     *
     * @param   int     $clientId           ID of the specific client
     * @param   int     $phoneNumberId      ID of the specific phone number
     * @param   object  $phoneNumberDetails Phone number object structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/phone-numbers#update
     */
    public function updateClientPhoneNumber($clientId, $phoneNumberId, $phoneNumberDetails)
    {
        $endPoint = 'clients/' . $clientId . '/phone_numbers/' . $phoneNumberId;

        $options = json_encode($phoneNumberDetails);

        return $this->_postProcess(
            $this->_put($endPoint, $options)
        );
    }


    /**
     * List Client Email Addresses
     *
     * @param   int     $clientId   ID of the specific client
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/email-addresses#list
     */
    public function listClientEmailAddresses($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/email_addresses';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client email address by Id
     *
     * @param   int     $clientId       ID of the specific client
     * @param   int     $emailAddressId ID of the specific email address
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/email-addresses#show
     */
    public function showClientEmailAddress($clientId, $emailAddressId)
    {
        $endPoint = 'clients/' . $clientId . '/email_addresses/' . $emailAddressId;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create client email address(es)
     *
     * @param   int     $clientId       ID of the specific client
     * @param   array   $emailAddresses Array of email address objects structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/email-addresses#create
     */
    public function createClientEmailAddresses($clientId, array $emailAddresses)
    {
        $endPoint = 'clients/' . $clientId . '/email_addresses';

        $body = new \stdClass();
        $body->email_addresses = $emailAddresses;
        $options = json_encode($body);
        unset($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Update a single client email address
     *
     * @param   int     $clientId               ID of the specific client
     * @param   int     $emailAddressId         ID of the specific email address
     * @param   object  $emailAddressDetails    Client object structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/email-addresses#update
     */
    public function updateClientEmailAddress($clientId, $emailAddressId, $emailAddressDetails)
    {
        $endPoint = 'clients/' . $clientId . '/email_addresses/' . $emailAddressId;

        $options = json_encode($emailAddressDetails);

        return $this->_postProcess(
            $this->_put($endPoint, $options)
        );
    }


    /**
     * List Client credit cards
     *
     * @param   int     $clientId   ID of the specific client
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/credit-cards#list
     */
    public function listClientCreditCards($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/credit_cards';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * THIS ENDPOINT DOES NOT YET EXIST!
     *
     * Get a single client credit card by Id
     *
     * NOTE: For PCI compliance, once you create a credit card you can NEVER
     * retrieve the full card number, expiration date or verification code.
     *
     * @param   int     $clientId       ID of the specific client
     * @param   int     $creditCardId   ID of the specific credit card
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/credit-cards#show
     */
    public function showClientCreditCard($clientId, $creditCardId)
    {
        $endPoint = 'clients/' . $clientId . '/credit_cards/' . $creditCardId;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create client credit card(s)
     *
     *  NOTE: Currently the API only supports creating a single card at a time.
     *        If you pass in more than one credit card to POST, it will just
     *        ignore everything after the first one.
     *        This will change in a future release to allow multiples.
     *
     * @param   int     $clientId       ID of the specific client
     * @param   array   $creditCards    Array of credit card objects structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/credit-cards#create
     */
    public function createClientCreditCards($clientId, array $creditCards)
    {
        $endPoint = 'clients/' . $clientId . '/credit_cards';

        $body = new \stdClass();
        foreach ($creditCards as $creditCard) {
            // Strip non-numeric chars from CC number
            $creditCard->number = $this->_cleanCreditCardNumber(
                $creditCard->number
            );
            $body->credit_cards[] = $creditCard;
        }
        $options = json_encode($body);
        unset($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * THIS ENDPOINT DOES NOT YET EXIST!
     *
     * Update a single client credit card
     *
     * @param   int     $clientId           ID of the specific client
     * @param   int     $creditCardId       ID of the specific email address
     * @param   object  $creditCardDetails  Client object structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/credit-cards#update
     */
    public function updateClientCreditCard($clientId, $creditCardId, $creditCardDetails)
    {
        $endPoint = 'clients/' . $clientId . '/credit_cards/' . $creditCardId;

        // Strip non-numeric chars from CC number
        $creditCardDetails->number = $this->_cleanCreditCardNumber(
            $creditCardDetails->number
        );
        $options = json_encode($creditCardDetails);

        return $this->_postProcess(
            $this->_put($endPoint, $options)
        );
    }


    /**
     * Remove non-numeric characters from credit card number and validate it
     *
     * @param  string   $creditCardNumber
     * @return string
     */
    protected function _cleanCreditCardNumber($creditCardNumber)
    {
        return preg_replace('/[^0-9]/', '', $creditCardNumber);
    }


    /**
     * List Offices for a Brokerage
     *
     * @param   array   $options    Options to use
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/offices#list
     */
    public function listOffices(array $options)
    {
        $endPoint = 'offices';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single office by Id
     *
     * @param   int     $id An Office ID
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/offices#show
     */
    public function showOffice($id)
    {
        $endPoint = 'offices/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search for office(s)
     *
     * @param   string  $query
     * @param   array   $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/offices#search
     */
    public function searchOffices($query, array $options)
    {
        $endPoint = 'offices/search';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Users for a Brokerage Office
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/users#list
     */
    public function listUsers(array $options)
    {
        $endPoint = 'users';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single user by Id
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/users#show
     */
    public function showUser($id)
    {
        $endPoint = 'users/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search for user(s)
     *
     * @param   string  $query
     * @param   array   $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/users#search
     */
    public function searchUsers($query, array $options)
    {
        $endPoint = 'users/search';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Active Categories
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/categories#list
     */
    public function listCategories(array $options)
    {
        $endPoint = 'categories';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Categories that have been deleted
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/categories#deleted
     */
    public function listCategoriesDeleted(array $options)
    {
        $endPoint = 'categories/deleted';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single category by Id
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/categories#show
     */
    public function showCategory($id)
    {
        $endPoint = 'categories/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Active Events
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/events#list
     */
    public function listEvents(array $options)
    {
        $endPoint = 'events';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Events that have been deleted
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/events#deleted
     */
    public function listEventsDeleted(array $options)
    {
        $endPoint = 'events/deleted';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single event by Id
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/events#show
     */
    public function showEvent($id)
    {
        $endPoint = 'events/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search for events
     *
     * @param   string  $query
     * @param   array   $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470096
     */
    public function searchEvents($query, array $options)
    {
        $endPoint = 'events/search';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Active Performers
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/performers#list
     */
    public function listPerformers(array $options)
    {
        $endPoint = 'performers';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Performers that have been deleted
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/performers#deleted
     */
    public function listPerformersDeleted(array $options)
    {
        $endPoint = 'performers/deleted';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single Performer by Id
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/performers#show
     */
    public function showPerformer($id)
    {
        $endPoint = 'performers/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search for performer(s)
     *
     * @param   string  $query
     * @param   array   $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/performers#search
     */
    public function searchPerformers($query, array $options)
    {
        $endPoint = 'performers/search';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search
     * Currently searches both performers and venues for a match and will return
     * any combination of such. The type will be denoted in the results.
     *
     * @param   string  $query      Query string
     * @param   array   $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/search#list
     */
    public function search($query, array $options)
    {
        $endPoint = 'search';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Active Venues
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/venues#list
     */
    public function listVenues(array $options)
    {
        $endPoint = 'venues';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Venues that have been deleted
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/venues#deleted
     */
    public function listVenuesDeleted(array $options)
    {
        $endPoint = 'venues/deleted';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single Venue by Id
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/venues#show
     */
    public function showVenue($id)
    {
        $endPoint = 'venues/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search for venue(s)
     *
     * @param   string  $query
     * @param   array   $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/venues#search
     */
    public function searchVenues($query, array $options)
    {
        $endPoint = 'venues/search';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Configurations
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/configurations#list
     */
    public function listConfigurations(array $options)
    {
        $endPoint = 'configurations';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single Configuration by Id
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/configurations#show
     */
    public function showConfiguration($id)
    {
        $endPoint = 'configurations/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Ticket Groups
     *
     * @param   array   $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/ticket-groups#list
     */
    public function listTicketGroups(array $options)
    {
        $endPoint = 'ticket_groups';

        if (!isset($options['event_id'])) {
            throw new ApiException(
                'You must supply an "event_id" when listing ticket groups.'
            );
        }

        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single Ticket by Id Group
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/ticket-groups#show
     */
    public function showTicketGroup($id)
    {
        $endPoint = 'ticket_groups/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Orders
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/orders#list
     */
    public function listOrders(array $options)
    {
        $endPoint = 'orders';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single order by Id
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/orders#show
     */
    public function showOrder($id)
    {
        $endPoint = 'orders/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create order(s)
     *
     * @param   array   $orders     Array of order objects as defined by API
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/orders#create_client_order
     */
    public function createOrders(array $orders)
    {
        $endPoint = 'orders';

        $body = new \stdClass();
        $body->orders = $orders;
        $options = json_encode($body);
        unset($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Create order(s) from raw JSON
     *
     * @param   array   $options     JSON of the order details
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/orders#create_client_order
     */
    public function createOrdersFromJson($options)
    {
        $endPoint = 'orders';

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Update an order
     *
     * @param   int     $orderId        ID of the specific order
     * @param   object  $orderDetails   Order object structured per API example
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/orders#update
     */
    public function updateOrder($orderId, $orderDetails)
    {
        $endPoint = 'orders/' . $orderId;

        $options = json_encode($orderDetails);

        return $this->_postProcess(
            $this->_put($endPoint, $options)
        );
    }


    /**
     * Accept an order
     *
     * @param   int     $orderId    ID of the order to accept
     * @param   int     $userId     ID of the user who reviewed and accepts this order
     * @return  bool
     * @link    http://developer.ticketevolution.com/endpoints/orders#accept_order
     */
    public function acceptOrder($orderId, $userId)
    {
        $endPoint = 'orders/' . $orderId . '/accept';

        $options = json_encode(array('reviewer_id' => $userId));

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );

        $response = $this->_post($endPoint, $options);

        if ($response->isSuccessful()) {
            return true;
        }
    }


    /**
     * Reject an order
     *
     * @param   int     $orderId    ID of the order to accept
     * @param   int     $userId     ID of the user who reviewed and rejects this order
     * @throws  OutOfBoundsException
     * @return  bool
     * @link    http://developer.ticketevolution.com/endpoints/orders#reject_order
     */
    public function rejectOrder($orderId, $userId, $reason)
    {
        $endPoint = 'orders/' . $orderId . '/reject';

        $allowedReasons = array(
            'Tickets No Longer Available',
            'Tickets Priced Incorrectly',
            'Duplicate Order',
            'Fraudulent Order',
            'Test Order',
            'Other',
        );
        if (!in_array($reason, $allowedReasons)) {
            throw new \OutOfBoundsException(
                'The rejection reason you provided is not allowed. '
                . 'Rejection reason must be one of: ' . implode(', ', $allowedReasons)
            );
        }
        unset($allowedreasons);

        $body = array(
            'reviewer_id'       => $userId,
            'rejection_reason'  => $reason,
        );
        $options = json_encode($body);
        unset($body);

        $response = $this->_post($endPoint, $options);

        if ($response->isSuccessful()) {
            return true;
        }
    }


    /**
     * Complete an order
     *
     * @param   int     $orderId    ID of the order to accept
     * @param   int     $userId     ID of the user who reviewed and rejects this order
     * @return  bool
     * @link    http://developer.ticketevolution.com/endpoints/orders#complete_order
     */
    public function completeOrder($orderId)
    {
        $endPoint = 'orders/' . $orderId . '/complete';

        $options = array();

        $response = $this->_post($endPoint, $options);

        if ($response->isSuccessful()) {
            return true;
        }
    }


    /**
     * List Shipments
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/shipments#list
     */
    public function listShipments(array $options)
    {
        $endPoint = 'shipments';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single shipment by Id
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/shipments#show
     */
    public function showShipment($id)
    {
        $endPoint = 'shipments/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create shipment(s)
     *
     * @param   array   $shipments  Array of shipment objects
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/shipments#create
     */
    public function createShipments(array $shipments)
    {
        $endPoint = 'shipments';

        $body = new \stdClass();
        $body->shipments = $shipments;
        $options = json_encode($body);
        unset($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Update a single shipment
     *
     * @param   object  $shipment
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/shipments#update
     */
    public function updateShipment($shipment)
    {
        $endPoint = 'shipments';

        $options = json_encode($shipment);

        return $this->_postProcess(
            $this->_put($endPoint, $options)
        );
    }


    /**
     * List Quotes
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/quotes#list
     */
    public function listQuotes(array $options)
    {
        $endPoint = 'quotes';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single quote by Id
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/quotes#show
     */
    public function showQuote($id)
    {
        $endPoint = 'quotes/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search for quote(s)
     *
     * @param   string  $query
     * @param   array   $options    Options to use for the search query
     * @throws  TicketEvolution\ApiException
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/quotes#search
     */
    public function searchQuotes($query, array $options)
    {
        $endPoint = 'quotes/search';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List EvoPay Accounts
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/accounts#list
     */
    public function listEvoPayAccounts(array $options)
    {
        $endPoint = 'accounts';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single EvoPay by Account ID
     *
     * @param   int     $id
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/accounts#show
     */
    public function showEvoPayAccount($id)
    {
        $endPoint = 'accounts/' . $id;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List EvoPay Transactions
     *
     * @param   int     $accountId  EvoPay Account ID
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/transactions#list
     */
    public function listEvoPayTransactions($accountId, array $options)
    {
        $endPoint = 'accounts/' . $accountId . '/transactions';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single EvoPay by Id Transaction
     *
     * @param   int     $accountId      EvoPay Account ID
     * @param   int     $transactionId  An EvoPay TransactionID
     * @return  stdClass
     * @link    http://developer.ticketevolution.com/endpoints/transactions#show
     */
    public function showEvoPayTransaction($accountId, $transactionId)
    {
        $endPoint = 'accounts/' . $accountId . '/transactions/' . $transactionId;

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Shipping Settings
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/settings#shipping
     */
    public function listSettingsShipping(array $options)
    {
        $endPoint = 'settings/shipping';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Service Fees Settings
     *
     * @param   array   $options    Options to use for the search query
     * @return  TicketEvolution\Webservice\ResultSet
     * @link    http://developer.ticketevolution.com/endpoints/settings#service_fees
     */
    public function listSettingsServiceFees(array $options)
    {
        $endPoint = 'settings/service_fees';

        $defaultOptions = array(
            'page'      => '1',
            'per_page'  => '100'
        );

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Returns a reference to the REST client
     *
     * @return  Zend_Rest_Client
     */
    public function getRestClient()
    {
        if ($this->_rest === null) {
            $this->_rest = new \Zend_Rest_Client();

            $httpClient = new \Zend_Http_Client(
                $this->_baseUri,
                array (
                    'keepalive' => $this->_usePersistentConnections
                )
            );


            /**
             * The Ticket Evolution Sandbox uses a self-signed certificate which,
             * by default is not allowed. If we are using https in the sandbox lets
             * tweak the options to allow this self-signed certificate.
             *
             * @link    http://framework.zend.com/manual/en/zend.http.client.adapters.html Example 2
             */
            if (strpos($this->_baseUri, 'sandbox') !== false) {
                $streamOptions = array(
                    // Accept self-signed SSL certificate
                    'ssl' => array(
                        //'verify_peer' => true,
                        'allow_self_signed' => true,
                    )
                );
            } else {
                $streamOptions = array();
            }

            // Create an adapter object and attach it to the HTTP client
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
     * @param   Zend_Rest_Client
     * @return  TicketEvolution\Webservice
     */
    public function setRestClient(\Zend_Rest_Client $client)
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
            'User-Agent'    => __CLASS__ . ' ' . self::VERSION . ' / Zend Framework ' . \Zend_Version::VERSION . ' / PHP ' . phpversion(),
            'Accept'        => 'application/json',
            'X-Token'       => $apiToken,
        );
        unset($ua);

        if (!empty($requestSignature)) {
            $headers['X-Signature'] = $requestSignature;
        }

        $this->_rest->getHttpClient()->setHeaders($headers);
    }


    /**
     * Prepare options for request
     *
     * @param   string $action         Action to perform [GET|POST|PUT|DELETE]
     * @param   array  $endPoint       The endPoint
     * @param   array  $options        User supplied options
     * @param   array  $defaultOptions Default options
     * @return  array
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
     * @param   string  $baseUri
     * @param   string  $secretKey
     * @param   string  $action
     * @param   string  $endPoint
     * @param   array   $options
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
     * Perform a GET request
     *
     * @param   string  $endPoint       The API endpoint
     * @param   array   $options        The specified options
     * @param   array   $defaultOptions Default options that can be overwritten by $options
     * @throws  TicketEvolution\Webservice\ApiConnectionException
     * @return  Zend_Http_Response
     */
    protected function _get($endPoint, $options, $defaultOptions=array())
    {
        $options = $this->_prepareOptions(
            'GET',
            $endPoint,
            $options,
            $defaultOptions
        );

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $client->getHttpClient()->resetParameters();

        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        try {
            return $client->restGet($this->_apiPrefix . $endPoint, $options);
        } catch (\Exception $e) {
            throw new ApiConnectionException(
                $e->getMessage(),
                $e->getCode(),
                $e->__toString()
            );
        }

    }


    /**
     * Perform a POST request
     *
     * @param   string  $endPoint       The API endpoint
     * @param   array   $options        The specified options
     * @param   array   $defaultOptions Default options that can be overwritten by $options
     * @throws  TicketEvolution\Webservice\ApiConnectionException
     * @return  Zend_Http_Response
     */
    protected function _post($endPoint, $options)
    {
        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'POST',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $client->getHttpClient()->resetParameters();

        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        try {
            return $client->restPost($this->_apiPrefix . $endPoint, $options);
        } catch (\Exception $e) {
            throw new ApiConnectionException(
                $e->getMessage(),
                $e->getCode(),
                $e->__toString()
            );
        }

    }


    /**
     * Perform a PUT request
     *
     * @param   string  $endPoint       The API endpoint
     * @param   array   $options        The specified options
     * @param   array   $defaultOptions Default options that can be overwritten by $options
     * @throws  TicketEvolution\Webservice\ApiConnectionException
     * @return  Zend_Http_Response
     */
    protected function _put($endPoint, $options)
    {
        $this->_requestSignature = self::computeSignature(
            $this->_baseUri,
            $this->_secretKey,
            'PUT',
            $this->_apiPrefix . $endPoint,
            $options
        );

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $client->getHttpClient()->resetParameters();

        $this->_setHeaders(
            $this->apiToken,
            $this->_apiVersion,
            $this->_requestSignature
        );

        try {
            return $client->restPut($this->_apiPrefix . $endPoint, $options);
        } catch (\Exception $e) {
            throw new ApiConnectionException(
                $e->getMessage(),
                $e->getCode(),
                $e->__toString()
            );
        }

    }


    /**
     * Allows post-processing logic to be applied.
     * Subclasses may override this method.
     *
     * @param   string  $response   The response to process
     * @throws  TicketEvolution\ApiException
     * @return  string|TicketEvolution\Webservice\ResultSet|stdClass
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


        $responseCode = $response->getStatus();
        $responseBody = $response->getBody();

        switch ($this->resultType) {
            case 'json':
                $decodedBody =& $responseBody;
                break;

            case 'decodedjson':
            case 'resultset':
            default:
                try {
                    $decodedBody = json_decode($responseBody);
                } catch (\Exception $e) {
                    throw new ApiException(
                        'Invalid response body from API: ' . $responseBody . ' (HTTP response code was ' . $responseCode . ')',
                        $responseCode,
                        $responseBody
                    );
                }
                break;

        }


        if ($responseCode < 200 || $responseCode >= 300) {
            $this->_handleApiError($responseBody, $responseCode, $decodedBody);
        }


        if ($this->resultType === 'resultset') {
            if (!isset($decodedBody->total_entries)) {
                // There is a single item, so no need to return a ResultSet
                return $decodedBody;
            }

            return new Webservice\ResultSet($decodedBody);
        } else {
            return $decodedBody;
        }

    }


    /**
     * Handle errors in the response.
     * Subclasses may override this method.
     *
     * @param   string  $responseBody   The response body
     * @param   int     $responseCode   The HTTP status code of the response
     * @param   string  $decodedBody    json_decode()d response body
     * @throws  TicketEvolution\ApiInvalidRequestException|TicketEvolution\ApiAuthenticationException|TicketEvolution\ApiException
     * @return  void
     */
    protected function _handleApiError($responseBody, $responseCode, $decodedBody)
    {
        switch ($responseCode) {
            case 400:
            case 404:
                throw new ApiInvalidRequestException(
                    isset($decodedBody->error) ? $decodedBody->error : null,
                    $responseCode,
                    $responseBody,
                    $decodedBody
                );
                break;

            case 401:
                throw new ApiAuthenticationException(
                    isset($decodedBody->error) ? $decodedBody->error : null,
                    $responseCode,
                    $responseBody,
                    $decodedBody
                );
                break;

            case 402:
            default:
                throw new ApiException(
                    isset($decodedBody->error) ? $decodedBody->error : null,
                    $responseCode,
                    $responseBody,
                    $decodedBody
                );
        }
    }


    /**
     * Utility method used to catch problems decoding the JSON.
     *
     * @param   string $string
     * @throws  TicketEvolution\ApiException
     * @return  string
     * @link    http://php.net/manual/en/function.json-decode.php
     */
    public static function json_decode($string)
    {
        try {
            $decodedJson = json_decode($string);
        } catch (\Exception $e) {
            throw new ApiException(
                'Invalid response body from API: ' . $responseBody . ' (HTTP response code was ' . $responseCode . ')',
                $responseCode,
                $responseBody
            );
        }

        return $decodedJson;
    }


}
