<?php

namespace Tobuli\Helpers\SMS\Services\HTTP;


use Illuminate\Support\Arr;
use Tobuli\Helpers\SMS\Services\SendSmsManager;

abstract class SendSmsHTTP extends SendSmsManager
{
    protected $authentication;
    protected $username;
    protected $password;
    protected $gatewayUrl;
    protected $customHeaders;
    protected $encoding;

    abstract protected function sendThroughConsole($base_url, $query_url);
    abstract protected function sendThroughCurlPHP($base_url, $query_url);

    public function __construct($gateway_args)
    {
        $this->authentication = Arr::get($gateway_args, 'authentication');
        $this->username = Arr::get($gateway_args, 'username');
        $this->password = Arr::get($gateway_args, 'password');
        $this->customHeaders = Arr::get($gateway_args, 'custom_headers');
        $this->gatewayUrl = Arr::get($gateway_args, 'sms_gateway_url');
        $this->encoding   = Arr::get($gateway_args, 'encoding');
    }

    protected function sendSingle($receiver_phone, $message_body)
    {
        $complete_url = $this->insertUrlVariables($receiver_phone, $message_body);
        $url_parts = parse_url($complete_url);

        $base_url = $this->buildGatewayBaseUrl($url_parts);
        $query_url = $this->buildGatewayQueryUrl($url_parts);

        if (app()->runningInConsole())
            return $this->sendThroughConsole($base_url, $query_url);

        return $this->sendThroughCurlPHP($base_url, $query_url);
    }

    protected function insertUrlVariables($receiver_phone, $message_body)
    {
        return strtr($this->gatewayUrl, [
            '%NUMBER%' => rawurlencode($receiver_phone),
            '%MESSAGE%' => urlencode($message_body),
            '%TIMESTAMP%' => time(),
        ]);
    }

    protected function getHeaders()
    {
        $headers = $this->getCustomHeaders();

        if ($this->encoding === 'json')
            $headers['Content-Type'] = 'application/json';

        return $headers;
    }

    protected function getCustomHeaders()
    {
        $headers = [];

        $customHeaders = array_map('trim', array_filter(explode(';', $this->customHeaders)));

        foreach ($customHeaders as $header)
        {
            list($title, $value) = array_map('trim', explode(':', $header));

            $headers[$title] = $value;
        }

        return $headers;
    }

    protected function commandLineHeaders()
    {
        $commandHeaders = [];

        $headers = $this->getHeaders();

        foreach ($headers as $key => $value)
        {
            $commandHeaders[] = '-H "' . $key . ': ' . $value . '"';
        }

        return implode(" ", $commandHeaders) . ' ';
    }

    protected function buildGatewayBaseUrl($url_parts)
    {
        $scheme = isset($url_parts['scheme']) ? $url_parts['scheme'] : 'http';
        $host = isset($url_parts['host']) ? $url_parts['host'] : '';
        $path = isset($url_parts['path']) ? $url_parts['path'] : '';
        $port = isset($url_parts['port']) ? $url_parts['port'] : null;

        return $scheme . '://' . $host . ($port ? ':' . $port : '') . $path;
    }

    protected function buildGatewayQueryUrl($url_parts)
    {
        if (empty($url_parts['query']))
            return '';

        parse_str($url_parts['query'], $url_query_parts);

        if ( ! count($url_query_parts))
            return '';

        $array_for_implode = [];
        foreach ($url_query_parts as $key => $value) {
            $array_for_implode[] = $key . '=' . rawurlencode($value);
        }

        return implode ( '&' , $array_for_implode );
    }
}