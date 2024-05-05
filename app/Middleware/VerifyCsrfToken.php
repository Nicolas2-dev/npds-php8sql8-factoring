<?php

namespace App\Middleware;

use Npds\Http\Request;
use Npds\Session\TokenMismatchException;

use Closure;


class VerifyCsrfToken
{

    public function handle(Request $request, Closure $next)
    {
        $session = $request->session();

        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if ($session->token() !== $token) {
            throw new TokenMismatchException();
        }

        return $next($request);
    }
}
