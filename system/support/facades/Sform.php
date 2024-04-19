<?php
declare(strict_types=1);

namespace npds\system\support\facades;

use npds\system\sform\sform as SformManager;


class Sform
{

    public static function __callStatic($method, $parameters)
    {
        $instance = SformManager::instance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}