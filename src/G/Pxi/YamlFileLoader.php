<?php

namespace G\Pxi;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Parser as YamlParser;
use Guzzle\Http\Client;

class YamlFileLoader extends FileLoader
{
    protected $locator;
    private $server;
    protected $yamlParser;

    public function __construct($server, FileLocatorInterface $fileLocator)
    {
        $this->locator = $fileLocator;
        $this->server  = $server;
    }

    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $content = $this->loadFile($path);

        if (null === $content) {
            return;
        }

        $this->parseImports($content, $file);

        if (isset($content['ports'])) {
            foreach ($content['ports'] as $port => $value) {
                if (isset($value['class'])) {
                    $this->server->on($port, [new $value['class'], 'onMessage']);
                } elseif (isset($value['url'])) {
                    $this->server->on($port, function ($data, Connection $conn) use ($value) {
                        $client = new Client();
                        $url = $value['url'];
                        $url = str_replace('{data}', trim($data), $url);
                        $url = trim(str_replace('{ipFrom}', $conn->getRemoteAddress(), $url));

                        $conn->close();
                        $response = $client->get($url)->send();
                        $statusCode = $response->getStatusCode();

                        if ($statusCode != 200) {
                            throw new \Exception("HTTP error. Code: {$statusCode}", $statusCode);
                        }
                    });
                }
            }
        }
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    private function parseImports($content, $file)
    {
        if (!isset($content['imports'])) {
            return;
        }

        foreach ($content['imports'] as $import) {
            $this->setCurrentDir(dirname($file));
            $this->import($import['resource'], null, isset($import['ignore_errors']) ? (Boolean)$import['ignore_errors'] : false, $file);
        }
    }

    protected function loadFile($file)
    {
        if (!stream_is_local($file)) {
            throw new InvalidArgumentException(sprintf('This is not a local file "%s".', $file));
        }

        if (!file_exists($file)) {
            throw new InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
        }

        if (null === $this->yamlParser) {
            $this->yamlParser = new YamlParser();
        }

        return $this->validate($this->yamlParser->parse(file_get_contents($file)), $file);
    }

    private function validate($content, $file)
    {
        if (null === $content) {
            return $content;
        }

        if (!is_array($content)) {
            throw new InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
        }

        foreach (array_keys($content) as $namespace) {
            if (in_array($namespace, array('imports', 'ports'))) {
                continue;
            }
        }

        return $content;
    }
}