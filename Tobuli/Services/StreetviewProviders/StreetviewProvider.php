<?php namespace Tobuli\Services\StreetviewProviders;

abstract class StreetviewProvider
{
    const DEFAULT_RADIUS = 100;

    protected $key;

    abstract public function getView($location, $size, $heading);
    abstract protected function getDefaultViewPath($size);

    public function __construct()
    {
        $this->key = settings('main_settings.streetview_key');
    }

    public function getDefaultView($size = null)
    {
        return file_get_contents($this->getDefaultViewPath($size));
    }

    protected function reverseCoordinates($location)
    {
        return implode(',', array_reverse(explode(',', $location)));
    }
}