<?php

namespace Modules\TwoNews\Support\Facades;

use Two\Support\Facades\Facade;


class News extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two_news'; }
}
