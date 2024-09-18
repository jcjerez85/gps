<?php namespace App\Http\Controllers\Admin;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;

use Validator;
use Tobuli\Exceptions\ValidationException;
use App\Exceptions\ResourseNotFoundException;

class LanguagesController extends BaseController
{
    private $section = 'languages';
    private $languages;

    function __construct()
    {
        parent::__construct();

        $this->languages = Arr::sort(settings('languages'), function($language){
            return $language['title'];
        });
    }

    public function index()
    {
        $data = [
            'section' => $this->section,
            'languages' => $this->languages,
        ];

        return View::make('admin::'.ucfirst($this->section).'.' . (request()->ajax() ? 'table' : 'index'))->with($data);
    }

    public function edit($lang)
    {
        $language = settings('languages.'.$lang);

        if (empty($language))
            throw new ResourseNotFoundException('global.language');

        $flags = [];

        $files = File::allFiles(public_path('assets/images/header/'));
        foreach ($files as $file) {
            $flags[$file->getRelativePathname()] = asset("assets/images/header/{$file->getRelativePathname()}");
        }

        return View::make('admin::Languages.edit')->with(compact('language', 'flags'));
    }

    public function update($lang)
    {
        $input = request()->all(['active', 'title', 'flag']);

        $validator = Validator::make($input, [
            'title' => 'required',
            'flag'  => 'required',
        ]);

        $language = settings('languages.'.$lang);

        if (empty($language))
            throw new ResourseNotFoundException('global.language');

        if ($validator->fails())
            throw new ValidationException($validator->errors());

        if ( ! File::exists(public_path("assets/images/header/{$input['flag']}")))
            throw new ValidationException(['flag' => trans('validation.exists')]);

        if ( empty($input['active']) && settings('main_settings.default_language') == $language['key'])
            throw new ValidationException(['active' => trans('validation.attributes.default_language')]);

        $language = array_merge($language, $input);

        settings('languages.'.$lang, $language);

        return ['status' => 1, 'message' => trans('front.successfully_saved')];
    }
}
