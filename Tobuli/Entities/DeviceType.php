<?php namespace Tobuli\Entities;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeviceType extends AbstractEntity
{
    protected $table = 'device_types';

    protected $fillable = [
        'active',
        'title',
        'sensor_group_id'
    ];

    public function setSensorGroupIdAttribute($value)
    {
        $this->attributes['sensor_group_id'] = empty($value) ? null : $value;
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function hasImage()
    {
        return $this->path ? true : false;
    }

    public function getImageUrl()
    {
        return asset($this->path);
    }

    public function saveImage(UploadedFile $image)
    {
        if ($this->path)
            $this->deleteImage();

        $extension = strtolower($image->getClientOriginalExtension());
        $name      = Str::random();
        $path      = 'images/deviceTypes/';
        $dir       = public_path($path);

        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $image->move($dir, $name . '.' . $extension);

        $this->path = $path . $name . '.' . $extension;
        $this->save();
    }

    public function deleteImage()
    {
        File::delete(public_path($this->path));
    }
}
