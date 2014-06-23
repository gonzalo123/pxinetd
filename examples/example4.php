<?php

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/src/Services/Reader1.php';

use G\Pxi\Pxinetd;
use G\Pxi\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

$service = new Pxinetd('0.0.0.0');

$loader = new YamlFileLoader($service, new FileLocator(__DIR__));
$loader->load('conf4.yml');

$service->on(8080, function ($data) {
    echo "$data";
});

$service->run();
