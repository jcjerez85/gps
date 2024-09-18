<?php

namespace Tobuli\Services;

use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Device;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\GeofenceGroup;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Exporters\Util\ExportTypesUtil;
use Tobuli\Traits\ModelUserServiceTrait;

class GeofenceUserService extends GeofenceService
{
    use ModelUserServiceTrait {
        ModelUserServiceTrait::__construct as private modelUserConstruct;
    }

    public function __construct(User $user)
    {
        parent::__construct();
        $this->modelUserConstruct($user, 'geofences');
    }

    protected function normalize(array $data)
    {
        $this->checkDevice($data);

        return parent::normalize($data) + ['user_id' => $this->user->id];
    }

    private function checkDevice(array &$data)
    {
        if (!array_key_exists('device_id', $data)) {
            return;
        }

        if (empty($data['device_id'])) {
            $data['device_id'] = null;

            return;
        }

        $device = Device::find($data['device_id']);

        if ($device && !$this->user->can('view', $device)) {
            unset($data['device_id']);
        }
    }

    public function getExportType(string $type): array
    {
        $selected = null;

        $items = $type === ExportTypesUtil::EXPORT_TYPE_GROUPS
            ? GeofenceGroup::where('user_id', $this->user->id)
                ->pluck('title', 'id')
                ->prepend(trans('front.ungrouped'), '0')
                ->all()
            : Geofence::userAccessible($this->user)
                ->pluck('name', 'id')
                ->all();

        if ($type === ExportTypesUtil::EXPORT_TYPE_ACTIVE) {
            $selected = Geofence::userAccessible($this->user)->where(['active' => 1])
                ->pluck('id', 'id')
                ->all();
        } elseif ($type === ExportTypesUtil::EXPORT_TYPE_INACTIVE) {
            $selected = Geofence::userAccessible($this->user)->where(['active' => 0])
                ->pluck('id', 'id')
                ->all();
        }

        return compact('items', 'selected', 'type');
    }

    /**
     * @param false|int|null|array $id
     * @param false|int|null|array $groupId
     * @param int|string|bool $active
     * @return int
     */
    public function changeActive($id, $groupId, $active = 0): int
    {
        return Geofence::userAccessible($this->user)
            ->when($groupId !== false, function ($query) use ($groupId) {
                if ($groupId) {
                    $groupId = is_array($groupId) ? $groupId : [$groupId];
                    $query->whereIn('group_id', $groupId);
                } else {
                    $query->whereNull('group_id');
                }
            })
            ->when($id !== false, function ($query) use ($id) {
                if ($id) {
                    $id = is_array($id) ? $id : [$id];
                    $query->whereIn('id', $id);
                }
            })
            ->update([
                'active' => filter_var($active, FILTER_VALIDATE_BOOLEAN)
            ]);
    }
}
