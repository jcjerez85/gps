<?php


namespace Tobuli\Forwards\Connections\Custom;


use Illuminate\Contracts\Support\Arrayable;
use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Forwards\ForwardConnection;

class Custom implements ForwardConnection, Arrayable
{
    protected $form;

    protected $client;

    public function __construct($config = null)
    {
        $this->form = new Form($config);
        $this->client = new Client($config);
    }

    public static function getType()
    {
        return 'custom';
    }

    public static function getTitle()
    {
        return 'Custom';
    }

    public static function isEnabled()
    {
        return config('addon.forward_custom');
    }

    public function setConfig($config)
    {
        $this->form->setConfig($config);
        $this->client->setConfig($config);

        return $this;
    }

    public function getAttributes()
    {
        return $this->form->getAttributes();
    }

    public function validate(array $data)
    {
        $this->form->validate($data);
    }

    public function process(Device $device, TraccarPosition $position)
    {
        $this->client->process($device, $position);
    }

    public function send()
    {
        $this->client->send();
    }

    public function toArray()
    {
        return [
            'type' => self::getType(),
            'title' => self::getTitle(),
        ];
    }
}