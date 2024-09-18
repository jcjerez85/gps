<?php

namespace Tobuli\History\Actions;

class AppendLockStatus extends ActionAppend
{
    protected $parameter;
    protected $value_on;
    protected $value_off;

    static public function required()
    {
        return [];
    }

    public function boot()
    {
        $this->parameter = settings('plugins.locking_widget.options.parameter');
        $this->value_on  = settings('plugins.locking_widget.options.value_on');
        $this->value_off = settings('plugins.locking_widget.options.value_off');
    }

    public function proccess(&$position)
    {
        $position->lock_status = $this->getStatus($position);
    }

    protected function getStatus($position) {
        $status = parseTagValue($position->other, $this->parameter);

        if (is_null($status))
            return null;

        if ($status == $this->value_on)
            return true;

        if ($status == $this->value_off)
            return false;

        return null;
    }
}
