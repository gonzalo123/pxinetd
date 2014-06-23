<?php

namespace G\Pxi\Events;

use Symfony\Component\EventDispatcher\Event;

class OnError extends Event
{
    private $exception;
    private $port;

    public function __construct(\Exception $e, $port)
    {
        $this->exception = $e;
        $this->port      = $port;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function getPort()
    {
        return $this->port;
    }
}