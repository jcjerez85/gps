<?php

namespace LaravelFCM\Request;

abstract class BaseRequest
{
    /**
     * @internal
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @internal
     *
     * @var string
     */
    protected $serverKey = null;

    /**
     * @internal
     *
     * @var string
     */
    protected $senderId = null;

    /**
     * Build a new BaseRequest
     *
     * @param string $serverKey The server key
     * @param string $senderId The sender Id
     */
    public function __construct(string $serverKey = null, string $senderId = null)
    {
        if ($serverKey !== null) {
            $this->serverKey = $serverKey;
        }

        if ($senderId !== null) {
            $this->senderId = $senderId;
        }

        // They may have been already filled
        if ($this->serverKey === null || $this->senderId === null) {
            $config = app('config')->get('fcm.http', []);
            $this->serverKey = $config['server_key'];
            $this->senderId = $config['sender_id'];
        }
    }

    /**
     * Build the header for the request.
     *
     * @return array<string,string>
     */
    protected function buildRequestHeader()
    {
        return [
            'Authorization' => 'key=' . $this->serverKey,
            'Content-Type' => 'application/json',
            'project_id' => $this->senderId,
        ];
    }

    /**
     * Build the body of the request.
     *
     * @return mixed
     */
    abstract protected function buildBody();

    /**
     * Return the request in array form.
     *
     * @return array
     */
    public function build()
    {
        return [
            'headers' => $this->buildRequestHeader(),
            'json' => $this->buildBody(),
        ];
    }
}
