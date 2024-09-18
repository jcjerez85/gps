<?php

namespace Tobuli\Helpers\SMS\Services\HTTP;

use Curl;
use Tobuli\Exceptions\ValidationException;

class SendSmsPOST extends SendSmsHTTP
{
    public function sendThroughConsole($base_url, $query_url)
    {
        $command = 'curl -i ';

        if ($this->authentication)
            $command .= '--user ' . $this->username . ':' . $this->password . ' ';

        $command .= $this->commandLineHeaders();

        if ($this->encoding === 'query') {
            $command .= '-X POST "' . $base_url . '?' . $query_url . '" > /dev/null 2>&1 &';
        } else {
            $command .= '-d \'' . $this->getData($query_url) . '\' ' . $base_url . ' > /dev/null 2>&1 &';
        }

        @exec($command);
    }

    public function sendThroughCurlPHP($base_url, $query_url)
    {
        try {
            $curl = new Curl();

            $curl->options = [
                'CURLOPT_TIMEOUT' => 5,
            ];

            if ($this->authentication)
                $curl->setAuth($this->username, $this->password);

            if ($this->encoding === 'query') {
                $data = '';
                $base_url .= '?' . $query_url;
            } else {
                $data = $this->getData($query_url);
            }

            $curl->headers = array_merge($this->getHeaders(), [
                'Content-Length' => strlen($data)
            ]);

            $response = $curl->request('POST', $base_url, $data);

            return $response;
        } catch (\CurlException $e) {
            throw new ValidationException([
                'curl_request' => trans('validation.attributes.bad_sms_gateway_url') . ": " . $e->getMessage()
            ]);
        }
    }

    protected function getData($query)
    {
        if ($this->encoding === 'json')
            return $this->queryToJson($query);

        return $query;
    }

    protected function queryToJson($query)
    {
        parse_str($query, $params);

        return json_encode($params);
    }
}