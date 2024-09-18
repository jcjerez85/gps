<?php namespace Tobuli\Entities;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Tobuli\Helpers\PolygonHelper;
use Tobuli\Traits\Filterable;
use Tobuli\Traits\Searchable;

class Geofence extends AbstractEntity {

    use Searchable, Filterable;

    const TYPE_CIRCLE = 'circle';
    const TYPE_POLYGON = 'polygon';

	protected $table = 'geofences';

    protected $fillable = [
        'user_id',
        'group_id',
        'device_id',
        'name',
        'active',
        'polygon_color',
        'speed_limit',
        'type',
        'radius',
        'center',
        'polygon',
    ];

    protected $hidden = array('polygon');

    protected $casts = [
        'radius' => 'float'
    ];

    protected $searchable = [
        'name',
    ];

    protected $filterables = [
        'group_id',
    ];

    protected $polygonHelpers = [];

    protected static function boot()
    {
        parent::boot();

        static::saved(function (Geofence $item) {
            if (!$item->wasChanged(['type', 'coordinates']) && !$item->wasRecentlyCreated) {
                return;
            }

            $polygon = $item->type === self::TYPE_POLYGON
                ? "PolygonFromText('POLYGON((" . gen_polygon_text(json_decode($item->coordinates, true)) . "))')"
                : 'NULL';

            DB::unprepared("UPDATE geofences SET polygon = $polygon WHERE id = $item->id");
        });
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(GeofenceGroup::class);
    }

    public function diemRate(): BelongsTo
    {
        return $this->belongsTo(DiemRate::class);
    }

    public function setPolygonAttribute($value)
    {
        if (is_array($value))
            $value = json_encode($value);

        $this->attributes['coordinates'] = $value;
    }

    public function getGroupIdAttribute($value)
    {
        if (is_null($value))
            return 0;

        return $value;
    }

    public function setGroupIdAttribute($value)
    {
        if (empty($value))
            $value = null;

        $this->attributes['group_id'] = $value;
    }

    public function setDeviceIdAttribute($value)
    {
        if (empty($value))
            $value = null;

        $this->attributes['device_id'] = $value;
    }

    public function setCenterAttribute($value)
    {
        if (is_array($value))
            $value = json_encode($value);

        $this->attributes['center'] = $value;
    }

    public function getCenterAttribute($value)
    {
        return json_decode($value, true);
    }

    public function pointIn($data)
    {
        if (is_object($data)) {
            $point = [
                'lat' => $data->latitude,
                'lng' => $data->longitude
            ];
        } elseif (is_array($data)) {
            $point = [
                'lat' => $data['latitude'],
                'lng' => $data['longitude']
            ];
        } elseif (is_string($data)) {
            $coordinates = explode(" ", $data);
            $point = [
                'lat' => $coordinates[0],
                'lng' => $coordinates[1]
            ];
        } else {
            return null;
        }

        if ($this->type == 'circle')
            return $this->pointInCircle($point);

        return $this->pointInPolygon($point);
    }

    public function pointOut($data)
    {
        return ! $this->pointIn($data);
    }

    /**
     * @param $point ['latitude' => x, 'longitude' => y]
     * @return float|int
     */
    public function pointAwayBy($point)
    {
        if ($this->pointIn($point))
            return 0;

        $center = $this->getCenter();

        return getDistance($center['lat'], $center['lng'], $point['latitude'], $point['longitude']);
    }

    public function getCenter()
    {
        if ($this->type == self::TYPE_CIRCLE)
            return $this->center;

        return $this->getPolygonHelper()->getCenter();
    }

    private function pointInPolygon($point)
    {
        return false !== $this->getPolygonHelper()->pointInPolygon($point);
    }

    private function pointInCircle($point)
    {
        $center = $this->center;

        return $this->radius > (getDistance($center['lat'], $center['lng'], $point['lat'], $point['lng']) * 1000);
    }

    /**
     * @return PolygonHelper
     */
    private function getPolygonHelper()
    {
        if (!isset($this->polygonHelpers[$this->id]))
        {
            $coordinates = json_decode($this->coordinates, TRUE);

            if (empty($coordinates)) {
                $coordinates = [];
            } else {
                $first = current($coordinates);
                array_push($coordinates, $first);
            }

            $this->polygonHelpers[$this->id] = new PolygonHelper($coordinates);
        }

        return $this->polygonHelpers[$this->id];
    }

    public function scopeUserOwned(Builder $query, User $user): Builder
    {
        return $query->where(['user_id' => $user->id]);
    }

    public function scopeUserAccessible(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user) {
            $query->userOwned($user);
        });
    }

    public function scopeContainPoint(Builder $query, $lat, $lng): Builder
    {
        return $query->where(function (Builder $query) use ($lat, $lng) {
            $query->where(function (Builder $query) use ($lat, $lng) {
                $query
                    ->where('geofences.type', self::TYPE_POLYGON)
                    ->whereRaw("ST_CONTAINS(geofences.polygon, POINT($lat, $lng))");
            });

            $query->orWhere(function (Builder $query) use ($lat, $lng) {
                $query
                    ->where('geofences.type', self::TYPE_CIRCLE)
                    ->whereRaw("ST_DISTANCE_SPHERE_2D(
                        $lat,
                        $lng,
                        SUBSTRING_INDEX(SUBSTRING_INDEX(geofences.center, 'lat\":', -1), ',', 1),
                        SUBSTRING_INDEX(SUBSTRING_INDEX(geofences.center, 'lng\":', -1), '}', 1)
                    ) <= geofences.radius");
            });
        });
    }
}
