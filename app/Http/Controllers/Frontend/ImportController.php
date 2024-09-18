<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportFieldsRequest;
use Illuminate\Support\Facades\View;
use Tobuli\Importers\ImportUtils;

set_time_limit(300);

class ImportController extends Controller
{
    private $importUtils;

    public function __construct(ImportUtils $importUtils)
    {
        $this->importUtils = $importUtils;

        parent::__construct();
    }

    public function getFields(ImportFieldsRequest $request): \Illuminate\Contracts\View\View
    {
        $importManager = $this->importUtils->resolveImporterManager($request->get('model'));

        $importer = $importManager->getImporter();

        $fileFields = $importManager->getImportFields($request->file('file'));
        $validationRules = $importer->getValidationRules();
        array_walk($validationRules, function (&$item) {
            $item = explode('|', $item);
        });

        return View::make('front::Import.Partials.fields_map')->with([
            'importFields' => $this->importUtils->getDefaultValues($importer->getImportFields(), $fileFields),
            'fileHeaders' => ['' => ''] + array_combine($fileFields, $fileFields),
            'fieldDescriptions' => $importer->getFieldDescriptions(),
            'validationRules' => $validationRules,
        ]);
    }
}
