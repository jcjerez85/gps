<?php


namespace App\Console;

use Illuminate\Support\Collection;
use Tobuli\Entities\Device;
use Tobuli\Entities\Forward;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Forwards\ForwardConnection;
use Tobuli\Forwards\ForwardsManager;

class PositionsForward
{
    /**
     * @var Device
     */
    protected $device;

    /**
     * @var ForwardConnection[]
     */
    protected $forwardConnections;

    public function __construct($device, $debug = false)
    {
        $this->device = $device;
        $this->debug = $debug;

        $this->loadForwardConnections();
    }

    public function isEnabled()
    {
        return config('addon.forwards');
    }

    public function process(TraccarPosition $position)
    {
        foreach ($this->forwardConnections as $connection) {
            try {
                $connection->process($this->device, $position);
            } catch (\Exception $exception) {
                $this->line('Forward process: ' . $exception->getMessage());
            }
        }
    }

    public function send()
    {
        foreach ($this->forwardConnections as $client) {
            try {
                $client->send();
            } catch (\Exception $exception) {
                $this->line('Forward process: ' . $exception->getMessage());
            }
        }
    }

    protected function line($text = '')
    {
        if ( ! $this->debug)
            return;

        echo $text . PHP_EOL;
    }

    protected function loadForwardConnections()
    {
        $this->forwardConnections = [];

        if (!$this->isEnabled())
            return;

        $manager = new ForwardsManager();

        $forwards = $this->getDeviceForwards();

        foreach ($forwards as $forward) {
            $type = $manager->resolveType($forward->type);

            if (!$type)
                continue;

            $this->forwardConnections[] = $type->setConfig($forward->payload);
        }
    }

    /**
     * @return Collection|Forward[]
     */
    protected function getDeviceForwards()
    {
        $start = microtime(true);

        $forwards = Forward::active()
            ->where(function($query) {
                $query->whereIn('id', function($query) {
                    $query
                        ->select('forward_id')
                        ->from('device_group_forward')
                        ->join('user_device_pivot', function ($join) {
                            $join
                                ->on('user_device_pivot.group_id', '=', 'device_group_forward.group_id');
                        })
                        ->where('user_device_pivot.device_id', $this->device->id);
                });
                $query->orWhereIn('id', function($query) {
                    $query
                        ->select('forward_id')
                        ->from('user_forward')
                        ->join('user_device_pivot', function ($join) {
                            $join
                                ->on('user_device_pivot.user_id', '=', 'user_forward.user_id');
                        })
                        ->where('user_device_pivot.device_id', $this->device->id);
                });
            })
            ->get();

        $this->line('Getting forwards ('. $forwards->count() .')' . (microtime(true) - $start));

        return $forwards;
    }
}