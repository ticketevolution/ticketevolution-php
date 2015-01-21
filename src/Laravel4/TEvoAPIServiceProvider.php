<?php namespace TicketEvolution\Laravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use TicketEvolution\Exception\UnauthorizedException;

class TEvoAPIServiceProvider extends LaravelServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('ticketevolution', function ($app) {

            if (isset($app['config']['services']['ticketevolution'])) {
                $config = array_filter($app['config']['services']['ticketevolution']);

                return new \TicketEvolution\Client($config);
            } else {
                return new \TicketEvolution\Client();
            }

        });

        $app = $this->app;

        $this->app->error(function (UnauthorizedException $exception) use ($app) {
            $app['log']->warning($exception);
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('ticketevolution');
    }
}
