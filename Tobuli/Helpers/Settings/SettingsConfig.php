<?php

namespace Tobuli\Helpers\Settings;

use Config;

class SettingsConfig extends Settings {

    protected $prefix = 'SettingsConfig';

    protected $main = 'tobuli';

    protected function _has($key) {
        if (empty($key))
            return false;

        return Config::has("{$this->main}.$key");
    }

    protected function _get($key) {
        if (empty($key))
            return null;

        return Config::get("{$this->main}.$key");
    }

    protected function _set($key, $value) {
        return;
    }
}