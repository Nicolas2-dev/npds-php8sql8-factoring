<?php

declare(strict_types=1);

namespace Npds\Support\Facades;


/**
 * @see \Npds\View\Factory
 * @see \Npds\View\View
 */
class View extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'view'; }
}
