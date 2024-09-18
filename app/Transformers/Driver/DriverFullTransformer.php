<?php

namespace App\Transformers\Driver;

use Tobuli\Entities\UserDriver AS Driver;

class DriverFullTransformer extends DriverTransformer {

    /**
     * @param Driver $entity
     * @return array|null
     */
    public function transform($entity) {
        if ( ! $entity)
            return null;

        return [
            'id'          => intval($entity->id),
            'user_id'     => intval($entity->user_id),
            'device_id'   => intval($entity->device_id),
            'name'        => $entity->name,
            'rfid'        => $entity->rfid,
            'phone'       => $entity->phone,
            'email'       => $entity->email,
            'description' => $entity->description,
        ];
    }
}