<?php

namespace G\Pxi;

use Ratchet\ConnectionInterface;

class Connection implements ConnectionInterface
{
    private $conn;

    public function __construct(ConnectionInterface $conn)
    {
        $this->conn = $conn;
    }

    public function getRemoteAddress()
    {
        return $this->conn->remoteAddress;
    }

    function send($data)
    {
        $this->conn->send($data);
    }

    function close()
    {
        $this->conn->close();
    }
}