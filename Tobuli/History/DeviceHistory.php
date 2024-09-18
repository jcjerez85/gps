<?php

namespace Tobuli\History;


use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Device;
use Tobuli\History\Actions\Action;
use Tobuli\History\Actions\SensorsValues;


class DeviceHistory
{
    public const EXTEND_TIME_MINUTES = 15;

    protected $config;

    protected $user;

    protected $device;

    /**
     * @var array<Action>
     */
    protected $actions;
    protected $actionContainer;

    protected $previousPosition;

    protected $date_from;
    protected $date_to;

    protected $list;

    protected $groups;

    protected $root;

    protected $geofences;

    public $sensors;
    public $sensors_data;

    public function __construct(Device $device)
    {
        $this->device = $device;

        $this->config = [
            'chunk_size'   => 5000,
            'stop_speed'   => 6,
            'stop_seconds' => 120,
            'speed_limit'  => null,
            'extend_start' => false,
            'extend_end'   => false,

            'min_fuel_fillings' => 10,
            'min_fuel_thefts'   => 10,
        ];

        $this->root = new Group('root');
        $this->groups = new GroupContainer();
        $this->actionContainer = new ActionContainer();
    }

    public function config($key)
    {
        return $this->config[$key];
    }

    public function allConfig(): array
    {
        return $this->config;
    }

