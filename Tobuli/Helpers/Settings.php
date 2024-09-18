<?php

namespace Tobuli\Helpers;

use Illuminate\Support\Facades\DB;
use Cache;

class Settings {

    private $cache;

    public function get($key) {
        return $this->merge($key);
    }

    public function set($key, $value) {
        $this->cache = [];

        return $this->setDB($key, $value);
    }

    public function has($key) {
        return ! is_null( $this->get($key) );
    }

    public function merge($key) {
        if ( isset($this->cache[$key]) ) {
            return $this->cache[$key];
        }

        // from config
        $cfg_value = $this->getConfig($key);

        // from db
        if ($this->hasDB($key))
        {
            $db_value = $this->getDB($key);

            if (is_array($db_value))
            {
                $cfg_value = $cfg_value ? $cfg_value : [];
                $value = array_merge_recursive_distinct($cfg_value, $db_value);
            } else
            {
                $value = $db_value;
            }
        } else
        {
            $value = $cfg_value;
        }

        $this->cache[$key] = $value;

        return $value;
    }

    private function hasDB($key) {
        if (empty($key))
            return false;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return false;

        try {
            $item = Cache::remember('settings.'.$group, 15 * 60, function() use ($group) {
                return DB::table('configs')->where('title', '=', $group)->first();
            });
        }
        catch (\Exception $e) {
            $item = false;
        }

        if (empty($item))
            return false;

        try {
            $has = $this->has_array_value( unserialize($item->value), $keys );
        }
        catch (\Exception $e) {
            $has = true;
        }

        return $has;
    }

    private function getDB($key) {
        if (empty($key))
            return null;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return null;

        try {
            $item = Cache::remember('settings.'.$group, 15 * 60, function() use ($group) {
                return DB::table('configs')->where('title', '=', $group)->first();
            });
        }
        catch (\Exception $e) {
            $item = null;
        }

        if (empty($item))
            return null;

        try {
            $value = $this->get_array_value( unserialize($item->value), $keys );
        }
        catch (\Exception $e) {
            $value = $item->value;
        }

        return $value;
    }

    private function setDB($key, $value) {
        if (empty($key))
            return false;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return false;

        Cache::forget('settings.'.$group);

        $item = DB::table('configs')->where('title', '=', $group)->first();

        if (empty($item))
            DB::table('configs')->insert(['title' => $group, 'value' => '']);

        try {
            $group_value = unserialize($item->value);
        }
        catch (\Exception $e) {}

        if (empty($group_value))
            $group_value = [];


        $this->set_array_value( $group_value, $keys, $value );

        if ( is_array($group_value) ) {
            $value = serialize( $group_value );
        }

        return DB::table('configs')->where('title', '=', $group)->update(['value' => $value]);
    }

    private function getConfig($key) {
        return config('tobuli.' . $key);
    }

    private function has_array_value($array, $keys) {
        if (empty($keys))
            return true;

        $key = array_shift($keys);

        if (array_key_exists($key, $array))
            return $this->has_array_value( $array[$key], $keys );
        else
            return false;
    }

    private function get_array_value($array, $keys) {
        if (empty($keys))
            return $array;

        $key = array_shift($keys);

        if (array_key_exists($key, $array))
            return $this->get_array_value( $array[$key], $keys );
        else
            return null;
    }

    private function set_array_value(&$array, $keys, $value) {
        if (empty($keys))
            return $array = $value;

        $key = array_shift($keys);

        if (!array_key_exists($key, $array))
            $array[$key] = null;

        return $this->set_array_value( $array[$key], $keys, $value );
    }
}