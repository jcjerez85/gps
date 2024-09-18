<?php

namespace Tobuli\Protocols;


class Manager
{
    public function protocol($name)
    {
        $class = "\\Tobuli\\Protocols\\Protocols\\" . ucfirst($name) . "Protocol";

        if (class_exists($class))
            return new $class();

        return new \Tobuli\Protocols\Protocols\BaseProtocol();
    }
}