<?php

namespace G\Pxi\Events;

use Symfony\Component\EventDispatcher\Event;

class OnMessage extends Event
{
    private $msg;
    private $port;

    public function __construct($msg, $port)
    {
        $this->msg  = $msg;
        $this->port = $port;
    }

    public function getMessage()
    {
        return $this->msg;
    }

    public function getPort()
    {
        return $this->port;
    }
}