<?php

declare(strict_types=1);

namespace Npds\Support\Facades;

use Npds\Support\Facades\Facade;


/**
 * @see \Npds\Cookie\CookieJar
 */
class Cookie extends Facade
{
    const FIVEYEARS = 2628000;

    /**
     * Determine if a cookie exists on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public static function has($key)
    {
        return ! is_null(static::$app['request']->cookie($key, null));
    }

    /**
     * Retrieve a cookie from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public static function get($key = null, $default = null)
    {
        return static::$app['request']->cookie($key, $default);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'cookie'; }

}
