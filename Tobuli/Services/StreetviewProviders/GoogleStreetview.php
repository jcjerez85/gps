<?php namespace Tobuli\Services\StreetviewProviders;

use Illuminate\Support\Facades\Cache;

class GoogleStreetview extends StreetviewProvider
{
    const STREETVIEW_URL = 'https://maps.googleapis.com/maps/api/streetview?';
    const METADATA_URL = 'https://maps.googleapis.com/maps/api/streetview/metadata?';

    private $metadata;
    private $panoramaID;

    public function __construct()
    {
        parent::__construct();

        if (is_null($this->key))
            throw new \Exception('Google streetview API key not found!');
    }

    public function getView($location, $size, $heading, $radius = self::DEFAULT_RADIUS)
    {
        if ( ! $this->panoramaID)
            $this->setPanoramaID($location, $size, $radius);

        if (is_null($this->panoramaID))
            throw new \Exception('Location not found!');

        $ch = curl_init(self::STREETVIEW_URL . 'pano=' . $this->panoramaID . '&size=' . $size . '&heading=' . $heading . '&pitch=-0.76&key=' . $this->key);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $image = curl_exec($ch);

        curl_close($ch);

        return $image;
    }

    private function setPanoramaID($location, $size, $radius = self::DEFAULT_RADIUS)
    {
        $this->panoramaID = Cache::get(md5($location));

        if ($this->panoramaID)
            return;

        if ( ! $this->metadata)
            $this->setMetaData($location, $size, $radius);

        if ( ! isset($this->metadata->location))
            return;

        $this->panoramaID = $this->metadata->pano_id;
        Cache::put(md5($location), $this->panoramaID, strtotime('7 days', 0) * 60);
    }

    private function setMetaData($location, $size, $radius = self::DEFAULT_RADIUS)
    {
        $ch = curl_init(self::METADATA_URL . 'location=' . $location . '&radius=' . $radius . '&key=' . $this->key . '&size=' . $size);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $response = curl_exec($ch);
        curl_close($ch);

        $this->metadata = json_decode($response);
    }

    protected function getDefaultViewPath($size)
    {
        if (file_exists(public_path('assets/images/no-streetview-' . $size . '.jpg')))
            return public_path('assets/images/no-streetview-' . $size . '.jpg');

        return public_path('assets/images/no-streetview.jpg');
    }
}