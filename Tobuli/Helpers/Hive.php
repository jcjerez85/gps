<?php

namespace Tobuli\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Hive
{
    // const URL = 'http://hive.gpswox.com/servers/v2/';
    const URL = 'http://localhost/';

    protected $client;

    protected $key;

    protected $error;

    public function __construct()
    {
        $this->client = new Client;
        $this->key = config('app.admin_user');
    }

    public function getBackupServer() {
        return $this->json('GET', "backup_server");
    }

    public function backupServerError(array $data) {
        return $this->json('POST', "backup_server/error", $data);
    }

    public function getApns() {
        return $this->json('GET', "apns");
    }

    public function getDeviceConfigs() {
        return $this->json('GET', "device_configs");
    }

    public function getError()
    {
        return $this->error;
    }

    protected function json($method, $endpoint, array $data = [])
    {
        $response = $this->call($method, $endpoint, $data);

        if (is_null($response))
            return null;

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function call($method, $endpoint, array $data = [])
    {
        $this->error = null;

        try {
            $result = $this->client->request($method, self::URL . $this->key . "/" . $endpoint, [
                'form_params' => $data
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $this->error = $response->getBody()->getContents();
            $result = null;
        }

        return $result;
    }
}