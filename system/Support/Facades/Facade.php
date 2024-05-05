<?php

declare(strict_types=1);

namespace Npds\Support\Facades;

use Npds\Container\Container;


abstract class Facade
{

    /**
     * L'instance d'application étant en façade.
     *
     * @var \Npds\Foundation\Application
     */
    protected static $app;

    /**
     * The resolved object instances.
     *
     * @var array
     */
    protected static $resolvedInstance;

    /**
     * Get the registered name of the component.
     *
     * @return string
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * @param  string  $name
     * @return mixed
     */
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        $container = Container::getInstance();

        return static::$resolvedInstance[$name] = $container->make($name);
    }

    /**
     * Définissez l'instance de l'application.
     *
     * @param  \Npds\Foundation\Application  $app
     * @return void
     */
    public static function setFacadeApplication($app)
    {
        static::$app = $app;
    }

    /**
     * Obtenez l'instance de l'application.
     *
     * @return  \Npds\Foundation\Application  $app
     */
    public static function getFacadeApplication()
    {
        return static::$app;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());

        return call_user_func_array(array($instance, $method), $args);
    }
}
