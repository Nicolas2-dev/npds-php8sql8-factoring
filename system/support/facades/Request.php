<?php

declare(strict_types=1);

namespace Npds\Support\Facades;

use Npds\Http\Request_old as HttpRequest;


/**
* @see \Npds\Http\Request
*/
class Request extends Facade
{

    /**
     * [__callStatic description]
     *
     * @param   [type]  $method      [$method description]
     * @param   [type]  $parameters  [$parameters description]
     *
     * @return  [type]
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = HttpRequest::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    //protected static function getFacadeAccessor() { return 'request'; }
}