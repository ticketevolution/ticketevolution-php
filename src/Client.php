<?php

namespace TicketEvolution;

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\HandlerStack;
use Symfony\Component\Config\FileLocator;
use function GuzzleHttp\default_user_agent;

class Client
{
    /**
     * Version for this library
     *
     * @const string
     */
    const VERSION = '4.2.3';

    /**
     * Guzzle service description
     *
     * @var \GuzzleHttp\Command\Guzzle\Description
     */
    private static $_description;


    /**
     * Guzzle base client
     *
     * @var \GuzzleHttp\Client
     */
    private $baseClient;


    /**
     * Guzzle Services client
     *
     * @var \GuzzleHttp\Command\Guzzle\GuzzleClient
     */
    private $serviceClient;


    /**
     * Configuration settings
     *
     * @var array
     */
    private $settings;


    /**
     * Create a new API client using the supplied settings.
     * Add a TEvoAuthMiddleware to handle the signing of requests.
     *
     * @param array $settings
     * @param array $middleware
     */
    public function __construct(array $settings = [], array $middleware = [])
    {
        $this->settings = $settings;
        $this->middleware = $middleware;

        // Use the TEvoAuth middleware to handle the request signing
        $this->middleware[] = new TEvoAuthMiddleware($this->settings['apiToken'], $this->settings['apiSecret']);

        // Don’t need these any more
        unset($this->settings['apiToken'], $this->settings['apiSecret']);

        // TEvo API servers don't like the “Expect: 100” header so override it
        // http://docs.guzzlephp.org/en/latest/request-options.html#expect
        $this->settings['expect'] = false;

        // Set a custom User Agent indicating which version of this library we are using
        // if one isn't already provided
        if (!isset($options['headers']['User-Agent'])) {
            $this->settings['headers']['User-Agent'] = 'ticketevolution-php/' . self::VERSION . ' ' . default_user_agent();
        }

    }


    /**
     * Merge (via override) settings with newly provided ones.
     *
     * @param  array $settings
     *
     * @return \TicketEvolution\Client
     */
    public function settings(array $settings): Client
    {
        $this->settings = array_merge($this->settings, $settings);

        if ($this->serviceClient) {
            $this->buildClient();
        }

        return $this;
    }


    /**
     * Build a new service client, reloading the description of necessary.
     *
     */
    private function buildClient()
    {
        $client = $this->getBaseClient();

        if (!static::$_description) {
            $this->reloadDescription();
        }

        $this->serviceClient = new GuzzleClient($client, static::$_description);
    }


    /**
     * Retrieve Guzzle base client.
     *
     * @return BaseClient
     */
    private function getBaseClient(): BaseClient
    {
        return $this->baseClient ?: $this->baseClient = $this->loadBaseClient($this->settings);
    }


    /**
     * Set adapter and create Guzzle base client.
     *
     * @param array $settings
     *
     * @return BaseClient
     */
    private function loadBaseClient(array $settings = []): BaseClient
    {
        // Create a handler stack and add any supplied middleware
        $stack = HandlerStack::create();
        array_walk($this->middleware, function ($middleware) use ($stack) {
            $stack->push($middleware);
        });

        $settings['handler'] = $stack;

        // Create the BaseClient
        $this->baseClient = new BaseClient($settings);

        return $this->baseClient;
    }


    /**
     * Description works tricky as a static property, reload as needed.
     *
     */
    private function reloadDescription()
    {
        static::$_description = $this->loadDescription();
    }


    /**
     * Load configuration file(s) and parse resources.
     *
     * @return Description
     */
    private function loadDescription(): Description
    {
        $locator = new FileLocator(realpath(__DIR__ . '/Resources/' . $this->settings['apiVersion']));

        $phpLoader = new PhpLoader($locator);

        $description = $phpLoader->load($locator->locate('service-description.php'));

        // Add the baseUrl specified in the settings.
        // Allowing one to easily change the baseUrl makes working in different environments easy.
        $description['baseUrl'] = $this->settings['baseUrl'];

        $this->description = new Description($description);

        return $this->description;
    }


    /**
     * Execute the command provided. This is used in calls that look like
     *
     * ```
     * $response = $client->listBrokerages(['page' => 2, 'per_page' => 2]);
     * ```
     *
     * as well as calls that look like
     *
     * ```
     * $command = $client->getCommand('listBrokerages', ['page' => 2, 'per_page' => 2]);
     * $response = $client->execute($command);
     * ```
     *
     * @param $method
     * @param $parameters
     *
     * @return \GuzzleHttp\Command\Result|\GuzzleHttp\Command\Command
     */
    public function __call($method, $parameters)
    {
        if (!$this->serviceClient) {
            $this->buildClient();
        }

        $response = call_user_func_array([$this->serviceClient, $method], $parameters);

        return $response;
    }
}
