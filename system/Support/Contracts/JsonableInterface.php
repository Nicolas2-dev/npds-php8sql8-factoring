<?php

declare(strict_types=1);

namespace Npds\Support\Contracts;


interface JsonableInterface
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0);
}
