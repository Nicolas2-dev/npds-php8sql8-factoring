<?php

declare(strict_types=1);

namespace npds\system\exception;

use Exception;
use RuntimeException;


class HttpException extends RuntimeException
{
    /**
     * [$statusCode description]
     *
     * @var [type]
     */
    private $statusCode;


    /**
     * [__construct description]
     *
     * @param   [type]     $statusCode  [$statusCode description]
     * @param   [type]     $message     [$message description]
     * @param   Exception  $previous    [$previous description]
     * @param   [type]     $code        [$code description]
     *
     * @return  [type]                  [return description]
     */
    public function __construct($statusCode, $message = null, Exception $previous = null, $code = 0)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code, $previous);
    }

    /**
     * [getStatusCode description]
     *
     * @return  [type]  [return description]
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}