<?php

namespace Tobuli\Validation;

use Tobuli\Entities\ChecklistRow;

class ChecklistValidator extends Validator
{
    public $rules = [
        'create' => [
            'service_id' => 'required|exists:device_services,id',
            'template_id' => 'required|exists:checklist_template,id',
        ],
        'upload' => [
            'file' => 'required|image',
        ],
        'sign' => [
            'signature' => 'required',
            'notes' => 'nullable|string'
        ],
        'delete_file' => [
            'filename' => 'required',
        ],
        'outcome' => [
            'outcome' => 'required|in:' . ChecklistRow::OUTCOME_PASS . ',' . ChecklistRow::OUTCOME_FAIL,
        ],
    ];
}
