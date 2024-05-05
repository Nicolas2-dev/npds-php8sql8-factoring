<?php

declare(strict_types=1);

namespace Npds\Support\Facades;

use Npds\Support\Facades\Facade;


/**
 * @see \Npds\Routing\UrlGenerator
 */
class Url extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'url'; }

}
