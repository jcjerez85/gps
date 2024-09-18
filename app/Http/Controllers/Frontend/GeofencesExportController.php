<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Entities\Geofence;
use Tobuli\Exporters\EntityManager\Geofence\ExportManager;
use Tobuli\Exporters\Util\ExportTypesUtil;
use Tobuli\Services\GeofenceUserService;

class GeofencesExportController extends Controller
{
    public function index()
    {
        $exportTypes = ExportTypesUtil::getTranslations();
        $geofences = Geofence::userOwned($this->user)->pluck('name', 'id')->all();

        return view('front::Geofences.export')->with(compact('geofences', 'exportTypes'));
    }

    public function store(): BinaryFileResponse
    {
        $attributes = [
            'id',
            'group_id',
            'name',
            'coordinates',
            'polygon_color',
            'type',
            'radius',
            'center',
            'device_id',
        ];

        $geofences = Geofence::userOwned($this->user)->with('group:id,title');

        return (new ExportManager($geofences))
            ->applyFilter($this->data['export_type'], $this->data)
            ->download($attributes, 'gexp');
    }

    public function getType()
    {
        $type = $this->data['type'];

        $data = (new GeofenceUserService($this->user))->getExportType($type);

        $this->data = $type === ExportTypesUtil::EXPORT_TYPE_GROUPS ? 'groups' : 'geofences';

        $input = $this->data;

        return view('front::Geofences.exportType')->with(array_merge($data, compact('input')));
    }
}
