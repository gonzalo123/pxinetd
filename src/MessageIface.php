<?php

namespace G\Pxi;

interface MessageIface
{
    public function onMessage($data, Connection $conn);
}