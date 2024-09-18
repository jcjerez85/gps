<?php

namespace Tobuli\Entities\File;

use Illuminate\Support\Facades\File;
use CustomFacades\Repositories\DeviceRepo;
use Illuminate\Support\Str;
use Tobuli\Entities\MediaCategory;
use Tobuli\Entities\DeviceCamera;
use Tobuli\Entities\Device;

class DeviceCameraMedia extends FileEntity
{
    private static $categoriesEnabled = true;

    protected $attributes = [
        'path',
        'name',
        'category',
        'size',
        'created_at',
        'camera_id',
        'camera_name',
    ];

    protected function getDirectory($entity)
    {
        $path = Str::finish(cameras_media_path(), '/');

        if ($entity) {
            if ($entity instanceof DeviceCamera) {
                $path .= $entity->device->imei . (isset($entity->ftp_username) ? '/' . $entity->ftp_username : '');
            }

            if ($entity instanceof Device) {
                $path .= $entity->imei;
            }
        }

        return $path;
    }

    public function fillCategory($file)
    {
        if (!self::$categoriesEnabled) {
            return null;
        }

        $basename = $this->fillBasename($file);

        $pathElements = explode('.', $basename);
        $categoryId = $pathElements[0];

        if (!is_numeric($categoryId))
            return null;

        $category = runCacheEntity(MediaCategory::class, $categoryId)->first();

        return $category->title ?? null;
    }

    public function fillCameraName($file)
    {
        $name = '';
        $attributes = $this->parseAttributes($file);

        if (isset($attributes['imei']) && isset($attributes['ftp_username'])) {
            $camera = $this->getCamera($attributes['imei'], $attributes['ftp_username']);

            if ($camera) {
                $name = $camera->name;
            }
        }

        return $name;
    }

    public function fillCameraId($file)
    {
        $id = null;
        $attributes = $this->parseAttributes($file);

        if (isset($attributes['imei']) && isset($attributes['ftp_username'])) {
            $camera = $this->getCamera($attributes['imei'], $attributes['ftp_username']);

            if ($camera) {
                $id = $camera->id;
            }
        }

        return $id;
    }

    private function getCamera($imei, $ftp_username)
    {
        $camera = null;

        $device = DeviceRepo::whereImei($imei);

        if ($device) {
            $camera = DeviceCamera::where(
                [
                    'device_id' => $device->id,
                    'ftp_username' => $ftp_username,
                ]
            )->first();
        }

        return $camera;
    }

    private function parseAttributes($path)
    {
        $attributes = [];
        $imei = null;
        $ftpUsername = null;

        try {
            list($imei, $ftpUsername) = array_filter(
                explode(
                    '/',
                    str_replace(
                        [
                            cameras_media_path(),
                            (File::name($path).'.'.File::extension($path))
                        ],
                        '',
                        $path
                    )
                )
            );
        } catch (\Exception $e) {}

        if (isset($imei)) {
            $attributes['imei'] = $imei;
        }

        if (isset($ftpUsername)) {
            $attributes['ftp_username'] = $ftpUsername;
        }

        return $attributes;
    }

    public static function setCategoriesEnabled(bool $categoriesEnabled)
    {
        self::$categoriesEnabled = $categoriesEnabled;
    }
}
