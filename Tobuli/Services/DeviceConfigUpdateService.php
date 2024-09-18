<?php

namespace Tobuli\Services;

use CustomFacades\Validators\AdminApnConfiguratorFormValidator;
use CustomFacades\Validators\AdminDeviceConfiguratorFormValidator;
use CustomFacades\Validators\DeviceConfiguratorFormValidator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\ApnConfig;
use Tobuli\Entities\DeviceConfig;

class DeviceConfigUpdateService
{
    public function updateConfigs($data)
    {
        if (! is_array($data)) {
            return false;
        }

        if (isset($data['apn'])) {
            $this->updateApnConfigs($data['apn']);
        }

        if (isset($data['device'])) {
            $this->updateDeviceConfigs($data['device']);
        }

        return true;
    }

    public function updateApnConfigs($newConfigs)
    {
        foreach ($newConfigs as $config) {
            AdminApnConfiguratorFormValidator::validate('create', $config);
        }

        return $this->saveConfigs($newConfigs, ['apn_name'], ApnConfig::class);
    }

    public function updateDeviceConfigs($newConfigs)
    {
        foreach ($newConfigs as $config) {
            AdminDeviceConfiguratorFormValidator::validate('create', $config);
        }

        return $this->saveConfigs($newConfigs, ['brand', 'model'], DeviceConfig::class);
    }

    /**
     * Create/update configs
     *
     * @param Collection $newConfigs       New configs
     * @param Array      $comparableFields Fields to filter configs by
     * @param string|Model $class Class of config
     * @return Boolean
     */
    private function saveConfigs($newConfigs, Array $comparableFields, $class)
    {
        if (! $newConfigs instanceof Collection) {
            $newConfigs = collect($newConfigs);
        }

        $serverConfigs = $class::all();

        $existing = $this->getExistingConfigs($serverConfigs, $newConfigs, $comparableFields);
        $new = $this->getNewConfigs($serverConfigs, $newConfigs, $comparableFields);

        DB::transaction(function () use ($new, $class) {
            foreach ($new as $config) {
                $class::create($config);
            }
        });

        DB::transaction(function () use ($existing) {
            foreach ($existing as $config) {
                $config->save();
            }
        });

        return true;
    }

    /**
     * Get existing configs from new, remove edited, updated data to recent
     *
     * @param Collection $serverConfigs    Existing server configs
     * @param Collection $newConfigs       New configs
     * @param Array      $comparableFields Fields to filter configs by
     * @return Collection
     */
    private function getExistingConfigs($serverConfigs, $newConfigs, Array $comparableFields)
    {
        return $serverConfigs->reject(function($serverConfig) { // remove edited configs
                return $serverConfig['edited'];
            })->map(function($serverConfig) use($newConfigs, $comparableFields) { // update server config data with new data
                foreach ($comparableFields as $field) {
                    $newConfigs = $newConfigs->where($field, $serverConfig[$field]);
                }

                $newConfig = $newConfigs->first();

                return $newConfig ? $serverConfig->fill($newConfig) : $serverConfig;
            });
    }

    /**
     * Get not existing configs from new
     *
     * @param Collection $serverConfigs    Existing server configs
     * @param Collection $newConfigs       New configs
     * @param Array      $comparableFields Fields to filter configs by
     * @return Collection
     */
    private function getNewConfigs($serverConfigs, $newConfigs, Array $comparableFields)
    {
        return $newConfigs->reject(function($newConfig) use($serverConfigs, $comparableFields) { //filter new configs that doesn't exist within server configs
            foreach ($comparableFields as $field) {
                $serverConfigs = $serverConfigs->where($field, $newConfig[$field]);
            }

            return $serverConfigs->count();
        });
    }
}
