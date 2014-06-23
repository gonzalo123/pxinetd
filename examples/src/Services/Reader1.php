<?php

namespace Services;

use G\Pxi\Connection;
use G\Pxi\MessageIface;

class Reader1 implements MessageIface
{
    public function onMessage($data, Connection $conn)
    {
        echo $data . $conn->getRemoteAddress();
    }
}