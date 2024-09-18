<?php


namespace Tobuli\Forwards;


use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarPosition;

interface ForwardConnection
{
    /**
     * @return string
     */
    public static function getType();

    /**
     * @return string
     */
    public static function getTitle();

    /**
     * @return string
     */
    public static function isEnabled();

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param array|null $config
     * @return mixed
     */
    public function setConfig($config);

    /**
     * @param array $data
     */
    public function validate(array $data);

    /**
     * @param Device $device
     * @param TraccarPosition $position
     */
    public function process(Device $device, TraccarPosition $position);

    public function send();
}