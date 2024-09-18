<?php namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Traits\Filterable;
use Tobuli\Traits\Searchable;

class Poi extends AbstractEntity {
    use Searchable, Filterable;

	protected $table = 'user_map_icons';

    protected $fillable = array('user_id', 'active', 'map_icon_id', 'group_id', 'name', 'description', 'coordinates');

    protected $casts = [
        'coordinates' => 'array',
    ];

    protected $searchable = [
        'name',
    ];

    protected $filterables = [
        'group_id',
    ];

    public function user() {
        return $this->belongsTo('Tobuli\Entities\User', 'user_id', 'id');
    }
    
    public function mapIcon()
    {
        return $this->hasOne('Tobuli\Entities\MapIcon', 'id', 'map_icon_id');
    }

    public function group()
    {
        return $this->belongsTo('Tobuli\Entities\PoiGroup', 'group_id', 'id');
    }

    public function setGroupIdAttribute($value)
    {
        if (empty($value))
            $value = null;

        $this->attributes['group_id'] = $value;
    }

    public function pointIn($data, $tolerance)
    {
        $distance = $this->pointDistance($data);

        if (is_null($distance))
            return false;

        return $distance <= $tolerance;
    }

    public function pointOut($data, $tolerance)
    {
        return ! $this->pointIn($data, $tolerance);
    }

    public function pointDistance($data)
    {
        if (is_string($data))
        {
            list($latitude, $longitude) = explode(' ', $data);
        }
        elseif (is_object($data))
        {
            $latitude = $data->latitude;
            $longitude = $data->longitude;
        }
        elseif (is_array($data))
        {
            $latitude = $data['latitude'];
            $longitude = $data['longitude'];
        }
        else
        {
            return null;
        }

        return getDistance($this->coordinates['lat'], $this->coordinates['lng'], $latitude, $longitude);
    }

    public function scopeUserOwned(Builder $query, User $user): Builder
    {
        return $query->where(['user_id' => $user->id]);
    }
}
