<?php
declare(strict_types=1);

namespace npds\system\support\facades;

use npds\system\http\Request as HttpRequest;


class Request
{

    public static function __callStatic($method, $parameters)
    {
        $instance = HttpRequest::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}