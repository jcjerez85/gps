<?php

namespace Tobuli\Services;

use App\Exceptions\ResourseNotFoundException;
use Illuminate\Support\Str;
use Tobuli\Entities\Device;

class SimBlockingService
{
    private $simProviders = [];
    private $simProvider;

    public function __construct()
    {
        $this->simProvider = $this->getSimProvider();
    }

    public function block(Device $device)
    {
        if (! $this->simProvider) {
            return false;
        }

        return $this->simProvider->block($device);
    }

    public function unblock(Device $device)
    {
        if (! $this->simProvider) {
            return false;
        }

        return $this->simProvider->unblock($device);
    }

    public function getProviderNames()
    {
        return array_map(function($value) {
            return $value->getName();
        }, $this->simProviders);
    }

    private function getSimProvider()
    {
        if (! $this->simProviders) {
            $this->setSimProviders();
        }

        return $this->simProviders[settings('plugins.sim_blocking.options.provider')] ?? null;
    }

    private function setSimProviders()
    {
        $providers = [];
        $classes = preg_grep('/^((?!SimProvider\.php)[\s\S])*$/',
            glob(Str::finish(base_path('Tobuli/Services/SimProviders'), '/').'?*Provider.php'));


        foreach ($classes as $class) {
            $name = pathinfo($class, PATHINFO_FILENAME);
            $basename = 'Tobuli\Services\SimProviders\\'.$name;
            $providers[Str::snake(str_replace('Provider', '', $name))] = new $basename();
        }

        $this->simProviders = $providers;
    }
}
