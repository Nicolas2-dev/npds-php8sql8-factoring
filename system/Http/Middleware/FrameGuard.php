<?php

declare(strict_types=1);

namespace Npds\Http\Middleware;

use Closure;


class FrameGuard
{
    /**
     * Handle the given request and get the response.
     *
     * @param  \Npds\Http\Request  $request
     * @param  \Closure  $next
     * @return \Npds\Http\Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);

        return $response;
    }
}
