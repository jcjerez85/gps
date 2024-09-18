<?php

namespace Tobuli\History\Stats;

use Tobuli\Helpers\Formatter\Formattable;
use Tobuli\Helpers\Formatter\Unit\IDList;
use Tobuli\Helpers\Formatter\Unit\ModelList;

class StatModelList extends StatList
{
    public function __construct($class, $property = null)
    {
        parent::__construct();

        $this->setFormatUnit(new ModelList($class, $property));
    }
}