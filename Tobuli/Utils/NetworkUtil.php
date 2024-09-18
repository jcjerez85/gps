<?php

namespace Tobuli\Utils;

use CustomFacades\Server;
use Illuminate\Support\Facades\Cache;

class NetworkUtil
{
    public static function isHostSelfReferencing($host): bool
    {
        if (!filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            $host = parse_url($host, PHP_URL_HOST);
        }

        if ($host === null) {
            throw new \InvalidArgumentException($host . ' host is invalid');
        }

        $serverIp = Server::ip();

        if (!$serverIp) {
            return false;
        }

        $hostIp = Cache::remember('hostbyname_' . $host, 180, function () use ($host) {
            return gethostbyname($host);
        });

        return $hostIp === $serverIp;
    }
}