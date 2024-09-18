<?php

namespace Tobuli\Services\SimProviders;

use Cache;
use RuntimeException;
use Tobuli\Entities\Device;

class EMnifyProvider extends SimProvider
{
    const STATUS_ACTIVE = 1;
    const STATUS_SUSPENDED = 2;

    private $token;

    public function __construct()
    {
        parent::__construct();

        $this->url = 'https://cdn.emnify.net/api/v1/';
        $this->isJsonResponse = true;
        $this->token = settings('plugins.sim_blocking.options.token');

        $this->curl->headers = ['accept' => 'application/json'];
        $this->curl->headers['Content-Type'] = 'application/json';
    }

    public function block(Device $device): bool
    {
        $this->changeStatus($device, self::STATUS_SUSPENDED);

        return true;
    }

    public function unblock(Device $device): bool
    {
        $this->changeStatus($device, self::STATUS_ACTIVE);

        return true;
    }

    private function changeStatus(Device $device, int $status)
    {
        $sim = $this->getDeviceSim($device);

        $this->request(
            'sim/' . $sim['id'],
            json_encode(['status' => ['id' => $status]]),
            'patch'
        );
    }

    private function getDeviceSim(Device $device)
    {
        if (!empty($device->msisdn)) {
            $sim = $this->request('sim', ['per_page' => 1, 'q' => "msisdn:{$device->msisdn}"]);

            if (isset($sim[0]['id'])) {
                return $sim[0];
            }
        }

        throw new RuntimeException('SIM not fount for MSISDN ' . $device->msisdn);
    }

    private function getAuthToken(): string
    {
        if ($token = Cache::get('emnify_auth_token')) {
            return $token;
        }

        $response = parent::request('authenticate', json_encode([
            'application_token' => $this->token,
        ]), 'post');

        if (!isset($response['auth_token'])) {
            throw new RuntimeException('Unable to retrieve auth token');
        }

        Cache::put('emnify_auth_token', $response['auth_token'], 239 * 60);

        return $response['auth_token'];
    }

    protected function request($path, $params = [], $method = 'get')
    {
        if (!isset($this->curl->headers['Authorization'])) {
            $this->curl->headers['Authorization'] = 'Bearer ' . $this->getAuthToken();
        }

        return parent::request($path, $params, $method);
    }
}
