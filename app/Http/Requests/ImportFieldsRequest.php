<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Tobuli\Importers\ImportUtils;

class ImportFieldsRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model' => 'required|' . Rule::in(array_keys(ImportUtils::getModelImportManagers())),
            'file' => 'required|file',
        ];
    }
}
