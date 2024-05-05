<?php

declare(strict_types=1);

namespace Npds\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface as BaseSessionInterface;


interface SessionInterface extends BaseSessionInterface
{
    /**
     * Get the session handler instance.
     *
     * @return \SessionHandlerInterface
     */
    public function getHandler();

    /**
     * Set the "previous" URL in the session.
     *
     * @param  string  $url
     * @return void
     */
    public function setPreviousUrl($url);

}
