<?php

namespace Tobuli\Helpers\Settings;

class SettingsModel extends Settings {

    protected $prefix = 'SettingsModel';

    protected $values;

    public function getValues()
    {
        return $this->values;
    }

    public function setValues($values)
    {
        $this->values = $values;
    }

    protected function _has($key) {
        if (empty($key))
            return false;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return false;

        $settings = $this->getValues();

        if ( ! isset($settings[$group]))
            return false;

        try {
            $has = has_array_value( $settings[$group], $keys );
        }
        catch (\Exception $e) {
            $has = true;
        }

        return $has;
    }

    protected function _get($key) {
        if (empty($key))
            return null;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return null;

        $settings = $this->getValues();

        $item = isset($settings[$group]) ? $settings[$group] : null;

        if (empty($item))
            return null;

        try {
            $value = get_array_value( $item, $keys );
        }
        catch (\Exception $e) {
            $value = $item;
        }

        return $value;
    }

    protected function _set($key, $value) {
        if (empty($key))
            return false;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return false;

        $settings = $this->getValues();

        $item = empty($settings[$group]) ? [] : $settings[$group];

        set_array_value( $item, $keys, $value );

        $settings[$group] = $item;

        $this->setValues($settings);
    }
}