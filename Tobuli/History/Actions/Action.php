<?php

namespace Tobuli\History\Actions;


use Illuminate\Support\Arr;
use Tobuli\History\DeviceHistory;

abstract class Action
{
    protected $history;

    //abstract public function register();
    abstract public function boot();

    static public function required(){ return [];}
    static public function after(){ return [];}

    public function __construct(DeviceHistory & $history)
    {
        $this->history = $history;
    }

    public function doIt(&$position)
    {
        $this->proccess($position);
    }

    public function preproccess($positions){}

    public function getPrevPosition()
    {
        return $this->history->getPrevPosition();
    }

    public function getDevice()
    {
        return $this->history->getDevice();
    }

    public function getSensor($type)
    {
        return $this->history->getSensor($type);
    }

    protected function proceed()
    {
        $this->history->setProceed();
    }

    protected function fire($event, $position = null)
    {
        if (in_array($event, ['engine_status.changed', 'drive_stop.changed']))
            return;

        //echo "Fire '$event'<br>";
    }

    protected function isStateChanged($position, $state_key)
    {
        $previous = $this->history->getPrevPosition();

        if ( ! $previous)
            return false;

        return $position->{$state_key} !== $previous->{$state_key};
    }

    protected function isStateCalcable($position, $state_key)
    {
        return $position->{$state_key} && ! $this->isStateChanged($position, $state_key);
    }

    protected function getSensorValue(& $sensor, & $position, $default = null)
    {
        if (is_null($sensor))
            return null;

        if ( ! empty($position->sensors[$sensor->id]))
            return $position->sensors[$sensor->id]['v'];

        $value = null;

        $previous = $this->getPrevPosition();
        if ($previous && ! empty($previous->sensors[$sensor->id]))
            $value = $previous->sensors[$sensor->id]['v'];

        $value = $sensor->getValue($position->other, false, $value);


        if (is_null($value) && $sensor->isUpdatable() && !empty($position->sensors_values)) {
            $json = json_decode($position->sensors_values, true);
            if (is_string($json))
                $json = json_decode($json, true);

            if (is_array($json) && $saved = Arr::where($json, function($value) use ($sensor){ return $value['id'] == $sensor->id; })) {
                try {
                    $value = array_shift($saved)['val'];
                } catch (\Exception $e) {
                    //dd($saved, $e);
                }
            }
        }


        if (is_null($value))
            $value = $default;

        return $position->sensors[$sensor->id]['v'] = $value;
    }

    protected function registerStat($key, $stat)
    {
        $this->history->registerStat($key, $stat);
    }

    protected function registerLisiner($key, $callback)
    {
        $this->history->registerStat($key, $callback);
    }

    public function __call($method, $args)
    {
        return call_user_func([$this->history, $method], $args);
    }

    public function __destruct()
    {
        unset($this->history);
    }
}