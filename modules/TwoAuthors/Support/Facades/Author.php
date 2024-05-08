<?php

namespace Modules\TwoAuthors\Support\Facades;

use Two\Support\Facades\Facade;


class Author extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two_author'; }
}
