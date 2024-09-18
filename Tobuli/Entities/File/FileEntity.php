<?php

namespace Tobuli\Entities\File;

use App\Jobs\DeleteFile;
use Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class FileEntity
{
    public static $entity;

    protected $attributes = [
        'path',
        'basename',
        'name',
        'size',
        'created_at'
    ];

    protected $attrValues = [];

    abstract protected function getDirectory($entity);

    public function __construct($file = null)
    {
        $this->fillAttributes($file);
    }

    public static function setEntity($entity)
    {
        static::$entity = $entity;

        return new static;
    }

    public function fillAttributes($file)
    {
        if (!$file) return $this;

        foreach ($this->attributes as $key => $attribute) {
            $method = Str::camel('fill' . ucfirst($attribute));

            if (!method_exists($this, $method)) continue;

            $this->attrValues[$attribute] = $this->{$method}($file);
        }

        return $this;
    }

    public function fillBasename($file)
    {
        return basename($file);
    }

    public function fillPath($file)
    {
        return $file;
    }

    public function fillName($file)
    {
        $dir  = $this->getDirectory(static::$entity);
        $file = str_replace($dir, '', $file);

        return urlencode(Crypt::encrypt($file));
    }

    public function fillSize($file)
    {
        $bytes = sprintf('%u', filesize($file));

        if ($bytes > 0)
        {
            return formatBytes($bytes);
        }

        return $bytes;
    }

    public function fillCreatedAt($file)
    {
        return date('Y-m-d H:i:s', File::lastModified($file));
    }

    public function getIconClass()
    {
        switch (true) {
            case $this->isImage():
                return 'fa fa-file-image-o';
            case $this->isConvertable():
                return 'fa fa-file-code-o';
            case $this->isVideo():
                return 'fa fa-file-video-o';
            default:
                return 'fa fa-file-o';
        }
    }

    public function getMimeType()
    {
        return mime_content_type($this->path);
    }

    public function getExtension()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function isConvertable()
    {
        if (!settings('dualcam.enabled'))
            return false;

        if (in_array($this->getExtension(), ['h265']))
            return true;

        return false;
    }

    public function isVideo()
    {
        if (!settings('dualcam.enabled'))
            return false;

        try {
            return in_array($this->getMimeType(), [
                'video/x-ms-asf',
                'video/x-flv',
                'video/mp4',
                'video/MP2T',
                'video/3gpp',
                'video/quicktime',
                'video/x-msvideo',
                'video/x-ms-wmv',
                'video/avi',
                'video/ogg',
                'video/webm'
            ]) || in_array($this->getExtension(), ['h265']);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isImage()
    {
        try {
            return is_array(getimagesize($this->path));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function imageQuality()
    {
        if (!$this->isImage())
            return '-';

        list($width, $height) = getimagesize($this->path);

        switch (true) {
            case $width >= 1280 && $height >= 720:
                $Q = 'High';
                break;
            case $width > 800 && $height > 600:
                $Q = 'Normal';
                break;
            default:
                $Q = 'Low';
        }

        return $Q;
    }

    public function delete()
    {
        try {
            if (File::delete($this->path)) {
                return true;
            }

            if (File::exists($this->path)) {
                dispatch(new DeleteFile($this->path)); // deleted by root
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function __get($key)
    {
        return $this->attrValues[$key];
    }

    public function __call($method, $parameters)
    {
        if (in_array($method, ['setEntity', 'getDirectory'])) {
            return call_user_func_array([$this, $method], $parameters);
        }

        $query = $this->newFileQuery();

        return call_user_func_array([$query, $method], $parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        $instance = new static;

        return call_user_func_array([$instance, $method], $parameters);
    }

    private function newFileQuery()
    {
        return new FileQuery($this, self::$entity);
    }
}