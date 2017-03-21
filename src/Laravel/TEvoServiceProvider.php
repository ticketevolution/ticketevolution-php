<?php

namespace TicketEvolution\Laravel;

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
        $configPath = realpath(__DIR__ . '/config/ticketevolution.php');

        if (function_exists('config_path')) {
            $publishPath = config_path('ticketevolution.php');
        } else {
            $publishPath = base_path('config/ticketevolution.php');
        }

        $this->publishes([$configPath => $publishPath], 'config');

        $this->mergeConfigFrom($configPath, 'ticketevolution');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('tevo', function () {
            return new Client(config('ticketevolution'));
        });

        $this->app->alias('tevo', Client::class);
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
