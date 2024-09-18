<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class WatchProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_ALARM_SOS),
            $this->initCommand(Commands::TYPE_ALARM_BATTERY),
            $this->initCommand(Commands::TYPE_ALARM_REMOVE),
            $this->initCommand(Commands::TYPE_ALARM_CLOCK),
            $this->initCommand(Commands::TYPE_POSITION_SINGLE),
            $this->initCommand(Commands::TYPE_POSITION_PERIODIC),
            $this->initCommand(Commands::TYPE_SET_PHONEBOOK),
            $this->initCommand(Commands::TYPE_SET_TIMEZONE),
            $this->initCommand(Commands::TYPE_SOS_NUMBER),
            $this->initCommand(Commands::TYPE_REBOOT_DEVICE),
            $this->initCommand(Commands::TYPE_CUSTOM),
            $this->initCommand(Commands::TYPE_REQUEST_PHOTO),
        ];
    }

    protected function buildCommandrequestPhoto($device, $data) {
        $data[Commands::KEY_TYPE] =  Commands::TYPE_CUSTOM;
        $data[Commands::KEY_DATA] =  'rcapture';

        return $data;
    }

    protected function buildCommandSetPhonebook($device, $data)
    {
        $addressees = [];

        foreach($data['name'] as $index => $name)
        {
            $phone = $data['phone'][$index];

            if (empty($name))
                continue;

            if (empty($phone))
                continue;

            $phone = str_replace(',', '', $phone);
            $name  = str_replace(',', '', $name);

            $name  = $this->string2Hex($name);

            $addressees[] = $phone . ',' . $name;
        }

        $data[Commands::KEY_DATA] = implode(',', $addressees);

        unset($data['name'], $data['phone']);
        return $data;
    }

    function string2Hex($string){
        $hex='';
        for ($i=0; $i < strlen($string); $i++){
            $hex .= '00' . dechex(ord($string[$i]));
        }
        return $hex;
    }
}