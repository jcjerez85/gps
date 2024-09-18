<?php


namespace Tobuli\Forwards\Connections\MacroPoint;


use Illuminate\Contracts\Support\Arrayable;
use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Forwards\ForwardConnection;

class MacroPoint implements ForwardConnection, Arrayable
{
    protected Form $form;

    protected Client $client;

    public function __construct($config = null)
    {
        $this->form = new Form($config);
        $this->client = new Client($config);
    }

    public static function getType()
    {
        return 'macropoint';
    }

    public static function getTitle()
    {
        return 'MacroPoint';
    }

    public static function isEnabled()
    {
        return config('addon.forward_macropoint');
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

        $this->client->setConfig($data);
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