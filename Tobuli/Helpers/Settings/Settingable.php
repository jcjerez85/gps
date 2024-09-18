<?php
/**
 * Created by PhpStorm.
 * User: Linas
 * Date: 11/22/2017
 * Time: 6:42 PM
 */

namespace Tobuli\Helpers\Settings;

use Tobuli\Helpers\Settings\SettingsModel;

trait Settingable
{
    protected $instanceSettings;

    protected function initSetting()
    {
        if ( ! $this->instanceSettings) {
            $this->instanceSettings = new SettingsModel();
        }

        $prefix = $this->getSettigsPrefix();

        $this->instanceSettings->setPrefix($prefix);

        return $this->instanceSettings;
    }

    public function getSettigsPrefix()
    {
        return 'Settings' . get_class($this) . $this->getKey();
    }

    public function getSettingsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = json_encode($value);
    }

    public function getSettings($key) {
        $this->initSetting()->setValues( $this->settings );

        return $this->initSetting()->get($key);
    }

    public function setSettings($key, $value) {
        if (empty($key))
            return false;

        $keys = explode('.', $key);

        $group = array_shift($keys);

        if (empty($group))
            return false;

        $settings = $this->settings;

        $item = empty($settings[$group]) ? [] : $settings[$group];

        set_array_value( $item, $keys, $value );

        $settings[$group] = $item;

        $this->settings = $settings;

        $this->save();
    }
}