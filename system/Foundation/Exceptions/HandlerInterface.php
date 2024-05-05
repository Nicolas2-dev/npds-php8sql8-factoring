<?php

declare(strict_types=1);

namespace Npds\Foundation\Exceptions;

use Exception;

use Npds\Http\Request;


interface HandlerInterface
{

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e);

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Exception  $e
     * @param  \Npds\Http\Request  $request
     * @return \Npds\Http\Response
     */
    public function render(Exception $e, Request $request);
}