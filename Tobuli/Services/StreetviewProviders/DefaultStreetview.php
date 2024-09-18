<?php namespace Tobuli\Services\StreetviewProviders;

class DefaultStreetview extends StreetviewProvider
{
    const DEFAULT_SERVER = 'http://5.189.140.114/index2.php?';
    const PITCH = -0.76;

    public function getView($location, $size, $heading)
    {
        return file_get_contents(self::DEFAULT_SERVER . http_build_query([
                'size' => $size,
                'location' => $location,
                'heading' => $heading,
                'pitch' => self::PITCH,
                'radius' => self::DEFAULT_RADIUS
            ]));
    }

    protected function getDefaultViewPath($size)
    {
        if (file_exists(public_path('assets/images/no-streetview-' . $size . '.jpg')))
            return public_path('assets/images/no-streetview-' . $size . '.jpg');

        return public_path('assets/images/no-streetview.jpg');
    }
}