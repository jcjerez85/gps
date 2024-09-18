<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use ModalHelpers\GeofenceGroupsModalHelper;
use Tobuli\Repositories\GeofenceGroup\GeofenceGroupRepositoryInterface as GeofenceGroup;
use Tobuli\Services\UserOpenGroupService;

class GeofencesGroupsController extends Controller {

    public function index(GeofenceGroupsModalHelper $geofenceGroupsModalHelper, GeofenceGroup $geofenceGroupRepo) {
        $groups = $geofenceGroupsModalHelper->paginated(Auth::User(), 0, $geofenceGroupRepo);

        return view('front::GeofencesGroups.index')->with(compact('groups'));
    }

    public function store(GeofenceGroupsModalHelper $geofenceGroupsModalHelper, GeofenceGroup $geofenceGroupRepo) {
        $input = Request::all();

        return response()->json(array_merge($geofenceGroupsModalHelper->edit($input, Auth::User(), 0, $geofenceGroupRepo), ['trigger' => 'updateGeofenceGroupsSelect', 'url' => route('geofences_groups.update_select')]));
    }

    public function updateSelect(GeofenceGroup $geofenceGroupRepo) {
        $input = Request::all();

        $geofence_groups = $geofenceGroupRepo
            ->getWhere(['user_id' => $this->user->id])
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

        return Form::select('group_id', $geofence_groups, isset($input['group_id']) ? $input['group_id'] : null, ['class' => 'form-control']);
    }

    public function changeStatus()
    {
        $id = $this->data['id'];

        (new UserOpenGroupService($this->user->geofenceGroups()))
            ->changeStatus($id);
    }
}
