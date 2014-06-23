<?php

include __DIR__ . "/../../vendor/autoload.php";

use Silex\Application;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app = new Application();

$app->get('/onMessage/{data}', function ($data) {
    return "OK" . "'{$data}'";
});

$app->get('/simulateError/{data}', function ($data) {
    throw new NotFoundHttpException();
});

$app->run();