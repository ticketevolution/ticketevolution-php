<?php

declare(strict_types=1);

namespace TicketEvolution;

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Command\Command;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Result;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Utils;
use Symfony\Component\Config\FileLocator;

#[\AllowDynamicProperties]
class Client
{
    public const VERSION = '5.0.0';

    private static Description $description;

    private BaseClient $baseClient;

    private GuzzleClient $serviceClient;

    /**
     * Create a new API client using the supplied settings.
     * Add a TEvoAuthMiddleware to handle the signing of requests.
     */
    public function __construct(private array $settings = [], private array $middleware = [])
    {
        // Use the TEvoAuth middleware to handle the request signing
        $this->middleware = array_merge([
            new TEvoAuthMiddleware($this->settings['apiToken'], $this->settings['apiSecret']),
        ], $middleware);

        // Do not need these anymore
        unset($this->settings['apiToken'], $this->settings['apiSecret']);

        // TEvo API servers do not like the “Expect: 100” header so override it
        // http://docs.guzzlephp.org/en/latest/request-options.html#expect
        $this->settings['expect'] = false;

        // Set a custom User Agent indicating which version of this library we are using
        // if one is not already provided
        if (! isset($options['headers']['User-Agent'])) {
            $this->settings['headers']['User-Agent'] = 'ticketevolution-php/'.self::VERSION.' '.Utils::defaultUserAgent();
        }
    }

    /**
     * Merge (via override) settings with newly provided ones.
     */
    public function settings(array $settings): Client
    {
        if (isset($settings['apiVersion']) && $settings['apiVersion'] !== $this->settings['apiVersion']) {
            $reloadDescription = true;
        }

        $this->settings = array_merge($this->settings, $settings);

        if ($reloadDescription ?? false) {
            $this->reloadDescription();
        }

        if (isset($this->serviceClient)) {
            $this->buildClient();
        }

        return $this;
    }

    /**
     * Build a new service client, reloading the description of necessary.
     */
    private function buildClient(): void
    {
        $client = $this->getBaseClient();

        if (! isset(static::$description)) {
            $this->reloadDescription();
        }

        $this->serviceClient = new GuzzleClient($client, static::$description);
    }

    /**
     * Retrieve Guzzle base client.
     */
    private function getBaseClient(): BaseClient
    {
        return isset($this->baseClient) ?: $this->baseClient = $this->loadBaseClient($this->settings);
    }

    /**
     * Set adapter and create Guzzle base client.
     */
    private function loadBaseClient(array $settings = []): BaseClient
    {
        // Create a handler stack and add any supplied middleware
        $stack = HandlerStack::create();
        array_walk($this->middleware, static function ($middleware) use ($stack) {
            $stack->push($middleware);
        });

        $settings['handler'] = $stack;

        // Create the BaseClient
        $this->baseClient = new BaseClient($settings);

        return $this->baseClient;
    }

    /**
     * Description works tricky as a static property, reload as needed.
     */
    private function reloadDescription(): void
    {
        static::$description = $this->loadDescription();
    }

    /**
     * Load configuration file(s) and parse resources.
     */
    private function loadDescription(): Description
    {
        $locator = new FileLocator(realpath(__DIR__.'/Resources/'.$this->settings['apiVersion']));

        $phpLoader = new PhpLoader($locator);

        $description = $phpLoader->load($locator->locate('service-description.php'));

        // Add the baseUrl specified in the settings.
        // Allowing one to easily change the baseUrl makes working in different environments easy.
        $description['baseUrl'] = $this->settings['baseUrl'];

        static::$description = new Description($description);

        return static::$description;
    }

    /**
     * Execute the command provided. This is used in calls that look like
     *
     * $response = $client->listEvents(['page' => 2, 'per_page' => 2]);
     *
     * as well as calls that look like
     *
     * $command = $client->getCommand('listEvents', ['page' => 2, 'per_page' => 2]);
     * $response = $client->execute($command);
     */
    public function __call($method, $parameters): Result|Command
    {
        if (! isset($this->serviceClient)) {
            $this->buildClient();
        }

        return call_user_func_array([$this->serviceClient, $method], $parameters);
    }
}
