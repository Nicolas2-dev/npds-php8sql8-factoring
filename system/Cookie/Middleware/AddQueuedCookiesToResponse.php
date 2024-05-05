<?php

declare(strict_types=1);

namespace Npds\Cookie\Middleware;

use Npds\Foundation\Application;

use Closure;


class AddQueuedCookiesToResponse
{
    /**
     * The cookie jar instance.
     *
     * @var \Npds\Cookie\CookieJar
     */
    protected $cookies;

    /**
     * Create a new CookieQueue instance.
     *
     * @param  \Npds\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->cookies = $app['cookie'];
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Npds\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        //
        $cookies = $this->cookies->getQueuedCookies();

        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
