<?php

declare(strict_types=1);

namespace Npds\Foundation;

use Closure;
use Exception;
use Throwable;

use Npds\Support\Arr;
use Npds\Http\Request;
use Npds\Container\Container;
use Npds\Foundation\Pipeline;
use Npds\Log\LogServiceProvider;
use Npds\Support\ServiceProvider;
use Npds\Events\EventServiceProvider;
use Npds\Foundation\ProviderRepository;
use Npds\Debug\Exception\FatalThrowableError;
use Npds\Exceptions\ExceptionServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;


class Application extends Container
{


    /**
     * The Framework Version.
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * The array of booting callbacks.
     *
     * @var array
     */
    protected $bootingCallbacks = array();

    /**
     * The array of booted callbacks.
     *
     * @var array
     */
    protected $bootedCallbacks = array();

    /**
     * The array of finish callbacks.
     *
     * @var array
     */
    protected $terminatingCallbacks = array();

    /**
     * All of the registered service providers.
     *
     * @var array
     */
    protected $serviceProviders = array();

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = array();

    /**
     * The deferred services and their providers.
     *
     * @var array
     */
    protected $deferredServices = array();


    /**
     * Create a new application instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->registerBaseServiceProviders();

        $this->registerCoreContainerAliases();
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public static function version()
    {
        return static::VERSION;
    }

    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));

        $this->register(new LogServiceProvider($this));

        $this->register(new ExceptionServiceProvider($this));
    }

    /**
     * Bind the installation paths to the application.
     *
     * @param  array  $paths
     * @return string
     */
    public function bindInstallPaths(array $paths)
    {
        $this->instance('path', realpath($paths['app']));

        foreach ($paths as $key => $value) {
            $this->instance("path.{$key}", realpath($value));
        }
    }

    /**
     * Start the exception handling for the request.
     *
     * @return void
     */
    public function startExceptionHandling()
    {
        $this['exception']->register();
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isBooted()) {
            return;
        }

        foreach ($this->serviceProviders as $provider) {
            $this->bootProvider($provider);
        };

        $this->fireAppCallbacks($this->bootingCallbacks);

        $this->booted = true;

