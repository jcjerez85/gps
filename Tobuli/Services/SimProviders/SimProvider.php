<?php

namespace Tobuli\Services\SimProviders;

use Illuminate\Support\Str;

abstract class SimProvider implements SimProviderInterface
{
    protected $url;
    protected $curl;
    protected $isJsonResponse;
    protected $basicAuth;

    public function __construct()
    {
        $curl = new \Curl;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = false;
        $curl->options['CURLOPT_TIMEOUT'] = 5;

        if (! empty($this->basicAuth)) {
            $curl->options['CURLOPT_HTTPAUTH'] = CURLAUTH_BASIC;
            $curl->options['CURLOPT_USERPWD'] = $this->basicAuth;
        }

        $this->curl = $curl;
    }

    public function getName()
    {
        return str_replace('Provider', '', (new \ReflectionClass($this))->getShortName());
    }

    protected function request($path, $params = [], $method = 'get')
    {
        $path = Str::finish($this->url, '/') . $path;

        $response = method_exists($this->curl, $method)
            ? $this->curl->$method($path, $params)
            : $this->curl->request(strtoupper($method), $path, $params);

        if ($this->isJsonResponse) {
            return json_decode($response->body, true);
        }

        return $response->body;
    }
}
