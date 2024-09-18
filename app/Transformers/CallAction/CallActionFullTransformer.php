<?php
namespace App\Transformers\CallAction;

use Tobuli\Entities\CallAction;

class CallActionFullTransformer extends CallActionTransformer {

    /**
     * @param CallAction $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'            => (int) $entity->id,
            'user_id'       => (int) $entity->user_id,
            'device_id'     => (int) $entity->device_id,
            'event_id'      => (int) $entity->event_id,
            'alert_id'      => (int) $entity->alert_id,
            'called_at'     => (string) $entity->called_at,
            'response_type' => (string) $entity->response_type,
            'remarks'       => (string) $entity->remarks,
            'created_at'    => (string) $entity->created_at,
            'updated_at'    => (string) $entity->updated_at,
        ];
    }
}
