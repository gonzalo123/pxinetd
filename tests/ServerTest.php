<?php

use React\EventLoop\StreamSelectLoop;
use G\Pxi\Pxinetd;
use G\Pxi\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use G\Pxi\Connection;

include_once __DIR__ . "/fixtures/Reader.php";

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testServer()
    {
        $loop = new StreamSelectLoop();

        $service = new Pxinetd('0.0.0.0');

        $service->on(8080, function ($data, Connection $conn) {
            $this->assertEquals('foo', $data);
            $this->assertEquals('127.0.0.1', $conn->getRemoteAddress());
        });

        $service->on(8888, function ($data, Connection $conn) {
            $this->assertEquals('bar', $data);
            $this->assertEquals('127.0.0.1', $conn->getRemoteAddress());
        });


        $service->register($loop);

        $client1 = stream_socket_client('tcp://localhost:8080');
        fwrite($client1, "foo");

        $client2 = stream_socket_client('tcp://localhost:8888');
        fwrite($client2, "bar");

        $loop->addTimer(0.005, function () use ($loop) {
            $loop->stop();
        });

        $loop->run();
    }

    public function testServerFromYml()
    {
        $loop = new StreamSelectLoop();

        $service = new Pxinetd('0.0.0.0');
        $loader = new YamlFileLoader($service, new FileLocator(__DIR__ ));
        $loader->load('fixtures/conf.yml');

        $service->register($loop);

        $client1 = stream_socket_client('tcp://localhost:9999');
        fwrite($client1, "foo");


        $loop->addTimer(0.005, function () use ($loop) {
            $loop->stop();
        });

        $loop->run();
    }
}