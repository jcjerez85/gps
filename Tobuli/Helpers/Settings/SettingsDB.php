<?php

namespace Tobuli\Helpers\Settings;

use Illuminate\Support\Facades\DB;
use Cache;

class SettingsDB extends Settings {

    const CACHE_SECONDS = 15 * 60;

    protected $prefix = 'SettingsDB';

    protected function _has($key) {
        if (empty($key))
            return false;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return false;

        $item = Cache::store('array')->remember('settings.'.$group, self::CACHE_SECONDS, function() use ($group) {
            return DB::table('configs')->where('title', '=', $group)->first();
        });

        if (empty($item))
            return false;

        try {
            $has = has_array_value( unserialize($item->value), $keys );
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

        $item = Cache::store('array')->remember('settings.'.$group, self::CACHE_SECONDS, function() use ($group) {
            return DB::table('configs')->where('title', '=', $group)->first();
        });

        if (empty($item))
            return null;

        try {
            $value = get_array_value( unserialize($item->value), $keys );
        } catch (\Exception $e) {
            $value = $item->value;
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

        Cache::store('array')->forget('settings.'.$group);

        $item = DB::table('configs')->where('title', '=', $group)->first();

        if (empty($item))
            DB::table('configs')->insert(['title' => $group, 'value' => '']);

        try {
            $group_value = unserialize($item->value);
        } catch (\Exception $e) {}

        if (empty($group_value))
            $group_value = [];


        set_array_value( $group_value, $keys, $value );

        if ( is_array($group_value) ) {
            $value = serialize( $group_value );
        }

        DB::beginTransaction();

        DB::table('configs')->where('title', '=', $group)->update(['value' => $value]);
        $stored = DB::table('configs')->where('title', '=', $group)->first(['value']);

        if ($stored->value == $value) {
            DB::commit();

            return true;
        }

        DB::rollBack();

        return false;
    }
}