<?php

declare(strict_types=1);

namespace Npds\Support\Facades;


/**
* @see \Npds\Filesystem\Filesystem
*/
class File extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'files'; }
}
