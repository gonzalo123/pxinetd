<?php

use G\Pxi\Pxinetd;
use G\Pxi\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

include_once __DIR__ . "/fixtures/Reader.php";

class YamlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $service = new Pxinetd('0.0.0.0');
        $this->assertEquals([], $service->getPorts());

        $loader = new YamlFileLoader($service, new FileLocator(__DIR__ ));
        $loader->load('fixtures/conf.yml');
        $this->assertEquals([9999, 9991, 9992], $service->getPorts());
    }
}