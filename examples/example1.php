<?php

include __DIR__ . '/../vendor/autoload.php';

use G\Pxi\Pxinetd;
use React\EventLoop\Factory as LoopFactory;

$loop = LoopFactory::create();
$service = new Pxinetd('0.0.0.0');

$service->on(8080, function ($data) {
    echo $data;
});

$service->register($loop);
$loop->run();