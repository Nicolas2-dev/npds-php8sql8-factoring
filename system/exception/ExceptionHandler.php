<?php

declare(strict_types=1);

namespace npds\system\exception;

use Exception;
use npds\system\exception\HttpException;

class ExceptionHandler extends BaseHandler
{

    /**
     * [report description]
     *
     * @param   Exception  $e  [$e description]
     *
     * @return  [type]         [return description]
     */
    public function report(Exception $e)
    {
        $message = $e->getMessage();

        $code = $e->getCode();
        $file = $e->getFile();
        $line = $e->getLine();

        $trace = $e->getTraceAsString();

        $date = date('M d, Y G:iA');

        $message = "Exception information:\n
    Date: {$date}\n
    Message: {$message}\n
    Code: {$code}\n
    File: {$file}\n
    Line: {$line}\n
    Stack trace:\n
{$trace}\n
---------\n\n";

        //
        $path = 'storage/framwork/errors.log';

        file_put_contents($path, $message, FILE_APPEND);
    }

    /**
     * Render an exception as an HTTP response and send it.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function render(Exception $e)
    {
        // Http Error Pages.
        if ($e instanceof HttpException) {
             $code = $e->getStatusCode();

        //     if (View::exists('Errors/' .$code)) {
        //         $view = View::make('Layouts/Default')
        //             ->shares('title', 'Error ' .$code)
        //             ->nest('content', 'Errors/' .$code, array('exception' => $e));

        //         echo $view->render();

        //         return;
        //     }
        echo 'http error : '.$code;
        }

        parent::render($e);
    }
}