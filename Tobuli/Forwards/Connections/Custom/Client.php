<?php


namespace Tobuli\Forwards\Connections\Custom;


use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Forwards\Connections\ForwardClient;

class Client extends ForwardClient
{
    protected $stream = null;

    public function send(){}

    public function process(Device $device, TraccarPosition $position)
    {
        $alarm = $position->getParameter('alarm');

        $data = [
            '$$',
            $device->imei,
            $device->name,
            $position->latitude,
            $position->longitude,
            date('d:m:Y H:i:s', strtotime($position->time)),
            round($position->course),
            round($position->speed),
            $position->protocol,
            in_array($device->getStatus(), [Device::STATUS_ONLINE, Device::STATUS_ENGINE]) ? 'ON' : 'OFF',
            $position->speed > 0 ? 'ON' : 'OFF',
            empty($alarm) ? 0 : $alarm
        ];

        $this->_send(implode(',', $data) . "\n");
    }

    protected function _send($data)
    {
        $stream = $this->connect();

        fwrite($stream, $data);
        stream_socket_shutdown($stream, STREAM_SHUT_WR);

        return stream_get_contents($stream);
    }

    protected function connect()
    {
        if ($this->stream)
            return $this->stream;

        $ip = $this->get('ip');
        $port = $this->get('port');

        $stream = stream_socket_client("tcp://$ip:$port", $errno, $errstr);

        if ($stream) {
            return $this->stream = $stream;
        }

        throw new \Exception("Stream socket fail: {$errno}: {$errstr}");
    }
}