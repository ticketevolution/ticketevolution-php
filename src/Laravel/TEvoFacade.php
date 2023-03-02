<?php

declare(strict_types=1);

namespace TicketEvolution\Laravel;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class TEvoFacade extends LaravelFacade
{
    protected static function getFacadeAccessor(): string
    {
        return 'tevo';
    }
}
