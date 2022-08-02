<?php

namespace Joaovdiasb\LaravelPdfManager;

use Illuminate\Support\Facades\Facade;

class LaravelPdfManagerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-pdf-manager';
    }
}
