<?php

declare(strict_types=1);

namespace Npds\Foundation\Exceptions;

use Exception;
use Throwable;

use Npds\Http\Request;
use Npds\Http\Response;
use Npds\Container\Container;
use Npds\Auth\AuthenticationException;
use Npds\Debug\Exception\FlattenException;
use Npds\Debug\Exception\FatalThrowableError;
use Npds\Debug\ExceptionHandler as SymfonyExceptionHandler;

use Psr\Log\LoggerInterface;


class Handler
{
    /**
     * The Container instance.
     *
     * @var \Npds\Container\Container
     */
    protected $container;

    /**
     * Whether or not we are in DEBUG mode.
     */
    protected $debug = false;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = array();


    /**
     * Create a new Exceptions Handler instance.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        //
        $this->debug = $container['config']->get('app.debug', true);
    }

    /**
     * Handle an uncaught exception from the application.
     *
     * @param  \Npds\Http\Request
     * @param  \Exception|\Throwable  $exception
     * @return void
     */
    public function handleException(Request $request, $exception)
    {
        if (! $exception instanceof Exception) {
            $exception = new FatalThrowableError($exception);
        }

        $this->report($exception);

        return $this->render($exception, $request);
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (! $this->shouldReport($exception)) {
            return;
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);

            $logger->error($exception);
        }
        catch (Exception $ex) {
            throw $exception; // Throw the original exception
        }
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param  \Exception  $exception
     * @return bool
     */
    public function shouldReport(Exception $exception)
    {
        $shouldReport = true;

        foreach ($this->dontReport as $type) {
            if ($exception instanceof $type) {
                $shouldReport = false;

                break;
            }
        }

        return $shouldReport;
    }

    /**
     * Render an exception as an HTTP response and send it.
     *
     * @param  \Exception  $e
     * @param  \Mini\Http\Request
     * @return void
     */
    public function render(Exception $exception, Request $request)
    {
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        $exception = FlattenException::create($exception);

        $handler = new SymfonyExceptionHandler($this->debug);

        return new Response(
            $handler->getHtml($exception), $exception->getStatusCode(), $exception->getHeaders()
        );
    }

    /**
     * Render an exception for console.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function renderForConsole(Exception $exception)
    {
        $message = sprintf(
            "%s: %s in file %s on line %d%s\n",
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        echo $message;
    }
}