<?php

namespace Modules\TwoBlocks\Support\Facades;

use Two\Support\Facades\Facade;


class Block extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two_block'; }
}
