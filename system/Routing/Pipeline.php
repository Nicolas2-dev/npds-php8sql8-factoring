<?php

declare(strict_types=1);

namespace Npds\Routing;


use Closure;
use Exception;
use Throwable;

use Npds\Foundation\Exceptions\HandlerInterface as ExceptionHandler;
use Npds\Http\Request;
use Npds\Foundation\Pipeline as BasePipeline;


class Pipeline extends BasePipeline
{
    /**
     * Get the final piece of the Closure onion.
     *
     * @param  \Closure  $callback
     * @return \Closure
     */
    protected function prepareDestination(Closure $callback)
    {
        return function ($passable) use ($callback)
        {
            try {
                return call_user_func($callback, $passable);
            }
            catch (Exception | Throwable $exception) {
                return $this->handleException($passable, $exception);
            }
        };
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @param  \Closure  $stack
     * @param  mixed  $pipe
     * @return \Closure
     */
    protected function createSlice($stack, $pipe)
    {
        return function ($passable) use ($stack, $pipe)
        {
            try {
                return $this->call($pipe, $passable, $stack);
            }
            catch (Exception | Throwable $exception) {
                return $this->handleException($passable, $exception);
            }
        };
    }

    /**
     * Handle the given exception.
     *
     * @param  mixed  $passable
     * @param  \Exception  $exception
     * @return mixed
     *
     * @throws \Exception
     */
    protected function handleException($passable, $exception)
    {
        if ((! $passable instanceof Request) || ! $this->container->bound(ExceptionHandler::class)) {
            throw $exception;
        }

        $handler = $this->container->make(ExceptionHandler::class);

        return $handler->handleException($passable, $exception);
    }
}
