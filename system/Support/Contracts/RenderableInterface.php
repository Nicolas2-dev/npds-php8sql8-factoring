<?php

declare(strict_types=1);

namespace Npds\Support\Contracts;


interface RenderableInterface
{
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();
}
