<?php

namespace Modules\TwoThemes\Support\Facades;

use Two\Support\Facades\Facade;


class Theme extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two_theme'; }
}