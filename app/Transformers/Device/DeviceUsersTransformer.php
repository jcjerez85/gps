<?php

namespace App\Transformers\Device;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Device;

class DeviceUsersTransformer extends DeviceTransformer {

    public function transform(Device $entity) {

        $users = $entity->users->filter(function ($user) {
            return $this->user->can('show', $user);
        });

        $result = [];

        foreach ($users as $user) {
            $result[] = [
                'id'       => (int)$user->id,
                'email'    => $user->email,
            ];
        }

        return $result;
    }
}