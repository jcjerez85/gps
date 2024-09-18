<?php

namespace App\Http\Controllers\Admin;

use CustomFacades\Validators\ExternalUrlFormValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Tobuli\Exceptions\ValidationException;

class ExternalUrlController extends BaseController
{
    public function index()
    {
        return View::make('admin::ExternalUrl.index')->with([
            'params' => settings('external_url'),
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->only(['enabled', 'external_url']);
        $input['external_url'] = trim($input['external_url']);

        try {
            ExternalUrlFormValidator::validate('update', $input);
        } catch (ValidationException $e) {
            return Redirect::back()->withErrors($e->getErrors());
        }

        settings('external_url', $input);

        return Redirect::back()->withSuccess(trans('front.successfully_saved'));
    }
}
