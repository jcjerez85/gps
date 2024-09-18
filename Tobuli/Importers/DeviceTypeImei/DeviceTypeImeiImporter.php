<?php

namespace Tobuli\Importers\DeviceTypeImei;

use Tobuli\Entities\DeviceTypeImei;
use Tobuli\Importers\Importer;


class DeviceTypeImeiImporter extends Importer
{
    protected $defaults = [];

    protected function getDefaults()
    {
        return $this->defaults;
    }

    protected function importItem($data, $attributes = [])
    {
        $data = $this->mergeDefaults($data);
        $data = array_merge($data, $attributes);

        if ( ! $this->validate($data)) {
            return;
        }

        $this->normalize($data);

        if ($this->getItem($data)) {
            return;
        }

        $this->create($data);
    }

    private function normalize(array &$data): array
    {
        return $data;
    }

    private function getItem($data)
    {
        return DeviceTypeImei::where('imei', $data['imei'])->first();
    }

    private function create($data)
    {
        beginTransaction();
        try {
            DeviceTypeImei::create($data);
        } catch (\Exception $e) {
            rollbackTransaction();
            throw $e;
        }
        commitTransaction();
    }

    public function getValidationBaseRules(): array
    {
        return [
            'imei'           => 'required|string|unique:device_type_imeis,imei',
            'msisdn'         => 'required|regex:/^\d{6,20}$/',
            'device_type_id' => 'required',
        ];
    }
}
