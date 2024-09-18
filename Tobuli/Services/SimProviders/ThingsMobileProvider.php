<?php

namespace Tobuli\Services\SimProviders;

use Tobuli\Entities\Device;

class ThingsMobileProvider extends SimProvider
{
    private $username;
    private $token;

    public function __construct()
    {
        parent::__construct();
        $this->url = 'https://api.thingsmobile.com/services/business-api/';
        $this->isJsonResponse = false;
        $this->username = settings('plugins.sim_blocking.options.username');
        $this->token = settings('plugins.sim_blocking.options.token');
    }

    public function block(Device $device)
    {
        $response = $this->request('blockSim', [
            'username' => $this->username,
            'token'    => $this->token,
            'msisdn'   => $device->msisdn,
        ], 'post');

        $this->checkResponseErrors($response, 'Failed blocking sim');

        return true;
    }

    public function unblock(Device $device)
    {
        $response = $this->request('unblockSim', [
            'username' => $this->username,
            'token'    => $this->token,
            'msisdn'   => $device->msisdn,
        ], 'post');

        $this->checkResponseErrors($response, 'Failed unblocking sim');

        return true;
    }

    private function checkResponseErrors($response, $errMessage)
    {
        $response = parseXMLToArray($response);

        if (empty($response)) {
            throw new \Exception('Sim provider error');
        }

        if (isset($response['errorCode'])) {
            throw new \Exception($response['errorMessage'] ?? 'Sim provider error');
        }

        if (($response['done'] ?? '') != 'true') {
            throw new \Exception($errMessage);
        }
    }
}
