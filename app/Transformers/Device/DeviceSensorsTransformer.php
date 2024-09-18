<?php

namespace App\Transformers\Device;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Device;

class DeviceSensorsTransformer extends DeviceTransformer {

    public function transform(Device $entity)
    {
        $sensors = [];

        foreach ($entity->sensors as $sensor) {
            $value = $sensor->getValueCurrent($entity->other);

            $sensors[] = [
                'id'       => (int)$sensor->id,
                'type'     => $sensor->type,
                'name'     => $sensor->formatName(),
                'value'    => $value,
                'unit'     => $sensor->getUnit(),
                'formated' => $sensor->formatValue($value),
            ];
        }

        return $sensors;
    }
}