<?php

namespace App\Transformers\Device;

use Formatter;
use App\Transformers\BaseTransformer;
use Tobuli\Entities\Device;

class DeviceLookupTransformer extends DeviceTransformer {

    public function transform(Device $entity)
    {
        $expirationDate = $this->canView($entity, 'expiration_date');
        $expirationDate = $expirationDate ? Formatter::time()->convert($expirationDate) : null;

        return [
            'id'                  => (int)$entity->id,
            'active'              => (boolean)$entity->active,
            'name'                => $entity->name,
            'imei'                => $this->canView($entity, 'imei'),
            'sim_number'          => $this->canView($entity, 'sim_number'),
            'device_model'        => $entity->device_model,
            'plate_number'        => $entity->plate_number,
            'vin'                 => $entity->vin,
            'registration_number' => $entity->registration_number,
            'object_owner'        => $entity->object_owner,
            'additional_notes'    => $entity->additional_notes,
            'protocol'            => $this->canView($entity, 'protocol'),
            'expiration_date'     => $expirationDate,
        ];
    }
}