<?php

namespace Tobuli\History\Actions;


class AppendRouteColor extends ActionAppend
{
    protected $actions = [];

    protected $route_color_sensor;
    protected $route_color_sensor_2;
    protected $route_color_sensor_3;

    protected $color_b;
    protected $color_p;
    protected $color_r;
    protected $color_r_2;
    protected $color_r_3;

    static public function required()
    {
        return [
            AppendDrivePrivate::class,
            AppendDriveBusiness::class,
        ];
    }

    public function boot()
    {
        if ( settings('plugins.route_color.status') )
        {
            $this->color_r = settings('plugins.route_color.options.value');
            if ($this->route_color_sensor = $this->getSensor('route_color'))
                $this->actions[] = [$this, 'processRouteColorSensor'];

            $this->color_r_2 = settings('plugins.route_color.options.value_2');
            if ($this->route_color_sensor_2 = $this->getSensor('route_color_2'))
                $this->actions[] = [$this, 'processRouteColorSensor2'];

            $this->color_r_3 = settings('plugins.route_color.options.value_3');
            if ($this->route_color_sensor_3 = $this->getSensor('route_color_3'))
                $this->actions[] = [$this, 'processRouteColorSensor3'];
        }

        if ( settings('plugins.business_private_drive.status') )
        {
            $this->color_b = settings('plugins.business_private_drive.options.business_color.value');
            $this->color_p = settings('plugins.business_private_drive.options.private_color.value');
            $this->actions[] = [$this, 'processDriveBusiness'];
            $this->actions[] = [$this, 'processDrivePrivate'];
        }
    }

    public function proccess(&$position)
    {
        $position->color = 'blue';

        foreach ($this->actions as $action)
            call_user_func_array($action, [ & $position]);
    }

    protected function processRouteColorSensor(& $position)
    {
        if ( ! $this->getSensorValue($this->route_color_sensor, $position, false))
            return;

        $position->color = $this->color_r;
    }

    protected function processRouteColorSensor2(& $position)
    {
        if ( ! $this->getSensorValue($this->route_color_sensor_2, $position, false))
            return;

        $position->color = $this->color_r_2;
    }

    protected function processRouteColorSensor3(& $position)
    {
        if ( ! $this->getSensorValue($this->route_color_sensor_3, $position, false))
            return;

        $position->color = $this->color_r_3;
    }

    protected function processDriveBusiness(& $position)
    {
        if (empty($position->drive_business))
            return;

        $position->color = $this->color_b;
    }

    protected function processDrivePrivate(& $position)
    {
        if (empty($position->drive_private))
            return;

        $position->color = $this->color_p;
    }
}