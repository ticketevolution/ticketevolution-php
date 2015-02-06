# PHP Client Library v3 for the [Ticket Evolution API](http://developer.ticketevolution.com/)

## Basic Usage
```php
<?php

// Require Composer’s autoloader
require 'vendor/autoload.php';

// Create an API Client
$apiClient = TicketEvolution::settings([
    'baseUrl'     => 'https://api.sandbox.ticketevolution.com',
    'apiVersion'  => 'v9',
    'apiToken'    => 'xxxxxxxxxxxxxxxxxxxxxxxx',
    'apiSecret'   => 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy',
]);

// Get a list of the 25 most popular events sorted by descending popularity
try {
    $events = $apiClient->listEvents([
        'page'     => 1,
        'per_page' => 25,
        'order_by' => 'events.popularity_score DESC',
    ]);
} catch (\Exception $e) {
    // Handle your Exceptions
}
```

## Advanced Usage
Here is a much more “real world” setup that also includes a logger ([Monolog](https://github.com/Seldaek/monolog) in this example), a [retry subscriber](https://github.com/guzzle/retry-subscriber/) and the included `RequestTimer` subscriber.

In your Terminal:
```
$ php composer require ticketevolution/ticketevolution-php monolog/monolog guzzlehttp/retry-subscriber
```

```php
<?php

use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use GuzzleHttp\Subscriber\Retry\RetrySubscriber;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use TicketEvolution\Client as TEvoClient;
use TicketEvolution\Subscriber\RequestTimer;

// Require Composer’s autoloader
require 'vendor/autoload.php';

/**
 * Setup Logger
 * This creates a file logger with a level of Debug mode for development.
 * For Production you probably want to adjust the log level.
 */
$log = new Logger('TEvoAPIClientLogger');
$log->pushHandler(new StreamHandler('/Users/jcobb/Sites/www.myaweometicketsite.dev/myaweometicketsite.com/app/storage/logs/guzzle.log', Logger::DEBUG));
$logSubscriber = new LogSubscriber($log, Formatter::DEBUG);

/**
 * Setup Retry Subscriber
 * This creates a retry subscriber that automatically retries requests that errored on connection (such as a timeout)
 */
$retrySubscriber = new RetrySubscriber([
    'filter' => RetrySubscriber::createConnectFilter()
]);

/**
 * Setup Request Timer Subscriber
 * This project includes a RequestTimer subscriber which allows you to see how long it took to send a request and receive the results.
 * You probably want to turn this off in Production
 */
$requestTimer = new RequestTimer();

// Create an API Client
$apiClient = TicketEvolution::settings([
    'baseUrl'     => 'https://api.sandbox.ticketevolution.com',
    'apiVersion'  => 'v9',
    'apiToken'    => 'xxxxxxxxxxxxxxxxxxxxxxxx',
    'apiSecret'   => 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy',
]);

// Attach the log subscriber
$client->getEmitter()->attach($logSubscriber);
// Attach the retry subscriber
$client->getEmitter()->attach($retrySubscriber);
// Attach the RequestTimer subscriber
$client->getEmitter()->attach($requestTimer);

// Get a list of the 25 most popular events sorted by descending popularity
try {
    $events = $apiClient->listEvents([
        'page'     => 1,
        'per_page' => 25,
        'order_by' => 'events.popularity_score DESC',
    ]);
} catch (\Exception $e) {
    // Handle your Exceptions
}

// Use the RequestTimer to see how long the operation took.
echo 'Request took ' . $requestTimer->getElapsedTime() . ' seconds';
```

## Even More Advanced Usage
If you have a need to use some of Guzzle’s more advanced features such as [Pools for batching of requests](http://guzzle.readthedocs.org/en/latest/clients.html?highlight=batch#batching-requests) you can do that. You will need to specify explicit URLs as [Pools](http://guzzle.readthedocs.org/en/latest/clients.html?highlight=batch#sending-requests-with-a-pool) cannot be used with the provided Service Definition which provides the handy magic methods such as `listEvents()`.

```php
<?php

use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use GuzzleHttp\Subscriber\Retry\RetrySubscriber;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use TicketEvolution\Client as TEvoClient;
use TicketEvolution\Subscriber\RequestTimer;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Pool;
use TicketEvolution\Subscriber\RequestTimer;
use TicketEvolution\Subscriber\TEvoAuth;

// Require Composer’s autoloader
require 'vendor/autoload.php';

/**
 * Setup Logger
 * This creates a file logger with a level of Debug mode for development.
 * For Production you probably want to adjust the log level.
 */
$log = new Logger('TEvoAPIClientLogger');
$log->pushHandler(new StreamHandler('/Users/jcobb/Sites/www.myaweometicketsite.dev/myaweometicketsite.com/app/storage/logs/guzzle.log', Logger::DEBUG));
$logSubscriber = new LogSubscriber($log, Formatter::DEBUG);

/**
 * Setup Retry Subscriber
 * This creates a retry subscriber that automatically retries requests that errored on connection (such as a timeout)
 */
$retrySubscriber = new RetrySubscriber([
    'filter' => RetrySubscriber::createConnectFilter()
]);

/**
 * Setup Request Timer Subscriber
 * This project includes a RequestTimer subscriber which allows you to see how long it took to send a request and receive the results.
 * You probably want to turn this off in Production
 */
$requestTimer = new RequestTimer();

// Create an API client that can handle Pools
$poolClient = new GuzzleClient([
    'base_url' => [
        'https://api.sandbox.ticketevolution.com',
        ['apiVersion' => 'v9'],
    ],
    'defaults' => [
        'auth' => 'tevoauth',
    ]
]);


/**
 * Attach various subscribers
 */
// Attach the TEvoAuth subscriber to handle [request signing](https://ticketevolution.atlassian.net/wiki/display/API/Signing)
$poolClient->getEmitter()->attach(new TEvoAuth('xxxxxxxxxxxxxxxxxxxxxxxx', 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy'));

// Attach the log subscriber
$poolClient->getEmitter()->attach($logSubscriber);

// Attach the retry subscriber
$poolClient->getEmitter()->attach($retrySubscriber);



/**
 * Create the requests to be pooled.
 * Since you must specify the actual URLs be sure to [follow the documentation and sort your GET parameters](https://ticketevolution.atlassian.net/wiki/display/API/Signing#Signing-GETrequests).
 */
$requests = [
    // Get a list of the 5 most popular SPORTS events anywhere sorted by descending popularity
    $poolClient->createRequest('GET', 'events?category_id=1&category_tree=true&order_by=events.popularity_score+DESC&page=1&per_page=5'),

    // Get a list of the 5 most popular CONCERTS events anywhere sorted by descending popularity
    $poolClient->createRequest('GET', 'events?category_id=54&category_tree=true&order_by=events.popularity_score+DESC&page=1&per_page=5'),

    // Get a list of the 5 most popular THEATRE events anywhere sorted by descending popularity
    $poolClient->createRequest('GET', 'events?category_id=68&category_tree=true&order_by=events.popularity_score+DESC&page=1&per_page=5'),
];

// $results is a GuzzleHttp\BatchResults object.
try {
    $results = Pool::batch($poolClient, $requests);
} catch (\Exception $e) {
    // Handle your Exceptions
}

// Can be accessed by index.
$i = 0;
foreach ($requests as $request) {
    echo '<p>' . $results[$i]->getBody() . '</p>' . PHP_EOL;
    $i++;
}

// Use the RequestTimer to see how long the operation took.
echo 'Requests took ' . $requestTimer->getElapsedTime() . ' seconds';

```

## Installation for Laravel 5 ##

[Laravel 5](http://laravel.com/) is not required, but if you are using Laravel 5 this package includes both a [ServiceProvider](http://laravel.com/docs/5.0/providers) and a [Facade](http://laravel.com/docs/5.0/facades) for easy integration.

Install the package via Composer

``` bash
$ composer require ticketevolution/ticketevolution-php
```

After updating composer add the `TEvoServiceProvider` to the `providers` array in `config/app.php`:

``` php
'TicketEvolution\Laravel\TEvoServiceProvider',
```

If you want to use the `TEvo` facade add this to the `aliases` array in `config/app.php`:

``` php
'TEvo' => 'TicketEvolution\Laravel\TEvoFacade',
```

To copy the default configuration file to `config/ticketevolution.php` run

``` bash
$ php artisan vendor:publish
```

In Laravel 5 it is recommended that you [keep your API credentials in your `.env` file](http://laravel.com/docs/5.0/configuration#environment-configuration) and that you do not publish that to your repo. In your `.env` you should include

``` php
TICKETEVOLUTION_API_BASEURL=https://api.sandbox.ticketevolution.com/v9
TICKETEVOLUTION_API_VERSION=v9
TICKETEVOLUTION_API_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TICKETEVOLUTION_API_SECRET=yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy

```

