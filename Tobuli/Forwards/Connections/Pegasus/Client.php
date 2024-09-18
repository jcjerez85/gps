<?php


namespace Tobuli\Forwards\Connections\Pegasus;


use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client AS GuzzleClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Forwards\Connections\ForwardClient;


class Client extends ForwardClient
{
    protected $items = [];

    public function send()
    {
        if (empty($this->items))
            return;

        $data = $this->items;
        $this->items = [];

        $this->_send($data);
    }

    public function process(Device $device, TraccarPosition $position)
    {
        $data = [
            "protocol.id"         => "rt.platform",
            "device.type.id"      => "rt.platform",
            "device.id"           => $device->imei,
            "device.name"         => $device->name,

            "timestamp"           => strtotime($position->time),
            "position.latitude"   => (float)$position->latitude,
            "position.longitude"  => (float)$position->longitude,
            "position.direction"  => (float)$position->course,
            "position.speed"      => (float)$position->speed,
            "position.altitude"   => (float)$position->altitude,
            "position.valid"      => (bool)$position->valid,
        ];

        $parameters = $position->parameters;

        $data["metric.odometer"] = round(Arr::get($parameters, 'totaldistance', 0), 3);
        $data["metric.hourmeter"] = round(Arr::get($parameters, 'enginehours', 0), 3);

        $hdop = Arr::get($parameters, 'hdop');
        if (!is_null($hdop)) {
            $data["position.hdop"] = (float) $hdop;
        }

        $sat = Arr::get($parameters, 'sat');
        if (!is_null($sat)) {
            $data["position.satellites"] = (int) $sat;
        }

        $ignition = $this->getIgnitionStatus($device, $position);
        if (!is_null($ignition)) {
            $data["io.ignition"] = $ignition;
        }

        $this->items[] = $data;
    }

    protected function getIgnitionStatus(Device $device, TraccarPosition $position)
    {
        $engineSensor = $device->getEngineSensor();

        if (!$engineSensor)
            return null;

        return $engineSensor->getValue($position->other, false, null);
    }

    /**
     * @param $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function _send($data)
    {
        $client = new GuzzleClient();

        $url = 'https://' . $this->get('domain') . '.peginstances.com/receivers/json';

        return $client->post($url, [
            RequestOptions::HEADERS => [
                'Authenticate' => $this->get('token')
            ],
            RequestOptions::TIMEOUT => 5,
            RequestOptions::JSON => $data
        ]);
    }
}