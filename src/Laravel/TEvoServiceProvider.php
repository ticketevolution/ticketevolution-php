<?php namespace TicketEvolution\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use TicketEvolution\Client;

class TEvoServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }


    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/config/ticketevolution.php');

        $this->publishes([$source => config_path('ticketevolution.php')]);

        $this->mergeConfigFrom($source, 'ticketevolution');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('tevo', function () {
            return new Client(config('ticketevolution'));
        });

        $this->app->alias('tevo', 'TicketEvolution\Client');
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['tevo'];
    }

}
