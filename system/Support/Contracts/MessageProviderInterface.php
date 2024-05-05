<?php

declare(strict_types=1);

namespace Npds\Support\Contracts;


interface MessageProviderInterface
{
    /**
     * Get the messages for the instance.
     *
     * @return \Npds\Support\MessageBag
     */
    public function getMessageBag();
}
