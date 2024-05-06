<?php

namespace App\Events;

use Npds\Http\Request;


class RefererUpdateEvent
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