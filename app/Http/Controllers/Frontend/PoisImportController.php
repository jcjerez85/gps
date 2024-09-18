<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\MapIcon;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Importers\POI\POIImportManager;

class PoisImportController extends Controller
{
    public function index()
    {
        $icons = MapIcon::all();

        return view('front::Pois.import')->with(compact('icons'));
    }

    public function store(POIImportManager $importManager)
    {
        $this->checkException('poi', 'store');

        $validator = Validator::make(request()->all(), [
            'map_icon_id' => 'required',
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $additionals = request()->all(['map_icon_id']);
        $importManager->import(request()->file('file'), $additionals);

        return Response::json(['status' => 1]);
    }
}
