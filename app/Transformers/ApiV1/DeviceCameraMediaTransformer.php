<?php

namespace App\Transformers\ApiV1;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\File\DeviceCameraMedia;
use Formatter;

class DeviceCameraMediaTransformer extends BaseTransformer
{
    private static $mediaCategoriesPermission;
    private static $cameraPermission;

    /**
     * @return array|null
     */
    public function transform(DeviceCameraMedia $entity)
    {
        $data = [
            'url' => route('api.device_media.get_file', [
                'id' => DeviceCameraMedia::$entity->id,
                'file_name' => $entity->name,
            ]),
            'size' => $entity->size,
            'created_at' => Formatter::time()->convert($entity->created_at),
        ];

        if ($this->isMediaCategoriesPerm()) {
            $data['category'] = $entity->category;
        }

        if ($this->isCameraPerm()) {
            $data['camera_id'] = $entity->camera_id;
            $data['camera_name'] = $entity->camera_name;
        }

        return $data;
    }

    private function isCameraPerm(): bool
    {
        if (!isset(self::$cameraPermission)) {
            self::$cameraPermission = $this->user->perm('device_camera', 'view');
        }

        return self::$cameraPermission;
    }

    private function isMediaCategoriesPerm(): bool
    {
        if (!isset(self::$mediaCategoriesPermission)) {
            self::$mediaCategoriesPermission = $this->user->perm('media_categories', 'view');
        }

        return self::$mediaCategoriesPermission;
    }
}
