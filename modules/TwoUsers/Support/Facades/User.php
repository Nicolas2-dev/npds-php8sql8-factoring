<?php

namespace Modules\TwoUsers\Support\Facades;

use Two\Support\Facades\Facade;


class User extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'two_user'; }
}
