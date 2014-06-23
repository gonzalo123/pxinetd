<?php
namespace G\Pxi;

use Ratchet\Server\IoServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as Reactor;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Silex\Application;
use React\EventLoop;
use G\Pxi\Events;

class Pxinetd
{
    private $ports = [];
    private $servers = [];
    private $address;
    private $dispatcher;

    public function __construct($address)
    {
        $this->address    = $address;
        $this->dispatcher = new EventDispatcher();
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function on($port, callable $callback)
    {
        $this->ports[$port] = $callback;
    }

    public function run()
    {
        $loop = LoopFactory::create();

        foreach ($this->ports as $port => $callback) {
            $socket = new Reactor($loop);
            $socket->listen($port, $this->address);

            $this->servers[$port] = new IoServer(new Message($callback, $this->dispatcher, $port), $socket, $loop);
        }

        echo "ports: " . implode(', ', array_keys($this->ports)) . "\n";

        $this->registerListeners();

        $loop->run();
    }

    private function registerListeners()
    {
        $this->dispatcher->addListener(Events::ON_MESSAGE, function (Events\OnMessage $event) {
            echo "New message on port: " . $event->getPort() . ". Message: " . $event->getMessage() . "\n";
        });

        $this->dispatcher->addListener(Events::ON_ERROR, function (Events\OnError $event) {
            echo "Error! on port: " . $event->getPort() . ". Message: " . $event->getException()->getMessage() . "\n";
        });
    }
}