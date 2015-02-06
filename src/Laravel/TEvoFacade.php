<?php namespace TicketEvolution\Laravel;

use Illuminate\Support\Facades\Facade as LaravelFacade;

/**
 * @see \TicketEvolution\Client
 */
class TEvoFacade extends LaravelFacade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tevo';
    }
}
