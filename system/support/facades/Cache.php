<?php

declare(strict_types=1);

namespace Npds\Support\Facades;

use Npds\Support\Facades\Facade;


/**
 * @see \Npds\Cache\CacheManager
 * @see \Npds\Cache\Repository
 */
class Cache extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'cache'; }

}
