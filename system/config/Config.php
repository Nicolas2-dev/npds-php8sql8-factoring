<?php

declare(strict_types=1);

namespace Npds\Config;

use ArrayAccess;

use Npds\Support\Arr;


class Config implements ArrayAccess
{

    /**
     * @var array
     */
    protected $items = array();


    /**
     * Return true if the key exists.
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::array_has($this->items, $key);
    }

    /**
     * Get the value.
     * @param string $key
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return Arr::array_get($this->items, $key, $default);
    }

    /**
     * Set the value.
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        Arr::array_set($this->items, $key, $value);
    }

    /**
     * Forget the value.
     * @param string $key
     */
    public function forget($key)
    {
        Arr::array_forget($this->items, $key);
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key): mixed
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        $this->forget($key);
    }

}
