<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Importers\Geofence\GeofenceImportManager;

class GeofencesImportController extends Controller
{
    public function index()
    {
        return view('front::Geofences.import');
    }

    public function store(GeofenceImportManager $importManager)
    {
        $this->checkException('geofences', 'store');

        $validator = Validator::make(request()->all(), [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $importManager->import(request()->file('file'));

        return Response::json([
            'status' => 1,
            'message' => trans('front.successfully_updated_geofence')
        ]);
    }
}
