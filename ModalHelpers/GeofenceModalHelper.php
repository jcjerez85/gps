<?php namespace ModalHelpers;

use CustomFacades\Repositories\GeofenceGroupRepo;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Device;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\GeofenceGroup;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Exporters\Util\ExportTypesUtil;
use Tobuli\Services\GeofenceService;

class GeofenceModalHelper extends ModalHelper
{
    protected $geofenceService;

    public function __construct()
    {
        parent::__construct();

        $this->geofenceService = new GeofenceService();
    }

    public function get()
    {
        try {
            $this->checkException('geofences', 'view');
        } catch (\Exception $e) {
            return ['geofences' => []];
        }

        $geofences = Geofence::userAccessible($this->user)->get();

        if ($this->api) {
            return compact('geofences');
        }

        $groups_opened = json_decode($this->user->open_geofence_groups, TRUE);

        $groups = GeofenceGroupRepo::getWhere(['user_id' => $this->user->id])
            ->prepend(new GeofenceGroup([
                'id'    => 0,
                'title' => trans('front.ungrouped')
            ]))
            ->mapWithKeys(function($group) use ($groups_opened) {
                $group_id = $group->id ?? 0;

                return [$group_id => [
                    'id'      => $group_id,
                    'title'   => $group->title,
                    'open'    => ($groups_opened && in_array($group_id, $groups_opened)),
                ]];
            })
            ->all();

        $grouped = $geofences->groupBy('group_id');

        return compact('grouped', 'groups');
    }

    public function create()
    {
        $this->checkException('geofences', 'store');

        $this->validate();

        $geofence = $this->geofenceService->create($this->data + ['user_id' => $this->user->id]);

        return ['status' => 1, 'item' => $geofence];
    }

    public function edit()
    {
        $item = Geofence::find($this->data['id']);

        $this->checkException('geofences', 'update', $item);

        $this->validate();

        $geofence = $this->geofenceService->edit($item, $this->data);

        return ['status' => 1];
    }

    private function validate()
    {
        if (array_key_exists('device_id', $this->data)) {
            if (empty($this->data['device_id']))
                $this->data['device_id'] = null;

            if ($this->data['device_id'] && $device = Device::find($this->data['device_id']))
                if (!$this->user->can('view', $device))
                    unset($this->data['device_id']);
        }
    }

    public function changeActive()
    {
        $validator = Validator::make($this->data, [
            'id' => 'required_without:group_id',
            'group_id' => 'required_without:id',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator->errors());


        Geofence::userAccessible($this->user)
            ->when(array_key_exists('group_id', $this->data), function($query) {
                if ($group_id = $this->data['group_id']) {
                    $group_id = is_array($group_id) ? $group_id : [$group_id];
                    $query->whereIn('group_id',$group_id);
                } else {
                    $query->whereNull('group_id');
                }
            })
            ->when(array_key_exists('id', $this->data) , function($query) {
                if ($id = $this->data['id']) {
                    $id = is_array($id) ? $id : [$id];
                    $query->whereIn('id',$id);
                }
            })
            ->update([
                'active' => filter_var($this->data['active'] ?? 0, FILTER_VALIDATE_BOOLEAN)
            ]);

        return ['status' => 1];
    }

    public function exportType()
    {
        $type = $this->data['type'];
        $selected = null;

        $items = Geofence::userAccessible($this->user)
            ->pluck('name', 'id')
            ->all();

        if ($type === ExportTypesUtil::EXPORT_TYPE_GROUPS) {
            $items = GeofenceGroupRepo::getWhere(['user_id' => $this->user->id])
                ->pluck('title', 'id')
                ->prepend(trans('front.ungrouped'), '0')
                ->all();
        } elseif ($type === ExportTypesUtil::EXPORT_TYPE_ACTIVE) {
            $selected = Geofence::userAccessible($this->user)->where(['active' => 1])
                ->pluck('id', 'id')
                ->all();
        } elseif ($type === ExportTypesUtil::EXPORT_TYPE_INACTIVE) {
            $selected = Geofence::userAccessible($this->user)->where(['active' => 0])
                ->pluck('id', 'id')
                ->all();
        }

        $data = compact('items', 'selected', 'type');
        if ($this->api) {
            return $data;
        }
        else {
            $this->data = $type === ExportTypesUtil::EXPORT_TYPE_GROUPS ? 'groups' : 'geofences';
            
            $input = $this->data;
            
            return view('front::Geofences.exportType')->with(array_merge($data, compact('input')));
        }
    }

    public function destroy()
    {
        $id = array_key_exists('geofence_id', $this->data) ? $this->data['geofence_id'] : $this->data['id'];

        $item = Geofence::find($id);

        $this->checkException('geofences', 'remove', $item);

        $this->geofenceService->delete($item);
        
        return ['status' => 1];
    }
}