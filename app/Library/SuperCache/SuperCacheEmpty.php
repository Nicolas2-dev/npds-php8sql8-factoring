<?php

declare(strict_types=1);

namespace App\Library\Supercache;


class SuperCacheEmpty
{

    /**
     * [$instance description]
     *
     * @var SuperCacheEmpty
     */
    private static ?SuperCacheEmpty  $instance = null;

    /**
     * [$genereting_output description]
     *
     * @var [type]
     */
    public int $genereting_output;

    /**
     * [__construct description]
     *
     */
    public function __construct()
    {
        $this->genereting_output = 0;
    }

    /**
     * instance SuperCacheEmpty
     *
     * @return SuperCacheEmpty
     */
    public static function setInstance(): SuperCacheEmpty
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Get singleton instance
     *
     * @return SuperCacheEmpty
     */
    public static function getInstance(): SuperCacheEmpty
    {
        return static::$instance;
    }

}