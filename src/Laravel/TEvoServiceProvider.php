<?php

declare(strict_types=1);

namespace TicketEvolution\Laravel;

use Illuminate\Support\ServiceProvider;
use TicketEvolution\Client;

class TEvoServiceProvider extends ServiceProvider
{
    protected bool $defer = true;

    public function boot(): void
    {
        $this->setupConfig();
    }

    protected function setupConfig(): void
    {
        $configPath = realpath(__DIR__.'/config/ticketevolution.php');

        if (function_exists('config_path')) {
            $publishPath = config_path('ticketevolution.php');
        } else {
            $publishPath = base_path('config/ticketevolution.php');
        }

        $this->publishes([$configPath => $publishPath], 'config');

        $this->mergeConfigFrom($configPath, 'ticketevolution');
    }

    public function register(): void
    {
        $this->app->singleton('tevo', function () {
            return new Client(config('ticketevolution'));
        });

        $this->app->alias('tevo', Client::class);
    }

    public function provides(): array
    {
        return ['tevo'];
    }
}
