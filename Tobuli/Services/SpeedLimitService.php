<?php namespace Tobuli\Services;

use Curl;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class SpeedLimitService
{
    const URL = 'https://roads.googleapis.com/v1/speedLimits';
    const LIMIT = 10;

    /**
     * @var Curl
     */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl;
        $this->curl->options['CURLOPT_SSL_VERIFYPEER'] = false;
        $this->curl->options['CURLOPT_TIMEOUT'] = 5;

        $this->key = config('services.speedlimit.key');
    }

    public function get($coordinates)
    {
        $result = [];

        $chunks = $coordinates->chunk(self::LIMIT);

        foreach ($chunks as $i => $chunk) {

            $chunkCoordinates = $chunk->values();

            $_res = $this->request($this->buildPath($chunkCoordinates));
            $_map = $this->mapResult($chunkCoordinates, $_res);

            $result = array_merge(
                $result,
                $_map
            );
        }

        return $result;
    }

    public function getKey($coordinate)
    {
        $latitude = round($coordinate->latitude, 6);
        $longitude = round($coordinate->longitude, 6);

        return "$latitude,$longitude";
    }

    protected function mapResult($coordinates, $limits)
    {
        $results = [];

        foreach ($coordinates as $index => $coordinate) {
            $placeId = Arr::get($limits, "snappedPoints.$index.placeId");

            $limit = null;

            if ($placeId && array_key_exists('speedLimits', $limits)) {
                $limit = Arr::first($limits['speedLimits'], function ($item) use ($placeId) {
                    return $item['placeId'] == $placeId;
                });
            }

            $results[$this->getKey($coordinate)] = $limit['speedLimit'] ?? null;
        }

        return $results;
    }

    protected function buildPath($coordinates)
    {
        $path = '';

        foreach ($coordinates as $coordinate) {
            $path .= "{$coordinate->latitude},{$coordinate->longitude}|";
        }

        return substr($path, 0, -1);
    }

    protected function request($path)
    {
        $response = $this->curl->get(self::URL . '?key=' . $this->key, [
            'path' => $path
        ]);

        $body = json_decode($response->body, true);

        if ($response->headers['Status-Code'] != 200) {
            throw new \Exception(Arr::get($body, 'error.message') ?: 'SpeedLimit API error.');
        }

        return $body;
    }
}