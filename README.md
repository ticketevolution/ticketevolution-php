# Ticket Evolution PHP Client Library
As of version 2.0.0 the Demo app and DataLoaders have been split into separate projects in order to simplify this package down to just the actual library.

- The Demo app is now at https://github.com/ticketevolution/ticketevolution-php-demo
- The DataLoaders app is now at https://github.com/jwcobb/ticketevolution-php-dataloaders


## Version 3 is Around the Corner
Development of version 3 of this library is just about finished and a preview can be seen in the `3.0` branch. **Version 3 will be 100% incompatible with version 2.** Version 3 drops the Zend Framework dependency and switches to using [Guzzle](https://github.com/guzzle/guzzle) for the transport layer.

Once v3 is officially released version 2 will be considered *maintenance only* but you will still be able to use version 2. Just make sure that you are using `"ticketevolution/ticketevolution-php": "~2.2"` in in your `composer.json`.


## Install

Via Composer

``` bash
$ composer require ticketevolution/ticketevolution-php
```

## Usage

``` php
$client = new TicketEvolution\Webservice($config);
var_dump($client->listPerformers();
```

## Contributing

Please see [CONTRIBUTING](https://github.com/ticketevolution/ticketevolution-php/blob/master/CONTRIBUTING.md) for details.

## Credits

- [J Cobb](https://github.com/jwcobb)
- [All Contributors](https://github.com/ticketevolution/ticketevolution-php/contributors)

## License

The BSD 3-Clause License (New BSD). Please see [License File](LICENSE.md) for more information.
