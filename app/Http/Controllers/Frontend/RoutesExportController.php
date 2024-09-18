<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Exporters\EntityManager\Route\ExportManager;
use Tobuli\Exporters\Util\ExportTypesUtil;

class RoutesExportController extends Controller
{
    public function index()
    {
        $exportTypes = ExportTypesUtil::getTranslations([ExportTypesUtil::EXPORT_TYPE_GROUPS]);
        $exportFormats = [
            'csv' => 'CSV',
            'gexp' => 'GEXP',
            'kml' => 'KML',
        ];
        $routes = $this->user->routes()->pluck('name', 'id')->all();

        return view('front::Routes.export')->with(compact('exportTypes', 'exportFormats', 'routes'));
    }

    public function store(): BinaryFileResponse
    {
        $attributes = ['id', 'active', 'name', 'coordinates', 'color'];
        $routes = $this->user->routes()->getQuery();

        return (new ExportManager($routes))
            ->applyFilter($this->data['export_type'], $this->data)
            ->download($attributes, $this->data['export_format']);
    }

    public function getType()
    {
        $type = $this->data['type'];
        $selected = null;

        $items = $this->user->routes()
            ->pluck('name', 'id')
            ->all();

        if ($type === ExportTypesUtil::EXPORT_TYPE_ACTIVE) {
            $selected = $this->user->routes()
                ->where('active', 1)
                ->pluck('id', 'id')
                ->all();
        } elseif ($type === ExportTypesUtil::EXPORT_TYPE_INACTIVE) {
            $selected = $this->user->routes()
                ->where('active', 0)
                ->pluck('id', 'id')
                ->all();
        }

        $data = compact('items', 'selected');

        if ($this->api) {
            return $data;
        }

        return view('front::Routes.exportType')->with($data);
    }
}