    public function hasConfig($key)
    {
        return array_key_exists($key, $this->config);
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function setRange($from, $to)
    {
        $this->date_from = $from;
        $this->date_to = $to;
    }

    public function setSensors($sensors)
    {
        $this->sensors = $sensors;
    }

    public function getDateFrom()
    {
        return $this->date_from;
    }

    public function getDateTo()
    {
        return $this->date_to;
    }

    /**
     * @return Group
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return Group
     */
    public function & root()
    {
        return $this->root;
    }

    /**
     * @return GroupContainer
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return GroupContainer
     */
    public function & groups()
    {
        return $this->groups;
    }

    public function getSensorsData()
    {
        return $this->sensors_data;
    }

    public function registerActions($actionClasses)
    {
        $this->actionContainer->add($actionClasses);

        return $this;
    }

    protected function bootActions()
    {
        if ($this->sensors)
            $this->actionContainer->add([SensorsValues::class]);

        $this->actions = [];

        foreach ($this->actionContainer->get() as $class) {
            $action = new $class($this);
            $action->boot();
            $this->actions[] = $action;
        }
    }

    public function get()
    {
        $this->doit();

        return [
            'root'   => $this->root,
            'groups' => $this->groups,
        ];
    }

    protected function doit()
    {
        $this->bootActions();
        $this->queryPositions($this->date_from, $this->date_to);
    }

    protected function queryPositions($from, $to)
    {
        $columns = [
            'id',
            'altitude',
            'course',
            'latitude',
            'longitude',
            'other',
            'speed',
            'device_time',
            'server_time',
            'valid',
            'sensors_values'
        ];

        $connection = $this->device->positions()->getRelated()->getConnectionName();
        $tableName = $this->device->positions()->getRelated()->getTable();

        DB::disableQueryLog();
        DB::connection($connection)->disableQueryLog();

        $query = DB::connection($connection)
            ->table($tableName)
            ->select($columns);

        $all = (clone $query)
            ->addSelect('time')
            ->whereBetween('time', [$from, $to]);

        $extendMinutes = $this->config['extend_start'];

        if ($extendMinutes !== false) {
            $extendMinutes = is_numeric($extendMinutes) ? $extendMinutes : self::EXTEND_TIME_MINUTES;
            $all->unionAll(
                (clone $query)
                    ->selectRaw("'$from' AS time")
                    ->when($extendMinutes > 0, function (Builder $query) use ($extendMinutes, $from) {
                        $fromExtended = \Carbon::parse($from)->subMinutes($extendMinutes)->format('Y-m-d H:i:s');;

                        $query->where('time', '>=', $fromExtended);
                    })
                    ->where('time', '<', $from)
                    ->orderBy('time', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->limit(1)
            );
        }

        $extendMinutes = $this->config['extend_end'];

        if ($extendMinutes !== false) {
            $extendMinutes = is_numeric($extendMinutes) ? $extendMinutes : self::EXTEND_TIME_MINUTES;

            $all->unionAll(
                (clone $query)
                    ->selectRaw("'$to' AS time")
                    ->when($extendMinutes > 0, function (Builder $query) use ($to, $extendMinutes) {
                        $toExtended = \Carbon::parse($to)->addMinutes($extendMinutes)->format('Y-m-d H:i:s');

                        $query->where('time', '<=', $toExtended);
                    })
                    ->where('time', '>', $to)
                    ->orderBy('time')
                    ->orderBy('id')
                    ->limit(1)
            );
        }

        $all->orderBy('time')->orderBy('id');

        $first = null;
        $last_position = null;

        try {
            $all->chunk($this->config('chunk_size'), function ($positions) use (& $first, & $last_position) {

                $this->preproccess($positions);

                foreach ($positions as $position) {
                    $this->proccess($position);

                    if (is_null($first) && $first = true)
                        $this->root->setStartPosition($position);

                    if ( ! empty($position->quit))
                        return false;
                }

                if (!empty($position))
                    $last_position = $position;
            });
        } catch (QueryException $exception) {
            if ($exception->getCode() != '42S02') {
                throw $exception;
            }
        }

        if( ! empty($last_position)) {
            $this->root->setEndPosition($last_position);

            foreach ($this->groups->actives() as $group) {
                if (!$group->isLastClose())
                    continue;

                $this->groupEnd($group->getKey(), $last_position);
            }
        }

    }

    protected function preproccess($positions)
    {
        foreach ($this->actions as $action)
        {
            $action->preproccess($positions);
        }
    }

    protected function proccess(&$position)
    {
        $this->proceed = false;

        foreach ($this->actions as & $action)
        {
            $action->doIt($position);

            if ( ! empty($position->break))
                break;

            if ($this->proceed)
                break;
        }

        $this->groups()->disactiveClosed();

        //unset($this->previousPosition);
        $this->previousPosition = $position;
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function getSensor($type)
    {
        return $this->device->getSensorByType($type);
    }

    public function getPrevPosition()
    {
        return $this->previousPosition;
    }


    public function hasStat($key): bool
    {
        return $this->root->stats()->has($key);
    }

    public function setStat($key, $stat)
    {
        $this->root->stats()->set($key, $stat);
    }

    public function registerStat($key, $stat)
    {
        $this->root->stats()->set($key, $stat);
    }

    public function applyStat($key, $value)
    {
        if ($key != 'positions')
            $this->root->applyStat($key, $value);

        $this->groups->applyStat($key, $value);
    }


    public function setProceed()
    {
        $this->proceed = true;
    }

    public function addList($position)
    {
        if (empty($this->list))
            $this->listPreviousPosition = $this->previousPosition;

        $this->list[] = $position;
    }

    public function getList()
    {
        return $this->list;
    }


    public function getListFirst()
    {
        $item = reset($this->list);

        return is_array($item) ? $item[0] : $item;
    }

    public function processList($closure)
    {
        foreach ($this->list as & $item) {

            $position = is_array($item) ? $item[0] : $item;

            $item = call_user_func($closure, $position);
        }
    }

    public function doitList()
    {
        $this->previousPosition = $this->listPreviousPosition;

        $i = 0;
        $count = count($this->list);

        while($i++ < $count && $position = array_shift($this->list)) {
            $this->proccess($position);
        }
    }


    public function groupStart($key, $position)
    {
        if ($key instanceof Group) {
            $group = $key;
        } else {
            $group = new Group($key);
        }

        $group->setStartPosition($position);
        $group->stats()->_clone( $this->root->stats()->all() );

        return $this->groups->open($group);
    }

    public function groupEnd($key, $position, $properties = [])
    {
        $this->groups->close($key, $position, $properties);
    }

    public function applyRoute($position)
    {
        if ($this->groups->hasActives()) {
            $lastGroup = $this->groups->last();
            $lastGroup->route()->apply($position);
        } else {
            $this->root->route()->apply($position);
        }

        unset($lastGroup);
    }

    public function setGeofences($geofences)
    {
        $this->geofences = $geofences;
    }

    public function getGeofences()
    {
        return $this->geofences;
    }

    public function inGeofences($position)
    {
        $inGeofences = [];

        if (empty($this->geofences))
            return $inGeofences;

        foreach ($this->geofences as $geofence)
        {
            if ( ! $geofence->pointIn($position))
                continue;

            $inGeofences[] = $geofence->id;
        }

        return $inGeofences;
    }

    public function debug()
    {
        $start = microtime(true);
        $this->doit();
        $time = microtime(true) - $start;

        echo "<br>";
        echo "Device: {$this->device->imei}<br>";

        $stats = $this->root->stats();

        foreach ($stats->all() as $key => $value)
            {
            if (is_object($value)) {
                echo "$key: " . $value->human() . "<br>";
            } else {
                echo "$key: {$value}<br>";
            }
        }

        echo "Groups: " . count($this->groups->all()) . "<br>";
        echo "Memory: " . (memory_get_usage(true) / 1024 / 1024) . "MB<br>";
        //echo "Object: " . ($this->getMemoryUsage($this) / 1024 / 1024) . "MB<br>";
        echo "Proccess: " . round($time, 4) . "s<br>";

        //dd($this->groups);
    }

    public function __destruct()
    {
        $this->actions = null;
        $this->sensors = null;
        $this->root = null;
        $this->groups = null;
        $this->sensors_data = null;
    }

}