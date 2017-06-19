# PHP Client Library v4 for the [Ticket Evolution API](http://developer.ticketevolution.com/)

## Basic Usage
Here is the most basic usage.

In your Terminal:
```bash
$ php composer require ticketevolution/ticketevolution-php
```

```php
<?php

use TicketEvolution\Client as TEvoClient;

// Require Composer’s autoloader
require_once '../vendor/autoload.php';

// Create an API Client
$apiClient = new TEvoClient([
    'baseUrl'    => 'https://api.sandbox.ticketevolution.com',
    'apiVersion' => 'v9',
    'apiToken'   => 'xxxxxxxxxxxxxxxxxxxxxxxx',
    'apiSecret'  => 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy',
]);

// Get a list of the 25 most popular events sorted by descending popularity
try {
    $results = $apiClient->listEvents([
        'page'     => 1,
        'per_page' => 25,
        'order_by' => 'events.popularity_score DESC',
    ]);
} catch (\Exception $e) {
    // Handle your Exceptions
}

var_dump($results);
```

## Advanced Usage
Here is a much more “real world” setup that also includes a logger ([Monolog](https://github.com/Seldaek/monolog) in this example) as a middleware.

In your Terminal:
```bash
$ php composer require ticketevolution/ticketevolution-php rtheunissen/guzzle-log-middleware monolog/monolog
```

```php
<?php

use Concat\Http\Middleware\Logger;
use GuzzleHttp\MessageFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;
use Psr\Log\LogLevel;
use TicketEvolution\Client as TEvoClient;

// Require Composer’s autoloader
require_once '../vendor/autoload.php';

/**
 * Setup Logger
 * This creates a file logger with a level of Debug mode for development.
 * For Production you probably want to adjust the log level.
 */
$log = new MonoLogger('TEvoAPILogger');
//$log->pushHandler(new StreamHandler('/Users/jcobb/Sites/www.myaweometicketsite.dev/myaweometicketsite.com/app/storage/logs/guzzle.log', LogLevel::DEBUG));
$log->pushHandler(new StreamHandler('../../app/storage/logs/guzzler.log', LogLevel::DEBUG));

// Add a formatter
$formatter = new MessageFormatter(MessageFormatter::DEBUG);

// Create a middleware for logging
$middleware = new Logger($log);

// Apply the formatter
$middleware->setFormatter($formatter);

// Add it to the middleware array so it will get pushed to the client’s stack
$middlewares[] = $middleware;

// Create an API Client
$apiClient = new TEvoClient([
    'baseUrl'    => 'https://api.sandbox.ticketevolution.com',
    'apiVersion' => 'v9',
    'apiToken'   => 'xxxxxxxxxxxxxxxxxxxxxxxx',
    'apiSecret'  => 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy',
], $middlewares);

// Get a list of the 25 most popular events sorted by descending popularity
try {
$results = $apiClient->listEvents([
    'page'     => 1,
    'per_page' => 25,
    'order_by' => 'events.popularity_score DESC',
]);
} catch (\Exception $e) {
    // Handle your Exceptions
}

// Use the RequestTimer to see how long the operation took.
echo 'Request took ' . $requestTimer->getElapsedTime() . ' seconds';

var_dump($results);
```


## Installation for Laravel 5 ##

[Laravel 5](http://laravel.com/) is not required, but if you are using Laravel 5 this package includes both a [ServiceProvider](http://laravel.com/docs/5.0/providers) and a [Facade](http://laravel.com/docs/5.0/facades) for easy integration.

Install the package via Composer

``` bash
$ composer require ticketevolution/ticketevolution-php
```

### Laravel 5.5+
[Package Auto-Discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518) will automatically add the ServiceProvider and `TEvo` facade.

### Laravel < 5.5
After updating composer add the `TEvoServiceProvider` to the `providers` array in `config/app.php`:

``` php
TicketEvolution\Laravel\TEvoServiceProvider::class,
```

If you want to use the `TEvo` facade add this to the `aliases` array in `config/app.php`:

``` php
'TEvo' => TicketEvolution\Laravel\TEvoFacade::class,
```

### Install configuration file
To copy the default configuration file to `config/ticketevolution.php` run

``` bash
$ php artisan vendor:publish --provider="TicketEvolution\Laravel\TEvoServiceProvider" --tag=config
```

### Use the `.env` file
In Laravel 5 it is recommended that you [keep your API credentials in your `.env` file](http://laravel.com/docs/5.0/configuration#environment-configuration) and that you do not publish that to your repo. In your `.env` you should include

``` php
TICKETEVOLUTION_API_BASEURL=https://api.sandbox.ticketevolution.com/v9
TICKETEVOLUTION_API_VERSION=v9
TICKETEVOLUTION_API_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TICKETEVOLUTION_API_SECRET=yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy
```

