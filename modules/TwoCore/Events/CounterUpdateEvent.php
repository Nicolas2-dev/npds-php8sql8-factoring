<?php

namespace Modules\TwoCore\Events;


use Two\Http\Request;


class CounterUpdateEvent
{

    /**
     * 
     */
    public $request;


    /**
     * Create a new Event instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

}
