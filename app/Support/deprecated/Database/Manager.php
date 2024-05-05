<?php

declare(strict_types=1);

namespace Npds\Database;

use Exception;
use Npds\Config\Config;


class Manager
{
    protected static $instance;

    /**
     * The Connection instances.
     *
     * @var \Npds\Database\Connection[]
     */
    protected $instances = array();


    /**
     * Get a Database Manager instance.
     *
     * @return \Npds\Database\Manager
     */
    public static function getInstance()
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    public function connection($name = 'default')
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (is_null($config = Config::get('database.' .$name))) {
            throw new Exception("Connection [$name] is not defined in configuration");
        }

        return $this->instances[$name] = new Connection($config);
    }

    public function __call($method, $parameters)
    {
        $instance = $this->connection();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}