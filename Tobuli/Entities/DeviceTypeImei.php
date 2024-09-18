<?php namespace Tobuli\Entities;

use Tobuli\Traits\Filterable;
use Tobuli\Traits\Searchable;

class DeviceTypeImei extends AbstractEntity
{
    use Searchable, Filterable;

    protected $table = 'device_type_imeis';

    protected $fillable = [
        'imei',
        'msisdn',
        'device_type_id'
    ];

    protected $searchable = [
        'imei',
        'msisdn'
    ];

    protected $filterables = [
        'device_type_id',
        'imei',
        'msisdn'
    ];

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class);
    }
}
