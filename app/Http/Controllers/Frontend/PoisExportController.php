<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Exporters\EntityManager\Poi\ExportManager;
use Tobuli\Exporters\Util\ExportTypesUtil;

class PoisExportController extends Controller
{
    public function index()
    {
        $exportTypes = ExportTypesUtil::getTranslations();
        $exportFormats = [
            'csv' => 'CSV',
            'gexp' => 'GEXP',
            'kml' => 'KML',
        ];
        $pois = $this->user->pois()->pluck('name', 'id')->all();

        return view('front::Pois.export')->with(compact('exportTypes', 'exportFormats', 'pois'));
    }

    public function store(): BinaryFileResponse
    {
        $attributes = ['id', 'icon_id', 'icon', 'group_id', 'active', 'name', 'description', 'coordinates'];
        $pois = $this->user->pois()->with('mapIcon:id,path')->getQuery();

        return (new ExportManager($pois))
            ->applyFilter($this->data['export_type'], $this->data)
            ->download($attributes, $this->data['export_format']);
    }

    public function getType()
    {
        $type = $this->data['type'];
        $selected = null;

        $items = $this->user->pois()
            ->pluck('name', 'id')
            ->all();

        if ($type === ExportTypesUtil::EXPORT_TYPE_GROUPS) {
            $items = $this->user->poiGroups()
                ->pluck('title', 'id')
                ->prepend(trans('front.ungrouped'), '0')
                ->all();
        } elseif ($type === ExportTypesUtil::EXPORT_TYPE_ACTIVE) {
            $selected = $this->user->pois()
                ->where('active', 1)
                ->pluck('id', 'id')
                ->all();
        } elseif ($type === ExportTypesUtil::EXPORT_TYPE_INACTIVE) {
            $selected = $this->user->pois()
                ->where('active', 0)
                ->pluck('id', 'id')
                ->all();
        }

        $data = compact('items', 'selected', 'type');

        if ($this->api) {
            return $data;
        }

        $this->data = $type === ExportTypesUtil::EXPORT_TYPE_GROUPS ? 'groups' : 'pois';

        $input = $this->data;

        return view('front::Pois.exportType')->with(array_merge($data, compact('input')));
    }
}
