<?php

namespace G\Pxi;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use G\Pxi\Events;

class Message implements MessageComponentInterface
{
    private $callback;
    private $dispatcher;
    private $port;

    public function __construct(callable $callback, EventDispatcherInterface $dispatcher, $port)
    {
        $this->port       = $port;
        $this->callback   = $callback;
        $this->dispatcher = $dispatcher;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->dispatcher->dispatch(Events::ON_OPEN, new Events\OnOpen($this->port));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->dispatcher->dispatch(Events::ON_MESSAGE, new Events\OnMessage($msg, $this->port));

        if ($this->isClosure($this->callback)) {
            $functionParameters = (new \ReflectionFunction($this->callback))->getParameters();
        } else {
            $functionParameters = (new \ReflectionClass($this->callback[0]))->getMethod($this->callback[1])->getParameters();
        }

        call_user_func_array($this->callback, $this->getDependencies($functionParameters, $msg, $from));
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->dispatcher->dispatch(Events::ON_CLOSE, new Events\OnClose($this->port));
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->dispatcher->dispatch(Events::ON_ERROR, new Events\OnError($e, $this->port));
    }

    private function isClosure($t)
    {
        return is_object($t) && ($t instanceof \Closure);
    }

    private function getDependencies(array $functionParameters, $msg, ConnectionInterface $from)
    {
        $dependencies = [];
        foreach ($functionParameters as $param) {
            $parameterName = $param->getName();

            if ($param->getClass() && $param->getClass()->name == 'G\Pxi\Connection') {
                $dependencies[$parameterName] = new Connection($from);
            } elseif ($parameterName == 'data') {
                $dependencies[$parameterName] = $msg;
            }
        }

        return $dependencies;
    }
}