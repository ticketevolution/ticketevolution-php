<?php namespace TicketEvolution;

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
     * @copyright   Copyright (c) 2014 Ticket Evolution, Inc. (http://www.ticketevolution.com)
     * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
     */


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice
 * @copyright   Copyright (c) 2014 Ticket Evolution, Inc. (http://www.ticketevolution.com)
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
    const VERSION = '2.2.1';

    /**
     * Ticket Evolution API Token
     *
     * @var     string
     * @link    http://settings.ticketevolution.com/brokerage/credentials
     */
    public $apiToken;

    /**
     * Ticket Evolution API Secret Key
     *
     * @var     string
     * @link    http://settings.ticketevolution.com/brokerage/credentials
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
     * @link    https://ticketevolution.atlassian.net/wiki/display/API/Current+Version Find the current version
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
     * Variable for measuring API call execution time.
     *
     * @var     float
     */
    protected $_timeStart;


    /**
     * Variable for measuring API call execution time.
     *
     * @var     float
     */
    protected $_timeEnd;


    /**
     * Default parameters for endpoints that support pagination.
     *
     * @var     float
     */
    protected $_defaultPagination = array(
        'page'     => '1',
        'per_page' => '100'
    );


    /**
     * Constructs a new Ticket Evolution Web Services Client
     *
     * @param   mixed $config An array or Zend_Config object with adapter parameters.
     *
     * @throws ApiException
     * @return  Webservice
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
            $this->_apiVersion = (string)$config['apiVersion'];
        }

        // See if we need to override the base URI.
        if (!empty($config['baseUri'])) {
            $this->_baseUri = (string)$config['baseUri'];
        }

        // See if we need to override the _usePersistentConnections.
        if (isset($config['usePersistentConnections'])) {
            $this->_usePersistentConnections = (bool)$config['usePersistentConnections'];
        }

        $this->apiToken = (string)$config['apiToken'];
        $this->_secretKey = (string)$config['secretKey'];

        $this->_apiPrefix = '/v' . $this->_apiVersion . '/';
    }


    /**
     * List Brokerages
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=25001994
     */
    public function listBrokerages(array $options)
    {
        $endPoint = 'brokerages';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single brokerage by Id
     *
     * @param   int $id The brokerage ID
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=25002003
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
     * @param   string $query   The query string
     * @param   array  $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=25002001
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

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Clients
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470168
     */
    public function listClients(array $options)
    {
        $endPoint = 'clients';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client by Id
     *
     * @param   int $id The Client ID
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470174
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
     * @param   string $query   The query string
     * @param   array  $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=6455318
     */
    public function searchClients($query, array $options)
    {
        $endPoint = 'searches/suggestions';

        // Because the results from this endpoint do not convert to a ResultSet
        // force a return of decodedjson
        if ($this->resultType == 'resultset') {
            $this->resultType = 'decodedjson';
        }

        //Set the proper entity to search
        $options['entities'] = 'clients';

        $options['q'] = trim($query);
        if (empty ($options['q'])) {
            throw new ApiException(
                'You must provide a non-empty query string when searching.'
            );
        }

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create client(s)
     *
     * @param   array $clients Array of client objects
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470183
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
     * @param   int    $id            The client ID to update
     * @param   object $clientDetails Client object structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470186
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
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=30572746
     */
    public function listClientCompanies(array $options)
    {
        $endPoint = 'companies';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client company by Id
     *
     * @param   int $id The Client Company ID
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=30572749
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
     * @param   array $companies Array of objects structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=30572753
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
     * @param   int    $id             The client ID to update
     * @param   object $companyDetails Company object structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=30572755
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
     * @param   int   $clientId ID of the specific client
     * @param   array $options  Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129319
     */
    public function listClientAddresses($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/addresses';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client address by Id
     *
     * @param   int $clientId  ID of the specific client
     * @param   int $addressId ID of the specific address
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129333
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
     * @param   int   $clientId  ID of the specific client
     * @param   array $addresses Array of address objects structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129337
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
     * @param   int    $clientId  ID of the specific client
     * @param   int    $addressId ID of the specific address
     * @param   object $address   Address object structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129342
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
     * @param   int   $clientId ID of the specific client
     * @param   array $options  Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394131
     */
    public function listClientPhoneNumbers($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/phone_numbers';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client phone number by Id
     *
     * @param   int $clientId      ID of the specific client
     * @param   int $phoneNumberId ID of the specific phone number
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394167
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
     * @param   int   $clientId     ID of the specific client
     * @param   array $phoneNumbers Array of phone numbers objects per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=983142
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
     * @param   int    $clientId           ID of the specific client
     * @param   int    $phoneNumberId      ID of the specific phone number
     * @param   object $phoneNumberDetails Phone number object structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394170
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
     * @param   int   $clientId ID of the specific client
     * @param   array $options  Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394119
     */
    public function listClientEmailAddresses($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/email_addresses';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single client email address by Id
     *
     * @param   int $clientId       ID of the specific client
     * @param   int $emailAddressId ID of the specific email address
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394122
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
     * @param   int   $clientId       ID of the specific client
     * @param   array $emailAddresses Array of email address objects structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=983146
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
     * @param   int    $clientId            ID of the specific client
     * @param   int    $emailAddressId      ID of the specific email address
     * @param   object $emailAddressDetails Client object structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394200
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
     * @param   int   $clientId ID of the specific client
     * @param   array $options  Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129301
     */
    public function listClientCreditCards($clientId, array $options)
    {
        $endPoint = 'clients/' . $clientId . '/credit_cards';

        $defaultOptions = $this->_defaultPagination;

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
     * @param   int $clientId     ID of the specific client
     * @param   int $creditCardId ID of the specific credit card
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394229
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
     * @param   int   $clientId    ID of the specific client
     * @param   array $creditCards Array of credit card objects structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129312
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
     * @param   int    $clientId          ID of the specific client
     * @param   int    $creditCardId      ID of the specific email address
     * @param   object $creditCardDetails Client object structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=27394225
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
     * @param  string $creditCardNumber
     *
     * @return string
     */
    protected function _cleanCreditCardNumber($creditCardNumber)
    {
        return preg_replace('/[^0-9]/', '', $creditCardNumber);
    }


    /**
     * List Offices for a Brokerage
     *
     * @param   array $options Options to use
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470028
     */
    public function listOffices(array $options)
    {
        $endPoint = 'offices';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single office by Id
     *
     * @param   int $id An Office ID
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470035
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
     * @param   string $query
     * @param   array  $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470037
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

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Users for a Brokerage Office
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470068
     */
    public function listUsers(array $options)
    {
        $endPoint = 'users';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single user by Id
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470081
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
     * @param   string $query
     * @param   array  $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470083
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

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Active Categories
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470005
     */
    public function listCategories(array $options)
    {
        $endPoint = 'categories';

        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Categories that have been deleted
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=31948905
     */
    public function listCategoriesDeleted(array $options)
    {
        $endPoint = 'categories/deleted';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single category by Id
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470098
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
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470092
     */
    public function listEvents(array $options)
    {
        $endPoint = 'events';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Events that have been deleted
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=31948920
     */
    public function listEventsDeleted(array $options)
    {
        $endPoint = 'events/deleted';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single event by Id
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470094
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
     * @param   string $query
     * @param   array  $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  Webservice\ResultSet
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

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Active Performers
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470084
     */
    public function listPerformers(array $options)
    {
        $endPoint = 'performers';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Performers that have been deleted
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=31948895
     */
    public function listPerformersDeleted(array $options)
    {
        $endPoint = 'performers/deleted';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single Performer by Id
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470086
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
     * @param   string $query
     * @param   array  $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470088
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

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Search
     * Currently searches both performers and venues for a match and will return
     * any combination of such. The type will be denoted in the results.
     *
     * @param   string $query   Query string
     * @param   array  $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/display/API/Search
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

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Active Venues
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470018
     */
    public function listVenues(array $options)
    {
        $endPoint = 'venues';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Venues that have been deleted
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=31948891
     */
    public function listVenuesDeleted(array $options)
    {
        $endPoint = 'venues/deleted';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single Venue by Id
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470020
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
     * @param   string $query
     * @param   array  $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470023
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

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Configurations
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470120
     */
    public function listConfigurations(array $options)
    {
        $endPoint = 'configurations';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single Configuration by Id
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470125
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
     * @param   array $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469962
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
     * @param   int   $id
     * @param   array $options Options to use for the search query
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9469964
     */
    public function showTicketGroup($id, $options = array())
    {
        $endPoint = 'ticket_groups/' . $id;

        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Set a ticket's properties (PDF and/or barcode)
     *
     * @param   int       $ticketId
     * @param   \stdClass $properties
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=28016760
     */
    public function ticketsSetProperties($ticketId, \stdClass $properties)
    {
        $endPoint = 'tickets/' . $ticketId;

        $options = json_encode($properties);
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_put($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Orders
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751395
     */
    public function listOrders(array $options)
    {
        $endPoint = 'orders';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single order by Id
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4129639
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
     * @param   array $orders Array of order objects as defined by API
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994275
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
     * @param   array $options JSON of the order details
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994275
     */
    public function createOrdersFromJson($options)
    {
        $endPoint = 'orders';

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Accept an order
     *
     * @param   int $orderId ID of the order to accept
     * @param   int $userId  ID of the user who reviewed and accepts this order
     *
     * @return  bool
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470105
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
     * @param   int    $orderId ID of the order to accept
     * @param   int    $userId  ID of the user who reviewed and rejects this order
     * @param   string $reason  One of the allowed reasons
     * @param   string $notes   Additional notes if necessary
     *
     * @throws  OutOfBoundsException
     * @return  bool
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470108
     */
    public function rejectOrder($orderId, $userId, $reason, $notes = null)
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
            'reviewer_id'     => $userId,
            'reason'          => $reason,
            'rejection_notes' => $notes,
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
     * @param   int $orderId ID of the order to accept
     *
     * @return  bool
     * @link
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
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994290
     */
    public function listShipments(array $options)
    {
        $endPoint = 'shipments';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single shipment by Id
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994292
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
     * @param   array $shipments Array of shipment objects
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994308
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
     * Cancel a shipment
     *
     * @param   int $shipmentId
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994324
     */
    public function cancelShipment($shipmentId)
    {
        $endPoint = 'shipments/' . $shipmentId . '/cancel';

        $options = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options)
        );
    }


    /**
     * Generate the airbill for a given shipment
     *
     * @param   int $shipmentId
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994306
     */
    public function generateAirbill($shipmentId)
    {
        $endPoint = 'shipments/' . $shipmentId . '/airbill';

        $options = array();

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Retrieve an already-generated airbill for a given shipment
     *
     * @param   int $shipmentId
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994303
     */
    public function getAirbill($shipmentId)
    {
        $endPoint = 'shipments/' . $shipmentId . '/get_airbill';

        $options = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options)
        );
    }


    /**
     * Retrieve an already-generated airbill for a given shipment
     *
     * @param   int   $shipmentId
     * @param   array $recipients An array of valid email addresses
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994303
     */
    public function emailAirbill($shipmentId, $recipients)
    {
        $endPoint = 'shipments/' . $shipmentId . '/email_airbill';

        $body = new \stdClass();
        $body->recipients = $recipients;
        $options = json_encode($body);
        unset($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Get shipment suggestions
     *
     * @param   int $ticketGroupId
     *
     * @return  \stdClass
     * @link https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=24674319
     */
    public function getShipmentSugestion($ticketGroupId, $address)
    {
        $endPoint = 'shipments/suggestion';

        $body = array(
            'ticket_group_id' => (int)$ticketGroupId,
        );
        if (is_numeric($address)) {
            $body['address_id'] = (int)$address;
        } else {
            $body['address_attributes'] = $address;
        }
        $options = json_encode($body);
        unset($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * Orders / Print Etickets & Items / Print Etickets
     *
     * @param   int      $orderId
     * @param   int|null $itemId
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470115
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=12550151
     */
    public function printEtickets($orderId, $itemId = null)
    {
        $endPoint = 'orders/' . $orderId . '/print_etickets';
        if ($itemId) {
            $endPoint = 'orders/' . $orderId . '/items/' . $itemId . 'print_etickets';
        }

        $options = array();
        $defaultOptions = array();

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Quotes
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=5341218
     */
    public function listQuotes(array $options)
    {
        $endPoint = 'quotes';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single quote by Id
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=5341220
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
     * @param   string $query
     * @param   array  $options Options to use for the search query
     *
     * @throws  ApiException
     * @return  Webservice\ResultSet
     * @link
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

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Create one or more EvoQuote(s)
     *
     * @param   array $quotes Array of objects structured per API example
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=5341222
     */
    public function createQuotes(array $quotes)
    {
        $endPoint = 'companies';

        $body = new \stdClass();
        $body->companies = $quotes;
        $options = json_encode($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * List EvoPay Accounts
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751480
     */
    public function listEvoPayAccounts(array $options)
    {
        $endPoint = 'accounts';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single EvoPay by Account ID
     *
     * @param   int $id
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751482
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
     * @param   int   $accountId EvoPay Account ID
     * @param   array $options   Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751489
     */
    public function listEvoPayTransactions($accountId, array $options)
    {
        $endPoint = 'accounts/' . $accountId . '/transactions';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * Get a single EvoPay by Id Transaction
     *
     * @param   int $accountId     EvoPay Account ID
     * @param   int $transactionId An EvoPay TransactionID
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751495
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
     * Create one or more EvoPay transaction
     *
     * @param   int   $accountId    EvoPay Account ID
     * @param   array $transactions Transactions
     *
     * @return  \stdClass
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=4751497
     */
    public function createEvoPayTransactions($accountId, array $transactions)
    {
        $endPoint = 'accounts/' . $accountId . '/transactions';

        $body = new \stdClass();
        $body->transactions = $transactions;
        $options = json_encode($body);
        unset($body);

        return $this->_postProcess(
            $this->_post($endPoint, $options)
        );
    }


    /**
     * List Shipping Settings
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470044
     */
    public function listSettingsShipping(array $options)
    {
        $endPoint = 'settings/shipping';

        $defaultOptions = $this->_defaultPagination;

        return $this->_postProcess(
            $this->_get($endPoint, $options, $defaultOptions)
        );
    }


    /**
     * List Service Fees Settings
     *
     * @param   array $options Options to use for the search query
     *
     * @return  Webservice\ResultSet
     * @link    https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=22347806
     */
    public function listSettingsServiceFees(array $options)
    {
        $endPoint = 'settings/service_fees';

        $defaultOptions = $this->_defaultPagination;

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
            $this->_rest = new RestClient();

            $httpClient = new HttpClient(
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

            $adapterConfig = array(
                'persistent' => $this->_usePersistentConnections,
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
     *
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
     * @param  string $apiToken
     * @param  string $apiVersion
     * @param  string $requestSignature
     *
     * @return void
     */
    protected function _setHeaders($apiToken, $apiVersion, $requestSignature = null)
    {
        $headers = array(
            'User-Agent' => __CLASS__ . ' ' . self::VERSION . ' / Zend Framework ' . \Zend_Version::VERSION . ' / PHP ' . phpversion(),
            'Accept'     => 'application/json',
            'X-Token'    => $apiToken,
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
     *
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
     * @param   string $baseUri
     * @param   string $secretKey
     * @param   string $action
     * @param   string $endPoint
     * @param   array  $options
     *
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
     * @param  string $action One of [GET|POST|PUT|DELETE]
     * @param  string $endPoint
     * @param  array  $options
     *
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
                $signature .= (string)$options;
            }
        }

        return $signature;
    }


    /**
     * Perform a GET request
     *
     * @param   string $endPoint       The API endpoint
     * @param   array  $options        The specified options
     * @param   array  $defaultOptions Default options that can be overwritten by $options
     *
     * @throws  TicketEvolution\Webservice\ApiConnectionException
     * @return  Zend_Http_Response
     */
    protected function _get($endPoint, $options = array(), $defaultOptions = array())
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
            $this->_startTimer();
            $response = $client->restGet($this->_apiPrefix . $endPoint, $options);
            $this->_endTimer();

            return $response;
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
     * @param   string $endPoint       The API endpoint
     * @param   array  $options        The specified options
     * @param   array  $defaultOptions Default options that can be overwritten by $options
     *
     * @throws  TicketEvolution\Webservice\ApiConnectionException
     * @return  Zend_Http_Response
     */
    protected function _post($endPoint, $options = array())
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
            $this->_startTimer();
            $response = $client->restPost($this->_apiPrefix . $endPoint, $options);
            $this->_endTimer();

            return $response;
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
     * @param   string $endPoint       The API endpoint
     * @param   array  $options        The specified options
     * @param   array  $defaultOptions Default options that can be overwritten by $options
     *
     * @throws  TicketEvolution\Webservice\ApiConnectionException
     * @return  Zend_Http_Response
     */
    protected function _put($endPoint, $options = array())
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
            $this->_startTimer();
            $response = $client->restPut($this->_apiPrefix . $endPoint, $options);
            $this->_endTimer();

            return $response;
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
     * @param   string $response The response to process
     *
     * @throws  ApiException
     * @return  string|Webservice\ResultSet|stdClass
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
         * echo PHP_EOL;
         * var_dump($this->getRestClient()->getHttpClient()->getLastRequest());
         * echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
         * echo PHP_EOL;
         * var_dump($this->getRestClient()->getHttpClient()->getLastResponse());
         * echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
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
            if (isset($decodedBody->per_page) || isset($decodedBody->total_entries)) {
                return new Webservice\ResultSet($decodedBody);
            }

            // There is a single item, so no need to return a ResultSet
            return $decodedBody;

        } else {
            return $decodedBody;
        }

    }


    /**
     * Handle errors in the response.
     * Subclasses may override this method.
     *
     * @param   string $responseBody The response body
     * @param   int    $responseCode The HTTP status code of the response
     * @param   string $decodedBody  json_decode()d response body
     *
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
     *
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


    /**
     * Utility method for timing API calls.
     *
     */
    protected function _startTimer()
    {
        $this->_timeStart = microtime(true);
    }


    /**
     * Utility method for timing API calls.
     *
     */
    protected function _endTimer()
    {
        $this->_timeEnd = microtime(true);
    }


    /**
     * Utility method for timing API calls.
     *
     */
    public function getElapsedTime()
    {
        try {
            return $this->_timeEnd - $this->_timeStart;
        } catch (\Exception $e) {
            return false;
        }
    }


}
