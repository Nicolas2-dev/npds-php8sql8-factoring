<?php

declare(strict_types=1);

namespace Npds\Support\Facades;


/**
* @see \Npds\Translation\Translator
*/
class Lang extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'translator'; }
}
