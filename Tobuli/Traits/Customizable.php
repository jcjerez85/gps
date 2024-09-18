<?php namespace Tobuli\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Tobuli\Entities\CustomField;
use Tobuli\Entities\CustomValue;

trait Customizable
{
    private $_customValues = null;
    private $_availableSlugs = null;

    public static function bootCustomizable()
    {
        static::deleting(function($item) {
            $morphModel = array_search(get_called_class(), Relation::morphMap());

            if ($morphModel === false) {
                return;
            }

            $item->customValues()->delete();
        });
    }

    public function customFields()
    {
        //get relation without primary and foreign keys constraint
        return Relation::noConstraints(function () {
            return $this->hasMany('\Tobuli\Entities\CustomField')
                ->where('model', array_search(get_class($this), Relation::morphMap()));
        });
    }

    public function customValues()
    {
        return $this->morphMany('\Tobuli\Entities\CustomValue', 'customizable');
    }

    public function getCustomValue($searchValue)
    {
        if (is_null($this->_customValues)) {
            $this->cacheCustomValues();
        }

        $searchBy = is_int($searchValue) ? 'custom_field_id' : 'slug';

        $customValue = Arr::first($this->_customValues, function ($value, $key) use($searchBy, $searchValue) {
            return isset($value[$searchBy]) && $value[$searchBy] == $searchValue;
        });

        if (is_null($customValue)) {
            return null;
        }

        return $customValue['is_array']
            ? json_decode($customValue['value'])
            : $customValue['value'];
    }

    public function setCustomValue($key, $value)
    {
        if (is_null($this->_customValues)) {
            $this->cacheCustomValues();
        }

        if (! $this->_availableSlugs) {
            return;
        }

        $searchBy = is_int($key) ? 'custom_field_id' : 'slug';
        $isArray = false;

        $customFieldId = isset($this->_availableSlugs[$key])
            ? $key
            : array_search($key, $this->_availableSlugs);

        if ($customFieldId === false) {
            return;
        }

        $slug = $this->_availableSlugs[$customFieldId];

        if (is_array($value)) {
            $isArray = true;
            $value = json_encode($value);
        }

        $customValueKey = null;
        
        foreach ($this->_customValues as $cKey => $customValue) {
            if ($customValue['custom_field_id'] == $customFieldId || $customValue['slug'] == $slug) {
                $customValueKey = $cKey;
            }
        }

        if (! is_null($customValueKey)) {
            $this->_customValues[$customValueKey]['value'] = $value;
            $this->_customValues[$customValueKey]['is_array'] = $isArray;

            return;
        }

        $this->_customValues[] = [
            'custom_field_id' => $customFieldId,
            'slug' => $slug,
            'value' => $value,
            'is_array' => $isArray,
        ];
    }

    public function deleteCustomValue($slug)
    {
        $this->customValues()
            ->whereSlug($slug)
            ->delete();
    }

    public function setCustomValues($values)
    {
        if (is_null($this->_customValues)) {
            $this->cacheCustomValues();
        }

        foreach ($values as $key => $value) {
            $this->setCustomValue($key, $value);
        }
    }

    public function saveCustomValues()
    {
        if (is_null($this->_customValues)) {
            return;
        }

        if (! $this->_availableSlugs) {
            return;
        }

        foreach ($this->_customValues as $customValue) {
            $customFieldId = isset($this->_availableSlugs[$customValue['custom_field_id']])
                ? $customValue['custom_field_id']
                : array_search($customValue['slug'], $this->_availableSlugs);

            if ($customFieldId === false) {
                continue;
            }

            $this->customValues()->updateOrCreate([
                    'custom_field_id' => $customFieldId,
                ],
                [
                    'custom_field_id' => $customFieldId,
                    'value' => $customValue['value'],
                ]
            );
        }
    }

    public function hasCustomFields()
    {
        return isset($this->customFields) && $this->customFields->count();
    }

    private function cacheCustomValues()
    {
        $this->_customValues = [];
        $customValues = $this->customValues()
            ->with('custom_field')
            ->get();

        foreach ($customValues as $value) {
            $this->_customValues[] = [
                'custom_field_id' => $value->custom_field_id,
                'slug' => $value->custom_field->slug,
                'value' => $value->value,
                'is_array' => $value->custom_field->data_type == 'multiselect',
            ];
        }

        $this->_availableSlugs = $this->customFields()
            ->get()
            ->pluck('slug', 'id')
            ->toArray();
    }
}
