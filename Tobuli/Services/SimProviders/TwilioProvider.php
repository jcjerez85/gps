<?php

namespace Tobuli\Services\SimProviders;

use Tobuli\Entities\Device;

class TwilioProvider extends SimProvider
{
    private $account_sid;
    private $token;

    public function __construct()
    {
        $this->account_sid = settings('plugins.sim_blocking.options.account_sid');
        $this->token = settings('plugins.sim_blocking.options.token');

        $this->basicAuth = $this->account_sid . ':' . $this->token;

        parent::__construct();
        $this->url = 'https://supersim.twilio.com/v1';
        $this->isJsonResponse = true;
    }

    public function block(Device $device)
    {
        $sid = $this->getSid($device);

        if (empty($sid)) {
            throw new \Exception('Failed retrieving SID');
        }

        $response = $this->request(
            'Sims/'.$sid,
            [
                'Status' => 'inactive',
            ],
            'post'
        );

        $this->checkResponseErrors($response, 'Failed blocking sim');

        return true;
    }

    public function unblock(Device $device)
    {
        $sid = $this->getSid($device);

        if (empty($sid)) {
            throw new \Exception('Failed retrieving SID');
        }

        $response = $this->request(
            'Sims/'.$sid,
            [
                'Status' => 'active',
            ],
            'post'
        );

        $this->checkResponseErrors($response, 'Failed unblocking sim');

        return true;
    }

    private function getSid(Device $device)
    {
        if (empty($device->msisdn)) {
            return false;
        }

        $response = $this->request(
            'Sims',
            [
                'Iccid' => $device->msisdn,
            ],
            'get'
        );

        if (empty($response)) {
            throw new \Exception('Unable to retrieve device info');
        }

        if (!empty($response['code'])) {
            throw new \Exception('Unable to retrieve device info ' . $response['code']);
        }

        if (empty($response['sims'])) {
            return false;
        }

        foreach ($response['sims'] as $sim) {
            if ($sim['iccid'] == $device->msisdn) {
                return $sim['sid'];
            }
        }

        return false;
    }

    private function checkResponseErrors($response, $errMessage)
    {
        if (empty($response)) {
            throw new \Exception($errMessage);
        }

        if (!empty($response['code'])) {
            throw new \Exception($errMessage . ' ' . $response['code']);
        }
    }
}
