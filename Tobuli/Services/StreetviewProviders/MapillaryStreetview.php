<?php namespace Tobuli\Services\StreetviewProviders;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class MapillaryStreetview extends StreetviewProvider
{
    const SEARCH_URL = 'https://graph.mapillary.com/images?';

    public function __construct()
    {
        parent::__construct();

        if (is_null($this->key))
            throw new \Exception('Mapillary streetview API key not found!');
    }

    public function getView($location, $size, $heading)
    {
        $imageUrl = Cache::remember(md5($location), 10080 * 60, function() use ($location) {
            return $this->search($location);
        });

        return $this->downloadImage($imageUrl);
    }

    private function downloadImage($imageUrl)
    {
        return file_get_contents($imageUrl);
    }

    protected function getDefaultViewPath($size)
    {
        return public_path('assets/images/no-streetview.jpg');
    }

    protected function search($location)
    {
        list($lat, $lng) = explode(',', $location);

        $bbox = [
            floatval($lng) - 0.001,
            floatval($lat) - 0.001,
            floatval($lng) + 0.001,
            floatval($lat) + 0.001,
        ];

        $url = self::SEARCH_URL . http_build_query([
                'access_token' => $this->key,
                'fields'       => 'id,thumb_1024_url',
                'bbox'         => implode(',', $bbox),
                'limit'        => 1
            ]);

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        $imageUrl = Arr::get($response, 'data.0.thumb_1024_url');

        if (empty($imageUrl))
            throw new \Exception('Location not found!');

        return $imageUrl;
    }
}