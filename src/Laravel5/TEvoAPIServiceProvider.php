<?php namespace TicketEvolution\Laravel5;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use TicketEvolution\Client;

class TEvoAPIServiceProvider extends ServiceProvider
{

    protected $tevo;


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->tevo = new Client([
            'baseUrl'    => Config::get('tevo.baseUrl'),
            'apiVersion' => Config::get('tevo.apiVersion'),
            'apiToken'   => Config::get('tevo.apiToken'),
            'apiSecret'  => Config::get('tevo.apiSecret'),
        ]);
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('tevo', function () {
            return $this->tevo;
        });
    }
}
