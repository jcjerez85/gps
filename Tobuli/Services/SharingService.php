<?php

namespace Tobuli\Services;

use App\Exceptions\ResourseNotFoundException;
use Illuminate\Support\Collection;
use Tobuli\Entities\Device;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\SmsTemplate;
use Tobuli\Entities\User;
use Tobuli\Entities\Sharing;

class SharingService
{
    public function create($userId, $data = [])
    {
        $default = [
            'expiration_date' => null,
            'active' => true,
            'name' => $this->generateName(),
            'delete_after_expiration' => false,
        ];

        $data = array_merge($default, $data);

        $sharing = new Sharing($data);
        $sharing->user_id = $userId;
        $sharing->generateHash();
        $sharing->save();

        return $sharing;
    }

    public function update(Sharing $sharing, $data)
    {
        $sharing->update($data);
    }

    public function remove(Sharing $sharing)
    {
        $sharing->delete();
    }

    public function syncDevices(Sharing $sharing, $devices)
    {
        $data = $this->formDevices($sharing->user_id, $devices);

        $sharing->devices()->sync($data);
    }

    public function addDevices(Sharing $sharing, $devices)
    {
        $data = $this->formDevices($sharing->user_id, $devices);

        $sharing->devices()->attach($data);
    }

    public function updateDevices(Sharing $sharing, $devices)
    {
        $data = $this->formDevices($sharing->user_id, $devices);

        //only update passed records. without detatching others
        $sharing->devices()->sync($data, false);
    }

    public function removeDevices(Sharing $sharing, $devices)
    {
        $data = $this->formDevices($sharing->user_id, $devices);

        $sharing->devices()->detach( array_keys($data) );
    }

    public function sendEmail(Sharing $sharing, $emails)
    {
        $template = EmailTemplate::getTemplate('sharing_link', $sharing->user);

        sendTemplateEmail($emails, $template, $sharing);
    }

    public function sendSms(Sharing $sharing, $phones)
    {
        $template = SmsTemplate::getTemplate('sharing_link', $sharing->user);

        sendTemplateSMS($phones, $template, $sharing, $sharing->user_id);
    }

    private function formDevices($user_id, $devices)
    {
        if ( ! ((is_array($devices) || $devices instanceof Collection)))
            $devices = [$devices];

        $data = [];

        foreach ($devices as $device)
        {
            if ($device instanceof Device)
                $device_id = $device->id;
            else {
                $device_id = (int)$device;
            }

            if (empty($device_id))
                continue;

            $data[$device_id] = $this->formDeviceData($user_id);
        }

        return $data;
    }

    private function formDeviceData($userId, $data = [])
    {
        $default = [
            'active'          => true,
            'expiration_date' => null,
        ];

        return array_merge($default, $data, ['user_id' => $userId]);
    }

    private function generateName()
    {
        $lastest = Sharing::latest()->first()->id ?? 0;

        return trans('front.sharing') . ' ' . ++$lastest;
    }
}
