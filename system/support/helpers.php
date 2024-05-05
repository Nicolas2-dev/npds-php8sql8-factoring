<?php

declare(strict_types=1);


use Npds\Container\Container;
use Npds\Support\Str;
use Npds\Support\Facades\Config;

use Symfony\Component\VarDumper\VarDumper;


if (! function_exists('site_url')) {
    /**
     * Site URL helper
     *
     * @param string $path
     * @return string
     */
    function site_url()
    {
        $url = Config::get('app.url');

        if (empty($parameters = func_get_args())) {
            return $url;
        }

        $path = array_shift($parameters);

        $result = preg_replace_callback('#\{(\d+)\}#', function ($matches) use ($parameters)
        {
            $key = (int) $matches[1];

            return isset($parameters[$key]) ? $parameters[$key] : $matches[0];

        }, $path);

        return $url .ltrim($result, '/');
    }
}

if (! function_exists('asset_url')) {
    /**
     * Asset URL helper
     * @param string $path
     * @return string
     */
    function asset_url($path)
    {
        $url = Config::get('app.url');

        return $url .'assets/' .ltrim($path, '/');
    }
}

if (! function_exists('app')) {
    /**
     * Get the root Facade application instance.
     *
     * @param  string  $make
     * @return mixed
     */
    function app($make = null)
    {
        $container = Container::getInstance();

        if (! is_null($make)) {
            return $container->make($make);
        }

        return $container;
    }
}

if (! function_exists('app_path')) {
    /**
    * Get the path to the App folder.
    *
    * @param   string  $path
    * @return  string
    */
    function app_path($path = '')
    {
        $basePath = app('path');

        if (empty($path)) {
            return $basePath;
        }

        return $basePath .DS .str_replace('/', DS, $path);
    }
}

if (! function_exists('base_path'))
{
    /**
     * Obtenez le chemin d'accès à la base de l'installation.
     *
     * @param  string  $path
     * @return string
     */
    function base_path($path = '')
    {
        $basePath = app('path.base');
        
        if (empty($path)) {
            return $basePath;
        }

        return $basePath .DS .str_replace('/', DS, $path);
    }
}

if ( ! function_exists('storage_path')) {
    /**
    * Get the path to the storage folder.
    *
    * @param   string  $path
    * @return  string
    */
    function storage_path($path = '')
    {
        $basePath = app('path.storage');

        if (empty($path)) {
            return $basePath;
        }

        return $basePath .DS .str_replace('/', DS, $path);
    }
}

if (! function_exists('camel_case')) {
    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    function camel_case($value)
    {
        return Str::camel($value);
    }
}

if (! function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

if (! function_exists('config')) {
    /**
     * Get the specified configuration value.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }

        return app('config')->get($key, $default);
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    function csrf_token()
    {
        $session = app('session');

        if (isset($session)) {
            return $session->token();
        }

        throw new RuntimeException("Application session store not set.");
    }
}

if (! function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        foreach (func_get_args() as $value) {
            VarDumper::dump($value);
        }

        exit(1);
    }
}

if (! function_exists('e'))
{
    /**
     * Escape HTML entities in a string.
     *
     * @param  string  $value
     * @return string
     */
    function esc($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (! function_exists('ends_with')) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string  $haystack
     * @param string|array  $needle
     * @return bool
     */
    function ends_with($haystack, $needle)
    {
        return Str::endsWith($haystack, $needle);
    }
}

if (! function_exists('sanitize'))
{
    function sanitize($data, $filter)
    {
        switch ($filter) {
            case 'string':
                return filter_var($data, FILTER_SANITIZE_STRING);

            case 'email':
                return filter_var($data, FILTER_SANITIZE_EMAIL);

            case 'integer':
                return filter_var($data, FILTER_SANITIZE_NUMBER_INT);

            case 'float':
                return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);

            case 'url':
                return filter_var($data, FILTER_SANITIZE_URL);
        }

        throw new InvalidArgumentException('Filter sanitize unknown.');
    }
}

if (! function_exists('snake_case')) {
    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    function snake_case($value, $delimiter = '_')
    {
        return Str::snake($value, $delimiter);
    }
}

if (! function_exists('starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needle
     * @return bool
     */
    function starts_with($haystack, $needle)
    {
        return Str::startsWith($haystack, $needle);
    }
}

if (! function_exists('str_contains')) {
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needle
     * @return bool
     */
    function str_contains($haystack, $needle)
    {
        return Str::contains($haystack, $needle);
    }
}

if (! function_exists('str_finish')) {
    /**
     * Cap a string with a single instance of a given value.
     *
     * @param  string  $value
     * @param  string  $cap
     * @return string
     */
    function str_finish($value, $cap)
    {
        return Str::finish($value, $cap);
    }
}

if (! function_exists('str_is')) {
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string  $pattern
     * @param  string  $value
     * @return bool
     */
    function str_is($pattern, $value)
    {
        return Str::is($pattern, $value);
    }
}

if (! function_exists('str_limit')) {
    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    function str_limit($value, $limit = 100, $end = '...')
    {
        return Str::limit($value, $limit, $end);
    }
}

if (! function_exists('str_plural')) {
    /**
     * Get the plural form of an English word.
     *
     * @param  string  $value
     * @param  int  $count
     * @return string
     */
    function str_plural($value, $count = 2)
    {
        return Str::plural($value, $count);
    }
}


if (! function_exists('str_random')) {
    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int     $length
     * @return string
     *
     * @throws \RuntimeException
     */
    function str_random($length = 16)
    {
        return Str::random($length);
    }
}

if (! function_exists('str_replace_array'))
{
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string  $search
     * @param  array   $replace
     * @param  string  $subject
     * @return string
     */
    function str_replace_array($search, array $replace, $subject)
    {
        foreach ($replace as $value) {
            $subject = preg_replace('/' .$search .'/', $value, $subject, 1);
        }

        return $subject;
    }
}

if (! function_exists('str_singular')) {
    /**
     * Get the singular form of an English word.
     *
     * @param  string  $value
     * @return string
     */
    function str_singular($value)
    {
        return Str::singular($value);
    }
}

if (! function_exists('studly_case')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    function studly_case($value)
    {
        return Str::studly($value);
    }
}

if (! function_exists('__'))
{
    /**
     * Récupérez le message formaté et traduit.
     *
     * @param string $message English default message
     * @param mixed $args
     * @return string|void
     */
    function __($message, $args = null)
    {
        if (! $message) return '';

        //
        $params = (func_num_args() === 2) ? (array)$args : array_slice(func_get_args(), 1);

        return app('translator')->instance()->translate($message, $params);
    }
}

if (! function_exists('url'))
{
    /**
     * Generate a url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @param  bool    $secure
     * @return string
     */
    function url($path = null, $parameters = array(), $secure = null)
    {
        return app('url')->to($path, $parameters, $secure);
    }
}

if (! function_exists('windows_os')) {
    /**
     * Déterminez si l'environnement actuel est basé sur Windows.
     *
     * @return bool
     */
    function windows_os()
    {
        return strtolower(substr(PHP_OS, 0, 3)) === 'win';
    }
}

if (! function_exists('head')) {
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param  array  $array
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}

if (! function_exists('last')) {
    /**
     * Get the last element from an array.
     *
     * @param  array  $array
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return ($value instanceof Closure) ? call_user_func($value) : $value;
    }
}

if (! function_exists('with')) {
    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed  $object
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }
}
