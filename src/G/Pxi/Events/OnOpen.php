<?php

namespace G\Pxi\Events;

use Symfony\Component\EventDispatcher\Event;

class OnOpen extends Event
{
    private $port;

    public function __construct($port)
    {
        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }
}