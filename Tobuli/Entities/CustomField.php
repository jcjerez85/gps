<?php namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class CustomField extends AbstractEntity
{
    protected $table = 'custom_fields';

    protected $fillable = [
        'title',
        'model',
        'data_type',
        'options',
        'default',
        'slug',
        'required',
        'validation',
        'description',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($item) {
            $item->clearFromSavedReports();
        });
    }

    public function setDefaultAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->attributes['default'] = $value;
    }

    public function getDefaultAttribute($value)
    {
        if ($this->data_type == 'multiselect') {
            $value = json_decode($value);
        }

        return $value;
    }

    public function getDefaultValueAttribute()
    {
        $value = $this->default;

        if (! is_null($value)) {
            return $value;
        }

        switch ($this->data_type) {
            case ('text'):
            case ('date'):
            case ('select'):
                return null;

                break;
            case ('datetime'):
                return '';

                break;
            case ('boolean'):
                return 0;

                break;
            case ('multiselect'):
                return [];

                break;
            default:
                return $value;

                break;
        }
    }

    public function setOptionsAttribute($value)
    {
        if (! is_array($value)) {
            return;
        }

        $options = array_combine(array_map(function($val) {
            return Str::slug($val, '_');
        }, $value), $value);

        $this->attributes['options'] = json_encode($options);
    }

    public function scopeFilterByModel($query, $model)
    {
        $morphMap = Relation::morphMap();

        if (is_object($model)) {
            $model = get_class($model);
        }

        if (isset($morphMap[$model])) {
            return $query->where('model', $model);
        }

        if ($model = array_search($model, $morphMap)) {
            return $query->where('model', $model);
        }

        throw new \Exception('no morph relation');
    }

    public static function getDataTypes($type = null)
    {
        $types = [
            'text'        => trans('validation.attributes.text'),
            'date'        => trans('validation.attributes.date'),
            'datetime'    => trans('validation.attributes.datetime'),
            'boolean'     => trans('validation.attributes.boolean'),
            'select'      => trans('validation.attributes.select'),
            'multiselect' => trans('validation.attributes.multiselect'),
        ];

        if (is_null($type)) {
            return $types;
        }

        if (! isset($types[$type])) {
            throw new \Exception('no data type found');
        }

        return $types[$type];
    }

    public function customValues()
    {
        return $this->hasMany('\Tobuli\Entities\CustomValue', 'custom_field_id');
    }

    public function clearFromSavedReports()
    {
        $reports = Report::whereNotNull('metas')
            ->get();

        foreach ($reports as $report) {
            $metas = array_filter($report->metas, function($meta) {
                return strpos($meta, "custom_fields.{$this->id}") === false;
            });

            $report->update(['metas' => array_values($metas)]);
        }
    }
}
