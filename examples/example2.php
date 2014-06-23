<?php

include __DIR__ . '/../vendor/autoload.php';

use G\Pxi\Pxinetd;
use G\Pxi\Connection;

$service = new Pxinetd('0.0.0.0');

$service->on(8080, function ($data) {
    echo $data;
});

$service->on(8088, function ($data, Connection $conn) {
    var_dump($conn->getRemoteAddress());
    echo $data;
    $conn->send("....");
    $conn->close();
});

$service->run();