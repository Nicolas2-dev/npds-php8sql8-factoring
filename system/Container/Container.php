<?php

declare(strict_types=1);

namespace Npds\Container;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use InvalidArgumentException;
use ReflectionFunctionAbstract;

use Npds\Support\Arr;
use Npds\Container\Execption\BindingResolutionException;


class Container implements ArrayAccess
{

    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * The resolved shared instances.
     *
     * @var array
     */
    protected $instances = array();

    /**
     * An array of the types that have been resolved.
     *
     * @var array
     */
    protected $resolved = array();

    /**
     * The registered dependencies.
     *
     * @var array
     */
    protected $bindings = array();

    /**
     * The registered type aliases.
     *
     * @var array
     */
    protected $aliases = array();

    /**
     * All of the registered rebound callbacks.
     *
     * @var array
     */
    protected $reboundCallbacks = array();


    /**
     * Determine if an object has been registered in the container.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract)
    {
         return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Determine if the given abstract type has been resolved.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function resolved($abstract)
    {
        return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Register an object and its resolver.
     *
     * @param  string   $abstract
     * @param  mixed    $concrete
     * @param  bool     $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_array($abstract)) {
            list ($abstract, $alias) = $abstract;

            $this->alias($abstract, $alias);
        }

        unset($this->instances[$abstract], $this->aliases[$abstract]);

        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (! $concrete instanceof Closure) {
            $method = ($abstract == $concrete) ? 'build' : 'make';

            $concrete = function ($container, $parameters = array()) use ($method, $abstract, $concrete)
            {
                return call_user_func(array($container, $method), $concrete, $parameters);
            };
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');

        if ($this->resolved($abstract)) {
            $this->rebound($abstract);
        }
    }

    /**
     * Register an object as a singleton.
     *
     * Singletons will only be instantiated the first time they are resolved.
     *
     * @param  string   $abstract
     * @param  Closure  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * "Extend" an abstract type in the container.
     *
     * @param  string    $abstract
     * @param  \Closure  $closure
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function extend($abstract, Closure $closure)
    {
        if (! isset($this->bindings[$abstract])) {
            throw new InvalidArgumentException("Type {$abstract} is not bound.");
        }

        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $closure($this->instances[$abstract], $this);

            return $this->rebound($abstract);
        }

        $resolver = $this->bindings[$abstract]['concrete'];

        $this->bind($abstract, function ($container) use ($resolver, $closure)
        {
            return $closure($resolver($container), $container);

        }, $this->isShared($abstract));
    }

    /**
     * Alias a type to a shorter name.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     */
    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $abstract;
    }
    
    /**
     * Register an existing instance as a singleton.
     *
     * @param  string  $abstract
     * @param  mixed   $instance
     * @return void
     */
    public function instance($abstract, $instance)
    {
        if (is_array($abstract)) {
            list ($abstract, $alias) = $abstract;

            $this->alias($abstract, $alias);
        }

        unset($this->aliases[$abstract]);

        $bound = $this->bound($abstract);

        $this->instances[$abstract] = $instance;

        if ($bound) {
            $this->rebound($abstract);
        }
    }
 
    /**
     * Bind a new callback to an abstract's rebind event.
     *
     * @param  string    $abstract
     * @param  \Closure  $callback
     * @return mixed
     */
    public function rebinding($abstract, Closure $callback)
    {
        $this->reboundCallbacks[$abstract][] = $callback;

        if ($this->bound($abstract)) {
            return $this->make($abstract);
        }
    }

    /**
     * Refresh an instance on the given target and method.
     *
     * @param  string  $abstract
     * @param  mixed   $target
     * @param  string  $method
     * @return mixed
     */
    public function refresh($abstract, $target, $method)
    {
        return $this->rebinding($abstract, function ($app, $instance) use ($target, $method)
        {
            call_user_func(array($target, $method), $instance);
        });
    }

    /**
     * Fire the "rebound" callbacks for the given abstract type.
     *
     * @param  string  $abstract
     * @return void
     */
    protected function rebound($abstract)
    {
        $instance = $this->make($abstract);

        $callbacks = isset($this->reboundCallbacks[$abstract]) ? $this->reboundCallbacks[$abstract] : array();

        foreach ($callbacks as $callback) {
            call_user_func($callback, $this, $instance);
        }
    }

    /**
     * Call the given callable / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    public function call($callback, $parameters = array(), $defaultMethod = null)
    {
        if (is_string($callback)) {
            $callback = $this->resolveStringCallback($callback, $defaultMethod);
        }

        if ($callback instanceof Closure) {
            $reflector = new ReflectionFunction($callback);
        }

        //
        else if (is_array($callback)) {
            $reflector = new ReflectionMethod($callback[0], $callback[1]);
        }  else {
            throw new InvalidArgumentException('Invalid callback provided.');
        }

        return call_user_func_array(
            $callback, $this->getMethodDependencies($parameters, $reflector)
        );
    }

    /**
     * Resolve a string callback.
     *
     * @param  string  $callback
     * @param  string|null  $defaultMethod
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function resolveStringCallback($callback, $defaultMethod = null)
    {
        list ($className, $method) = $this->parseCallback($callback, $defaultMethod);

        if (empty($method) || ! class_exists($className)) {
            throw new InvalidArgumentException('Invalid callback provided.');
        }

        return array(
            $this->make($className), $method
        );
    }

    /**
     * Get all dependencies for a given method.
     *
     * @param  array  $parameters
     * @param  \ReflectionFunctionAbstract  $reflector
     * @return array
     */
    protected function getMethodDependencies(array $parameters, ReflectionFunctionAbstract $reflector)
    {
        $dependencies = array();

        foreach ($reflector->getParameters() as $parameter) {
            
            if (array_key_exists($name = $parameter->getType()->getName(), $parameters)) {
                $dependencies[] = $parameters[$name];

                unset($parameters[$name]);
            }

            // The dependency does not exists in parameters.
            else if (! is_null($class = $parameter->getType())) {
                $className = $class->getName();

                $dependencies[] = $this->make($className);
            }

            // The dependency does not reference a class.
            else if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            }
        }

        return array_merge($dependencies, $parameters);
    }

    /**
     * Parse a Class@method style callback into class and method.
     *
     * @param  string  $callback
     * @param  string  $default
     * @return array
     */
    protected function parseCallback($callback, $default)
    {
        return array_pad(explode('@', $callback, 2), 2, $default);
    }

    /**
     * Resolve a given type to an instance.
     *
     * @param  string  $abstract
     * @param  array   $parameters
     * @return mixed
     */
    public function make($abstract, $parameters = array())
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (! isset($this->bindings[$abstract])) {
            $concrete = $abstract;
        } else {
            $concrete = Arr::array_get($this->bindings[$abstract], 'concrete', $abstract);
        }

        if (($concrete == $abstract) || ($concrete instanceof Closure)) {
            $object = $this->build($concrete, $parameters);
        } else {
            $object = $this->make($concrete);
        }

        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }

        $this->resolved[$abstract] = true;

        return $object;
    }

    /**
     * Instantiate an instance of the given type.
     *
     * @param  string  $abstract
     * @param  array   $parameters
     * @return mixed
     */
    protected function build($abstract, $parameters = array())
    {
        if ($abstract instanceof Closure) {
            return call_user_func($abstract, $this, $parameters);
        }

        $reflector = new ReflectionClass($abstract);

        if ( ! $reflector->isInstantiable()) {
            throw new BindingResolutionException("Resolution target [$abstract] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $abstract();
        }

        $dependencies = $this->getDependencies(
            $dependencies = $constructor->getParameters(), $this->parseParameters($dependencies, $parameters)
        );

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param  array  $parameters
     * @param  array  $arguments that might have been passed into our resolve
     * @return array
     */
    protected function getDependencies($parameters, $arguments)
    {
        $dependencies = array();

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getType();

            if (array_key_exists($className = $parameter->getType()->getName(), $arguments)) {
                $dependencies[] = $arguments[$className];
            }

            // No arguments given.
            else if (is_null($dependency)) {
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }

        return (array) $dependencies;
    }

    /**
     * Resolves optional parameters for our dependency injection.
     *
     * @param ReflectionParameter
     * @return default value
     */
    protected function resolveNonClass($parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new BindingResolutionException("Unresolvable dependency resolving [$parameter].");
    }

    /**
     * Resolve a class based dependency from the container.
     *
     * @param  \ReflectionParameter  $parameter
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function resolveClass($parameter)
    {
        try {
            return $this->make($parameter->getType()->getName());
        }
        catch (BindingResolutionException $e) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }

            throw $e;
        }
    }

    /**
     * If extra parameters are passed by numeric ID, rekey them by argument name.
     *
     * @param  array  $dependencies
     * @param  array  $parameters
     * @return array
     */
    protected function parseParameters(array $dependencies, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (is_numeric($key)) {
                unset($parameters[$key]);

                $name = $dependencies[$key]->getName();

                $parameters[$name] = $value;
            }
        }

        return $parameters;
    }

    /**
     * Determine if a given type is shared.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function isShared($abstract)
    {
        $shared = isset($this->bindings[$abstract]['shared']) ? $this->bindings[$abstract]['shared'] : false;

        return isset($this->instances[$abstract]) || ($shared === true);
    }

    /**
     * Get the alias for an abstract if available.
     *
     * @param  string  $abstract
     * @return string
     */
    protected function getAlias($abstract)
    {
        return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
    }

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param  \Mini\Container\Container  $container
     * @return void
     */
    public static function setInstance(Container $container)
    {
        static::$instance = $container;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key): bool 
    {
        return isset($this->bindings[$key]);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key): mixed
    {
        return $this->make($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if (! $value instanceof Closure) {
            $value = function() use ($value)
            {
                return $value;
            };
        }

        $this->bind($key, $value);
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]);
    }

    /**
     * Dynamically access container services.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * Dynamically set container services.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }

}