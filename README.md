PXINETD
======

## Example 1

```php
use G\Pxi\Pxinetd;

$service = new Pxinetd('0.0.0.0');

$service->on(8080, function ($data) {
    echo $data;
});

$service->run();
```

## Example 2

```php
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
```

## Example 3

```php
use G\Pxi\Pxinetd;
use G\Pxi\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

$service = new Pxinetd('0.0.0.0');

$loader = new YamlFileLoader($service, new FileLocator(__DIR__ ));
$loader->load('conf3.yml');

$service->on(8080, function ($data) {
    echo "$data";
});

$service->run();
```

```bash
  9999:
    class: Services\Reader1
```

```php
use G\Pxi\Connection;
use G\Pxi\MessageIface;

class Reader1 implements MessageIface
{
    public function onMessage($data, Connection $conn)
    {
        echo $data . $conn->getRemoteAddress();
    }
}
```



