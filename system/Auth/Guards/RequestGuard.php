<?php

declare(strict_types=1);

namespace Npds\Auth;

use Npds\Auth\Guard;
use Npds\Http\Request;


class RequestGuard extends Guard
{
    /**
     * The guard callback.
     *
     * @var callable
     */
    protected $callback;

    /**
     * The request instance.
     *
     * @var \Npds\Http\Request
     */
    protected $request;


    /**
     * Create a new authentication guard.
     *
     * @param  callable  $callback
     * @param  \Npds\Http\Request  $request
     * @return void
     */
    public function __construct(callable $callback, Request $request)
    {
        $this->request  = $request;
        $this->callback = $callback;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Npds\Auth\UserInterface|null
     */
    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        return $this->user = call_user_func($this->callback, $this->request);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = array())
    {
        $guard = new static($this->callback, $credentials['request']);

        return ! is_null($guard->user());
    }

    /**
     * Set the current request instance.
     *
     * @param  \Npds\Http\Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
