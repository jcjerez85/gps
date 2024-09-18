<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Tobuli\Services\TranslationService;
use Tobuli\Exceptions\ValidationException;

class TranslationsController extends BaseController
{
    private $languages;
    private $files;
    private $translationService;

    function __construct(TranslationService $translationService)
    {
        parent::__construct();

        $this->languages = Arr::sort(settings('languages'), function($language){
            return $language['title'];
        });

        $this->translationService = $translationService;
        $this->files = [
            'all' => trans('admin.all_trans'),
            'front' => trans('admin.front_trans'),
            'admin' => trans('admin.admin_trans'),
            'global' => trans('admin.global_trans'),
            'validation' => trans('admin.validation_trans'),
            'reminders' => trans('admin.reminders_trans'),
            'passwords' => trans('admin.passwords_trans'),
            'errors' => trans('admin.errors_trans'),
            'auth' => trans('admin.auth_trans'),
        ];
    }

    public function index()
    {
        return View::make('admin::Translations.index')->with(['languages' => $this->languages]);
    }

    public function show($lang)
    {
        $lang = substr($lang, 0, 2);
        $files = $this->files;

        $language = settings('languages.'.$lang);

        return View::make('admin::Translations.show')->with(compact('files', 'lang', 'language'));
    }

    public function save()
    {
        $rules = [
            'lang' => 'required|is_language',
            'trans' => 'required|array',
        ];
        $data = request()->all();

        if (isset($data['trans'])) {
            $errors = [];

            foreach ($data['trans'] as $key => $translation) {
                $placeholders = $this->translationService->getPlaceholders($key);

                if ($placeholders) {
                    foreach ($placeholders as $placeholder) {
                        if (strpos($translation, $placeholder) === false) {
                            $errors['trans.'.$key][] = trans('validation.placeholder', ['placeholder' => $placeholder]);
                        }
                    }
                }
            }

            if ($errors) {
                throw new ValidationException($errors);
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        try {
            $this->translationService->save($data['lang'], $data['trans']);
        } catch (\Exception $e) {
            return ['status' => 0, 'message' => $e->getMessage()];
        }

        return ['status' => 1, 'message' => trans('front.successfully_saved')];;
    }

    public function fileTrans()
    {
        $data = request()->all();
        $validator = Validator::make($data,
            [
                'lang' => 'required|is_language',
                'file' => 'required|translation_file',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $file = $data['file'];
        $lang = $data['lang'];

        $translations = [];

        if ($file == 'all') {
            $translations = $this->files;
            array_shift($translations);
        } else {
            $translations[$file] = 0;
        }

        foreach ($translations as $currFile => $value) {
            $translations[$currFile] = [
                'english' => $this->translationService->getTranslations($currFile, 'en', true),
                'original' => $this->translationService->getTranslations($currFile, $lang, true),
                'current' => $this->translationService->getTranslations($currFile, $lang),
            ];
        }

        return View::make('admin::Translations.trans')->with(compact('file', 'lang','translations'));
    }
}
