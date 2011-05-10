<?php
/**
 * Ticketevolution Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@teamonetickets.com so we can send you a copy immediately.
 *
 * @category    Ticketevolution
 * @package     Ticketevolution_Webservice
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt     New BSD License
 * @version     $Id: Webservice.php 30 2011-05-09 23:41:55Z jcobb $
 */


/**
 * @category    Ticketevolution
 * @package     Ticketevolution_Webservice
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     http://teamonetickets.com/software/ticket-evolution-framework-for-php/LICENSE.txt     New BSD License
 */
class Ticketevolution_Webservice
{
    /**
     * Amazon Web Services Access Key ID
     *
     * @var string
     */
    public $apiToken;

    /**
     * @var string
     */
    protected $_secretKey = null;

    /**
     * Base URI for the REST client
     * @var string
     */
    protected $_baseUri = 'http://api.ticketevolution.com';

    /**
     * API version
     *
     * @var string
     */
    protected $_apiVersion = '2';


    /**
     * Reference to REST client object
     *
     * @var Zend_Rest_Client
     */
    protected $_rest = null;


    /**
     * Constructs a new Ticket Evolution Web Services Client
     *
     * @param  mixed $config  An array or Zend_Config object with adapter parameters.
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice
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
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('Parameters must be in an array or a Zend_Config object');
        }

        /*
         * Verify that an API token has been specified.
         */
        if (!is_string($config['apiToken']) || empty($config['apiToken'])) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('API token must be specified in a string');
        }

        /*
         * Verify that an API secret key has been specified.
         */
        if (!is_string($config['secretKey']) || empty($config['secretKey'])) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('Secret key must be specified in a string');
        }

        /*
         * See if we need to override the API version.
         */
        if (isset($config['apiVersion']) && !empty($config['apiVersion'])) {
            $this->_apiVersion = (string) $config['apiVersion'];
        }

        /*
         * See if we need to override the base URI.
         */
        if (isset($config['baseUri']) && !empty($config['baseUri'])) {
            $this->_baseUri = (string) $config['baseUri'];
        }

        $this->apiToken = (string) $config['apiToken'];
        $this->_secretKey = (string) $config['secretKey'];
    }


    /**
     * List Brokerages
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listBrokers(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'brokerages', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/brokerages', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single brokerage by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Brokerage
     */
    public function showBroker($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'brokerages/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Brokerage
         */
        require_once 'Ticketevolution/Brokerage.php';
        return new Ticketevolution_Brokerage($response);
    }


    /**
     * Search for a brokerage
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function searchBrokers($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new Ticketevolution_Webservice_Exception('You must provide a non-empty query string');
        }

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'brokerages/search';
        $options['q'] = (string) $query;
        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', $endPoint, $options, $defaultOptions);
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * List Offices for a Brokerage
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listOffices(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'offices', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/offices', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single office by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Office
     */
    public function showOffice($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'offices/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Office
         */
        require_once 'Ticketevolution/Office.php';
        return new Ticketevolution_Office($response);
    }


    /**
     * Search for an office
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function searchOffices($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new Ticketevolution_Webservice_Exception('You must provide a non-empty query string');
        }

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'offices/search';
        $options['q'] = (string) $query;
        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', $endPoint, $options, $defaultOptions);
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * List Users for a Brokerage Office
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listUsers(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'users', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/users', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single user by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_User
     */
    public function showUser($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'users/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_User
         */
        require_once 'Ticketevolution/User.php';
        return new Ticketevolution_User($response);
    }


    /**
     * Search for a user
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function searchUsers($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new Ticketevolution_Webservice_Exception('You must provide a non-empty query string');
        }

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'users/search';
        $options['q'] = (string) $query;
        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', $endPoint, $options, $defaultOptions);
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * List Categories
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listCategories(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'categories', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/categories', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single category by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Category
     */
    public function showCategory($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'categories/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Category
         */
        require_once 'Ticketevolution/Category.php';
        return new Ticketevolution_Category($response);
    }


    /**
     * List Events
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listEvents(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'events', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/events', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single event by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Event
     */
    public function showEvent($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'events/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Event
         */
        require_once 'Ticketevolution/Event.php';
        return new Ticketevolution_Event($response);
    }


    /**
     * List Performers
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listPerformers(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'performers', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/performers', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single Performer by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Performer
     */
    public function showPerformer($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'performers/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Performer
         */
        require_once 'Ticketevolution/Performer.php';
        return new Ticketevolution_Performer($response);
    }


    /**
     * Search for a performer
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function searchPerformers($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new Ticketevolution_Webservice_Exception('You must provide a non-empty query string');
        }

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'performers/search';
        $options['q'] = (string) $query;
        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', $endPoint, $options, $defaultOptions);
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * List Venues
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listVenues(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'venues', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/venues', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single Venue by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Venue
     */
    public function showVenue($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'venues/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Venue
         */
        require_once 'Ticketevolution/Venue.php';
        return new Ticketevolution_Venue($response);
    }


    /**
     * Search for a venue
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function searchVenues($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new Ticketevolution_Webservice_Exception('You must provide a non-empty query string');
        }

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'venues/search';
        $options['q'] = (string) $query;
        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', $endPoint, $options, $defaultOptions);
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * List Configurations
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listConfigurations(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'configurations', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/configurations', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single Configuration by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Venue
     */
    public function showConfiguration($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'configurations/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Configuration
         */
        require_once 'Ticketevolution/Configuration.php';
        return new Ticketevolution_Configuration($response);
    }


    /**
     * List Ticket Groups
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listTicketgroups(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('per_page' => '500');
        $options = self::_prepareOptions('GET', 'ticket-groups', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/ticket-groups', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single Ticket by Id Group
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Ticketgroup
     */
    public function showTicketgroup($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'ticket-groups/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Ticketgroup
         */
        require_once 'Ticketevolution/Ticketgroup.php';
        return new Ticketevolution_Ticketgroup($response);
    }


    /**
     * List Orders
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listOrders(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'orders', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/orders', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single order by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Order
     */
    public function showOrder($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'orders/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Order
         */
        require_once 'Ticketevolution/Order.php';
        return new Ticketevolution_Order($response);
    }


    /**
     * List Quotes
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listQuotes(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'quotes', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/quotes', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single quote by Id
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Quote
     */
    public function showQuote($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'quotes/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Quote
         */
        require_once 'Ticketevolution/Quote.php';
        return new Ticketevolution_Quote($response);
    }


    /**
     * Search for a quote
     *
     * @param  string $query
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function searchQuotes($query, array $options)
    {
        $trimmedQuery = trim($query);
        if (empty ($trimmedQuery)) {
            throw new Ticketevolution_Webservice_Exception('You must provide a non-empty query string');
        }

        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'quotes/search';
        $options['q'] = (string) $query;
        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', $endPoint, $options, $defaultOptions);
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * List EvoPay Accounts
     *
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listEvopayaccounts(array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', 'accounts', $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/accounts', $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single EvoPay by Account ID
     *
     * @param  int $id
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Evopayaccount
     */
    public function showEvopayaccount($id)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'accounts/' . $id;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Evopayaccount
         */
        require_once 'Ticketevolution/Evopayaccount.php';
        return new Ticketevolution_Evopayaccount($response);
    }


    /**
     * List EvoPay Transactions
     *
     * @param  int $accountId EvoPay Account ID
     * @param  array $options Options to use for the search query
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Webservice_ResultSet
     */
    public function listEvopaytransactions($accountId, array $options)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'accounts/' . $accountId . '/transactions';
        $defaultOptions = array('page'  => '1',
                                'per_page' => '100');
        $options = self::_prepareOptions('GET', $endPoint, $options, $defaultOptions);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet($endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Webservice_ResultSet
         */
        require_once 'Ticketevolution/Webservice/ResultSet.php';
        return new Ticketevolution_Webservice_ResultSet($response);
    }


    /**
     * Get a single EvoPay by Id Transaction
     *
     * @param  int $accountId EvoPay Account ID
     * @param  int $transactionId
     * @throws Ticketevolution_Webservice_Exception
     * @return Ticketevolution_Evopaytransaction
     */
    public function showEvopaytransactions($accountId, $transactionId)
    {
        $client = $this->getRestClient();
        $client->setUri($this->_baseUri);

        $endPoint = 'accounts/' . $accountId . '/transactions/' . $transactionId;
        $options = array();
        $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, 'GET', $endPoint, $options);
        $client->getHttpClient()->resetParameters();
        self::_setHeaders($this->apiToken, $this->_apiVersion, $this->_requestSignature);

        $response = $client->restGet('/' . $endPoint, $options);
        if ($response->isError()) {
            /**
             * @see Ticketevolution_Webservice_Exception
             */
            require_once 'Ticketevolution/Webservice/Exception.php';
            throw new Ticketevolution_Webservice_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }
        $response = self::_jsonDecode($response->getBody());

        /**
         * @see Ticketevolution_Evopaytransaction
         */
        require_once 'Ticketevolution/Evopaytransaction.php';
        return new Ticketevolution_Evopaytransaction($response);
    }


    /**
     * Returns a reference to the REST client
     *
     * @return Zend_Rest_Client
     */
    public function getRestClient()
    {
        if($this->_rest === null) {
            /**
             * @see Zend_Rest_Client
             */
            require_once 'Zend/Rest/Client.php';
            $this->_rest = new Zend_Rest_Client();
        }
        return $this->_rest;
    }

    /**
     * Set REST client
     *
     * @param Zend_Rest_Client
     * @return Zend_Service_Amazon
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
        $headers = array('X-Token'  => (string)$apiToken,
                         'Accept'   => (string)'application/vnd.ticketevolution.api+json; version=' . $apiVersion
                        );
        if(!empty($requestSignature)) {
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

        if($this->_secretKey !== null) {
            $this->_requestSignature = self::computeSignature($this->_baseUri, $this->_secretKey, (string)$action, (string)$endPoint, $options);
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
    static public function computeSignature($baseUri, $secretKey, $action, $endPoint, array $options)
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
    static public function buildRawSignature($baseUri, $action, $endPoint, array $options)
    {
        $signature = $action . ' ' . str_replace('http://', '', $baseUri) . '/' . $endPoint . '?';
        if(!empty($options)) {
            // Turn the $options into GET parameters
            ksort($options);
            $params = array();
            foreach($options AS $k => $v) {
                // Oddly, we get 401 Unauthorized if we urlencode or rawurlencode
                $params[] = $k."=".$v;
            }
            $signature .= implode('&', $params);
        }
        return $signature;
    }


    /**
     * Decode the JSON
     * Only made this a method so we could throw descriptive error
     *
     * @param  string $json
     * @throws Ticketevolution_Webservice_Exception
     * @return string
     */
    protected static function _jsonDecode($json)
    {
        $decodedJson = json_decode($json);
        if(!is_null($decodedJson)) {
            return $decodedJson;
        }
        switch(json_last_error())
        {
            case JSON_ERROR_NONE:
                $error = 'No errors';
                break;
                
            case JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;

            case JSON_ERROR_CTRL_CHAR:
                $error = 'Unexpected control character found';
                break;

            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON';
                break;

            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON';
                break;

            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;

        }
        /**
         * @see Ticketevolution_Webservice_Exception
         */
        require_once 'Ticketevolution/Webservice/Exception.php';
        throw new Ticketevolution_Webservice_Exception('An error occurred decoding the JSON received: '
                                       . $error);
    }
}
