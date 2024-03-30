<?php

declare(strict_types=1);

namespace npds\system\exception;

use Throwable;
use TypeError;
use ParseError;
use ErrorException;
use ReflectionProperty;

class FatalThrowableError extends ErrorException
{

    /**
     * [__construct description]
     *
     * @param   Throwable  $e  [$e description]
     *
     * @return  [type]         [return description]
     */
    public function __construct(Throwable $e)
    {
        if ($e instanceof ParseError) {
            $message = 'Parse error: ' .$e->getMessage();
            $severity = E_PARSE;
        } else if ($e instanceof TypeError) {
            $message = 'Type error: ' .$e->getMessage();
            $severity = E_RECOVERABLE_ERROR;
        } else {
            $message = $e->getMessage();
            $severity = E_ERROR;
        }

        ErrorException::__construct(
            $message,
            $e->getCode(),
            $severity,
            $e->getFile(),
            $e->getLine()
        );

        $this->setTrace($e->getTrace());
    }

    /**
     * [setTrace description]
     *
     * @param   [type]  $trace  [$trace description]
     *
     * @return  [type]          [return description]
     */
    protected function setTrace($trace)
    {
        $traceReflector = new ReflectionProperty('Exception', 'trace');

        $traceReflector->setAccessible(true);

        $traceReflector->setValue($this, $trace);
    }
}