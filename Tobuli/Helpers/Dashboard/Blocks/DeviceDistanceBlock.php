<?php namespace Tobuli\Helpers\Dashboard\Blocks;

use Carbon\Carbon;
use Tobuli\Entities\Device;

class DeviceDistanceBlock extends Block implements BlockInterface
{
    const DAYS_PERIOD = 5;

    /**
     * @return string
     */
    protected function getName()
    {
        return 'device_distance';
    }

    /**
     * Device ditances devided into day intevals.
     *
     * @return array
     */
    protected function getContent()
    {
        $results = [];
        $keys = [];

        $from = Carbon::now()->startOfDay();
        $to   = Carbon::now();

        $devices = $this->user->devices()->orderBy('updated_at','desc')->limit(10)->get()->sortBy('timestamp');

        $i = 0;
        while ($i < self::DAYS_PERIOD)
        {
            $from_date = $from->toDateString();

            foreach ($devices as $device)
            {
                try {
                    $data = $device->getDistanceBetween($from_date, $to->toDateTimeString());
                } catch (\Exception $e) {
                    $data = 0;
                }

                $results[$device->name][] = [$i, $data];
            }

            $keys[] = date('F j', strtotime($from_date));
            $to = clone $from;
            $from->subDay();
            $i++;
        }

        return [
            'data' => json_encode($results),
            'keys' => json_encode($keys),
        ];
    }
}