<?php

namespace Themes\TwoBackend\Support\Facades;

use Two\Support\Facades\Facade;


class ThemeOptions extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two_theme_options_backend'; }
}