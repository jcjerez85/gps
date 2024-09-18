<?php namespace Tobuli\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Tobuli\Services\StreetviewProviders\DefaultStreetview;
use Tobuli\Services\StreetviewProviders\GoogleStreetview;
use Tobuli\Services\StreetviewProviders\MapillaryStreetview;

class AlertSoundService
{
    /**
     * @return array
     */
    public static function getList()
    {
        $list = array_merge(
            self::getDirFiles('assets/audio/'),
            self::getDirFiles('assets/custom/audio/')
        );
        
        return self::moveToTop($list, self::getDefault());
    }

    /**
     * @return string
     */
    public static function getDefault()
    {
        return 'assets/audio/hint.mp3';
    }

    /**
     * @param $path
     * @return string
     */
    public static function getAsset($path)
    {
        if ($path && File::exists(public_path($path)))
            return asset($path);

        return asset(self::getDefault());
    }

    /**
     * @param $path
     * @return array
     */
    private static function getDirFiles($path)
    {
        $result = [];

        if (!File::exists(public_path($path))) {
            return $result;
        }

        $files = File::allFiles(public_path($path));

        foreach ($files as $file) {
            /** var $file SplFileInfo  */
            $result[$path . $file->getFilename()] = $file->getBasename('.' . $file->getExtension());
        }

        return Arr::sort($result);
    }

    private static function moveToTop(array $list, string $key)
    {
        return array_merge(
            [$key => $list[$key]],
            $list
        );
    }
}