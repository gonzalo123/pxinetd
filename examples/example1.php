<?php

include __DIR__ . '/../vendor/autoload.php';

use G\Pxi\Pxinetd;

$service = new Pxinetd('0.0.0.0');

$service->on(8080, function ($data) {
    echo $data;
});

$service->run();