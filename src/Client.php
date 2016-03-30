<?php namespace TicketEvolution;

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use TicketEvolution\Subscriber\TEvoAuth;

class Client
{
    /**
     * Version for this library
     *
     * @const string
     */
    const VERSION = '3.0.4';

    /**
     * Guzzle service description
     *
     * @var \TicketEvolution\Description
     */
    private static $_description;


    /**
     * Guzzle base client
     *
     * @var \GuzzleHttp\Client
     */
    private $_baseClient;


    /**
     * Adapter for Guzzle base client
     *
     * @var \GuzzleHttp\Adapter\AdapterInterface
     */
    private $_baseClientAdapter;


    /**
     * Api client services
     *
     * @var \GuzzleHttp\Command\Guzzle\GuzzleClient
     */
    private $_client;


    /**
     * TicketEvolution client config settings
     *
     * @var array
     */
    private $_settings;


    /**
     * Request header items
     *
     * @var array
     */
    private $_globalParams = [
//        'apiVersion'   => [
//            'type'     => 'string',
//            'location' => 'uri',
//            'required' => true,
//        ],
    ];


    /**
     * Create a new GuzzleClient Service, ability to use the client
     * without setting properties on instantiation.
     *
     * @param  array $settings
     */
    public function __construct(array $settings = array())
    {
        $this->_settings = $settings;
    }


    /**
     * Merge additional settings with existing and save. Overrides
     * existing settings as well.
     *
     * @param  array $settings
     *
     * @return static
     */
    public function settings(array $settings)
    {
        $this->_settings = array_merge($this->_settings, $settings);
        if ($this->_client) {
            $this->buildClient();
        }

        return $this;
    }


    /**
     * Load resource configuration file and return array.
     *
     * @param  string $name
     *
     * @return array
     */
    private function loadResource($name)
    {
        return require __DIR__ . '/Resources/' . $this->_settings['apiVersion'] . '/' . $name . '.php';
    }


    /**
     * Build new service client from descriptions.
     *
     * @return void
     */
    private function buildClient()
    {
        $client = $this->getBaseClient();

        if (!static::$_description) {
            $this->reloadDescription();
        }

        $this->_client = new GuzzleClient(
            $client,
            static::$_description,
            [
                'emitter' => $this->_baseClient->getEmitter(),
            ]
        );
    }


    /**
     * Retrieve Guzzle base client.
     *
     * @return \GuzzleHttp\Client
     */
    private function getBaseClient()
    {
        return $this->_baseClient ?: $this->_baseClient = $this->loadBaseClient();
    }


    /**
     * Set adapter and create Guzzle base client.
     *
     * @return \GuzzleHttp\Client
     */
    private function loadBaseClient(array $settings = [])
    {
        // Force the authorization scheme to 'tevoauth'
        $settings['defaults']['auth'] = 'tevoauth';

        if ($this->_baseClientAdapter) {
            $settings['adapter'] = $this->_baseClientAdapter;
        }

        $this->_baseClient = new BaseClient($settings);

        // Attach the TEvoAuth subscriber to handle the required authorization
        $this->_baseClient->getEmitter()->attach(new TEvoAuth($this->_settings['apiToken'], $this->_settings['apiSecret']));

        // Don't need these any more
        unset($this->_settings['apiToken'], $this->_settings['apiSecret']);

        // Set a custom User Agent indicating which version of this library we are using.
        // Can’t set this in $settings['defaults'] above because we want to prepend the default.
        $this->_baseClient->setDefaultOption(
            'headers/User-Agent',
            'ticketevolution-php/' . self::VERSION . ' ' . $this->_baseClient->getDefaultUserAgent()
        );

        // TEvo API servers don't like the Expect: 100 header
        $this->_baseClient->setDefaultOption('expect', false);


        return $this->_baseClient;
    }


    /**
     * Description works tricky as a static
     * property, reload as a needed.
     *
     * @return void
     */
    private function reloadDescription()
    {
        static::$_description = new Description($this->loadConfig());
    }


    /**
     * Load configuration file and parse resources.
     *
     * @return array
     */
    private function loadConfig()
    {
        $description = $this->loadResource('service-config');

        // initial description building, use api info and build base url
        $description = $description + [
                'baseUrl'    => $this->_settings['baseUrl'],
                'operations' => [],
                'models'     => []
            ];

        // Don't need this any more
        unset($this->_settings['baseUrl']);

        // process each of the service description resources defined
        foreach ($description['services'] as $serviceName) {
            $service = $this->loadResource($serviceName);
            $description = $this->loadServiceDescription($service, $description);
        }

        // dead weight now, clean it up
        unset($description['services']);

        return $description;
    }


    /**
     * Load service description from resource, add global
     * parameters to operations. Operations and models
     * added to full description.
     *
     * @param  array $service
     * @param  array $description
     *
     * @return array
     */
    private function loadServiceDescription(array $service, array $description)
    {
        foreach ($service as $section => $set) {
            if ($section == 'operations') {
                // add global parameters to the operation parameters
                foreach ($set as &$op) {
                    $op['parameters'] = isset($op['parameters'])
                        ? $op['parameters'] + $this->_globalParams
                        : $this->_globalParams;
                }
            }
            $description[$section] = $description[$section] + $set;
        }

        return $description;
    }


    public function __call($method, $parameters)
    {
        if (!$this->_client) {
            $this->buildClient();
        }

        // gather parameters to pass to service definitions
        $settings = $this->_settings;

        // merge client settings/parameters and method parameters
        if (!isset($parameters[0])) {
            // This happens when called via something like
            // $response = $client->getUsers();
            $parameters[0] = $settings;
        } elseif (is_string($parameters[0])) {
            // This happens when called via something like
            // $command = $client->getCommand('myCommand', ['option' => 'value']);
            $parameters[1] = $parameters[1] + $settings;
        } elseif (is_array($parameters[0])) {
            // This happens when called via something like
            // $response = $client->getUsers(['option' => 'value']);
            $parameters[0] = $parameters[0];// + $settings;
        }

        $response = call_user_func_array([$this->_client, $method], $parameters);

        return $response;
    }
}
