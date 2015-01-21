# PHP Client Library v3 for the [Ticket Evolution API](http://developer.ticketevolution.com/)
The Ticket Evolution PHP Client is an open source framework created to simplify working with the [Ticket Evolution API web service](http://api.ticketevolution.com/). We created it to enable you to quickly implement the Ticket Evolution API web service on your own site whether you are creating a new site or switching from a different provider. We released it as open source so that you could make changes and improvements as you see fit. When you do we hope that you will give these changes back to the community by contributing them back to the project.


## Install

Via Composer

``` bash
$ composer require ticketevolution/ticketevolution-php
```

## Usage

``` php
$apiClient = TicketEvolution::settings([
    'baseUrl'     => 'https://api.ticketevolution.com',
    'apiVersion'  => 'v9',
    'apiToken'    => 'xxxxxxxxxxxxxxxxxxxxxxxx',
    'apiSecret'   => 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy',
]);

$events = $apiClient->listEvents();
```

## Contributing

Please see [CONTRIBUTING](https://github.com/ticketevolution/ticketevolution-php/blob/master/CONTRIBUTING.md) for details.

## Credits

- [J Cobb](https://github.com/jwcobb)
- [All Contributors](https://github.com/ticketevolution/ticketevolution-php/contributors)

## License

The BSD 3-Clause License (New BSD). Please see [License File](LICENSE.md) for more information.


## Acknowledgments
Some inspiration was taken from the [ShopifyExtras/PHP-Shopify-API-Wrapper](https://github.com/ShopifyExtras/PHP-Shopify-API-Wrapper)