        $this->fireAppCallbacks($this->bootedCallbacks);
    }

    /**
     * Boot the given service provider.
     *
     * @param  \Npds\Support\ServiceProvider  $provider
     * @return mixed
     */
    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            $this->call(array($provider, 'boot'));
        }
    }

    /**
     * Register a new boot listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booting($callback)
    {
        $this->bootCallbacks[] = $callback;
    }

    /**
     * Register a new "booted" listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booted($callback)
    {
        $this->bootedCallbacks[] = $callback;

        if ($this->isBooted()) {
            $this->fireAppCallbacks(array($callback));
        }
    }

    /**
     * Call the booting callbacks for the application.
     *
     * @param  array  $callbacks
     * @return void
     */
    protected function fireAppCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * Run the application.
     *
     * @param  \Npds\Http\Request|null  $request
     *
     * @return \Npds\Http\Response
     */
    public function run(Request $request = null)
    {
        if (is_null($request)) {
            $request = Request::createFromGlobals();
        }

        $request->enableHttpMethodParameterOverride();

        try {
            $response = $this->sendRequestThroughRouter($request);
        }
        catch (Exception | Throwable $e) {
            $response = $this->handleException($request, $e);
        }

        $response->send();

        $this->shutdown($request, $response);
    }

    /**
     * Handle an exception or throwable error occured while dispatching the HTTP request.
     *
     * @param  \Npds\Http\Request  $request
     * @param  \Exception|\Throwable  $exception
     *
     * @return \Npds\Http\Response
     */
    protected function handleException(Request $request, $exception)
    {
        if (! $exception instanceof Exception) {
            $exception = new FatalThrowableError($exception);
        }

        $handler = $this->make('Npds\Foundation\Exceptions\HandlerInterface');

        if (! $exception instanceof HttpException) {
            $handler->report($exception);
        }

        return $handler->render($exception, $request);
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param  \Npds\Http\Request  $request
     */
    protected function sendRequestThroughRouter(Request $request)
    {
        $this->instance('request', $request);

        $this->boot();

        $router = $this->make('router');

        $pipeline = new Pipeline(
            $this, $this->config->get('app.middleware', array())
        );

        $response = $pipeline->dispatch($request, function ($request) use ($router)
        {
            $this->instance('request', $request);

            return $router->dispatch($request);
        });

        return $router->prepareResponse($request, $response);
    }

    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  \Npds\Http\Request  $request
     * @param  \Npds\Http\Response  $response
     * @return void
     */
    public function shutdown(Request $request, $response)
    {
        $middleware = $this->config->get('app.middleware', array());

        if (! is_null($route = $request->route())) {
            $middleware = array_merge(
                $this->router->gatherMiddleware($route), $middleware
            );
        }

        foreach ($middleware as $value) {
            if (! is_string($value)) {
                continue;
            }

            $name = head(explode(':', $value, 2));

            if (method_exists($instance = $this->make($name), 'terminate')) {
                $instance->terminate($request, $response);
            }
        }

        $this->terminate();
    }

    /**
     * Register a terminating callback with the application.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function terminating(Closure $callback)
    {
        $this->terminatingCallbacks[] = $callback;

        return $this;
    }

    /**
     * Call the "terminating" callbacks assigned to the application.
    *
     * @return void
     */
    public function terminate()
    {
        $this->fireAppCallbacks($this->terminatingCallbacks);
    }

    /**
     * Register a service provider with the application.
     *
     * @param  \Npds\Support\ServiceProvider|string  $provider
     * @param  array  $options
     * @param  bool  $force
     * @return \Npds\Support\ServiceProvider
     */
    public function register($provider, $options = array(), $force = false)
    {
        if (! is_null($registered = $this->getRegistered($provider)) && ! $force) {
            return $registered;
        }

        if (is_string($provider)) {
            $provider = new $provider($this);
        }

        $provider->register();

        foreach ($options as $key => $value) {
            $this[$key] = $value;
        }

        $this->markAsRegistered($provider);

        if ($this->isBooted()) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    /**
     * Get the registered service provider instnace if it exists.
     *
     * @param  \Npds\Support\ServiceProvider|string  $provider
     * @return \Npds\Support\ServiceProvider|null
     */
    public function getRegistered($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        if (! array_key_exists($name, $this->loadedProviders)) {
            return;
        }

        return Arr::array_first($this->serviceProviders, function ($key, $value) use ($name)
        {
            return get_class($value) == $name;
        });
    }

    /**
     * Mark the given provider as registered.
     *
     * @param  \Npds\Support\ServiceProvider
     * @return void
     */
    protected function markAsRegistered($provider)
    {
        $this->events->dispatch($class = get_class($provider), array($provider));

        //
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[$class] = true;
    }

    /**
     * Load and boot all of the remaining deferred providers.
     *
     * @return void
     */
    public function loadDeferredProviders()
    {
        foreach ($this->deferredServices as $service => $provider) {
            $this->loadDeferredProvider($service);
        }

        $this->deferredServices = array();
    }

    /**
     * Load the provider for a deferred service.
     *
     * @param  string  $service
     * @return void
     */
    protected function loadDeferredProvider($service)
    {
        $provider = $this->deferredServices[$service];

        if (! isset($this->loadedProviders[$provider])) {
            $this->registerDeferredProvider($provider, $service);
        }
    }

    /**
     * Register a deffered provider and service.
     *
     * @param  string  $provider
     * @param  string  $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null)
    {
        if (! is_null($service)) {
            unset($this->deferredServices[$service]);
        }

        $this->register($instance = new $provider($this));

        if ($this->isBooted()) {
            return;
        }

        $this->booting(function() use ($instance)
        {
            $this->bootProvider($instance);
        });
    }

    /**
     * Resolve the given type from the container.
     *
     * (Overriding \Npds\Container::make)
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function make($abstract, $parameters = array())
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->deferredServices[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }

        return parent::make($abstract, $parameters);
    }

    /**
     * Set the application's deferred services.
     *
     * @param  array  $services
     * @return void
     */
    public function setDeferredServices(array $services)
    {
        $this->deferredServices = $services;
    }

    /**
     * Get the service provider repository instance.
     *
     * @return ProviderRepository
     */
    public function getProviderRepository()
    {
        $manifest = $this->config->get('app.manifest', rtrim(STORAGE_PATH, DS));

        return new ProviderRepository($manifest);
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        $aliases = array(
            'app'             => array('Npds\Foundation\Application', 'Npds\Container\Container'),
            'config'          => 'Npds\Config\Config',
            'cookie'          => 'Npds\Cookie\CookieJar',
            'encrypter'       => 'Npds\Encryption\Encrypter',
            'db'              => 'Npds\Database\DatabaseManager',
            'events'          => 'Npds\Events\Dispatcher',
            'hash'            => 'Npds\Hashing\HasherInterface',
            'log'             => array('Npds\Log\Writer', 'Psr\Log\LoggerInterface'),
            'redirect'        => 'Npds\Routing\Redirector',
            'request'         => 'Npds\Http\Request',
            'router'          => 'Npds\Routing\Router',
            'session'         => 'Npds\Session\SessionManager',
            'session.store'   => 'Npds\Session\Store',
            'url'             => 'Npds\Routing\UrlGenerator',
            'view'            => 'Npds\View\Factory',
        );

        foreach ($aliases as $key => $value) {
            foreach ((array) $value as $alias) {
                $this->alias($key, $alias);
            }
        }
    }

    /**
     * Set the application request for the console environment.
     *
     * @return void
     */
    public function setRequestForConsoleEnvironment()
    {
        $url = $this->config->get('app.url', 'http://localhost');

        $request = Request::create($url, 'GET', array(), array(), array(), $_SERVER);

        $this->instance('request', $request);
    }

}
