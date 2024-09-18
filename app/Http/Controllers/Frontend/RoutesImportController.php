<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Importers\Route\RouteImportManager;

class RoutesImportController extends Controller
{
    public function index()
    {
        return view('front::Routes.import');
    }

    public function store(RouteImportManager $importManager)
    {
        $this->checkException('routes', 'store');

        $validator = Validator::make(request()->all(), [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $importManager->import(request()->file('file'));

        return Response::json(['status' => 1]);
    }
}
