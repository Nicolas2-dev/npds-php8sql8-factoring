<?php

declare(strict_types=1);

namespace Npds\Support\Contracts;


interface ArrayableInterface
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}
