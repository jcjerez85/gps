<?php namespace Tobuli\Entities;

use Illuminate\Support\Facades\Cache;

class Database extends AbstractEntity {
	protected $table = 'databases';

    protected $fillable = [];

    public function devices()
    {
        return $this->hasManyThrough(Device::class, TraccarDevice::class);
    }
}
