<?php

if (! function_exists('settings')) {
    /**
    * @param  array|string  $key
    * @param  array|string  $value
    * @return mixed
    */
    function settings($key = null, $value = null)
    {
        if (is_null($key)) {
            return app('Tobuli\Helpers\Settings\SettingsDB');
        }

        if (is_null($value)) {
            return app('Tobuli\Helpers\Settings\SettingsDB')->get($key);
        }

        return app('Tobuli\Helpers\Settings\SettingsDB')->set($key, $value);
    }
}