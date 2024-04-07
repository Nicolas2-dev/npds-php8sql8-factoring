<?php
declare(strict_types=1);

namespace npds\system\support\facades;

use npds\system\cache\cacheManager;

class Cache
{

    public static function __callStatic($method, $parameters)
    {
        $instance = cacheManager::instance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}