<?php

namespace Tobuli\History\Stats;

class StatValueFirst extends StatValue
{
    protected function valid($value) {
        if ( ! is_null($this->value))
            return false;

        return is_numeric($value);
    }
}