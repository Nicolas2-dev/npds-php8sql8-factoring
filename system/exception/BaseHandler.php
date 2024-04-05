<?php

declare(strict_types=1);

namespace npds\system\exception;

use Exception;
use ErrorException;

use npds\system\exception\HttpException;
use npds\system\exception\FatalThrowableError;


class BaseHandler
{
    /**
     * The current Handler instance.
     *
     * @var 
     */
    protected static $instance;

    /**
     * Whether or not we are in DEBUG mode.
     */
    protected $debug = false;


    /**
     * Create a new Exceptions Handler instance.
     *
     * @return void
     */
    public function __construct($debug)
    {
        $this->debug =  $debug; 
    }

    /**
     * Bootstrap the Exceptions Handler.
     *
     * @return void
     */
    public static function initialize($debug)
    {
        static::$instance = $instance = new static($debug);

        // Setup the Exception Handlers.
        set_error_handler(array($instance, 'handleError'));

        set_exception_handler(array($instance, 'handleException'));

        register_shutdown_function(array($instance, 'handleShutdown'));
    }

    /**
     * Convert a PHP error to an ErrorException.
     *
     * @param  int  $level
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  array  $context
     * @return void
     *
     * @throws ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = array())
    {
        if (error_reporting() & ($level > 0)) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handle an uncaught exception from the application.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function handleException($e)
    {
        if (! $e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }   

        if (! $e instanceof HttpException) {
            $this->report($e);
        }

        $this->render($e);
    }

    /**
     * Report or log an exception.
     *
     * @param  Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        //
    }

    /**
     * Render an exception as an HTTP response and send it.
     *
     * @param  Exception  $e
     * @return void
     */
    public function render(Exception $e)
    {
        $type = $this->debug ? 'Debug' : 'Default';

        if ($type === 'Debug') {
            $this->error_debug($e);
        } else {
            $this->error_default();
        }
    }

    /**
     * [error_default description]
     *
     * @return  [type]  [return description]
     */
    public function error_default()
    {
        global $pdst;

        echo '<div class="row page-header">
                <h1>Whoops!</h1>
            </div>

            <div class="row">
                <h2 class="text-center"><strong>Whoops! An error occurred.</strong></h2>
            </div>';
        
        if(isset($pdst)) {
            include("themes/default/footer.php"); 
        }               
    }

    /**
     * [debug description]
     *
     * @param   Exception  $e
     *
     * @return  void
     */
    public function error_debug(Exception  $e) 
    {
        global $pdst;

        echo '<div class="row page-header">
            <h1>Whoops!</h1>
        </div>
        
        <div class="row">
            <p>
                '.$e->getMessage() .' in '. $e->getFile() .' on line '. $e->getLine() .'
            </p>
            <br>
            <pre>'.  $e->getTraceAsString() .'</pre>
        </div>';
        
        if(isset($pdst)) {
            include("themes/default/footer.php"); 
        }
    }

    /**
     * Handle the PHP shutdown event.
     *
     * @return void
     */
    public function handleShutdown()
    {
        if (! is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalExceptionFromError($error));
        }
    }

    /**
     * Create a new fatal exception instance from an error array.
     *
     * @param  array  $error
     * @param  int|null  $traceOffset
     * @return ErrorException
     */
    protected function fatalExceptionFromError(array $error)
    {
        return new ErrorException(
            $error['message'], $error['type'], 0, $error['file'], $error['line']
        );
    }

    /**
     * Determine if the error type is fatal.
     *
     * @param  int  $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE));
    }
}