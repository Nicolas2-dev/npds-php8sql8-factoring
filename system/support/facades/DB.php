<?php
declare(strict_types=1);

namespace npds\system\support\facades;

use npds\system\database\Manager;


class DB
{

    public static function __callStatic($method, $parameters)
    {
        $instance = Manager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}