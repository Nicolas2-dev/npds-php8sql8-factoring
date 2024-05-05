<?php

declare(strict_types=1);

namespace Npds\Support\Facades;


/**
* @see \Npds\Session\Store
*/
class Session extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'session'; }
}
