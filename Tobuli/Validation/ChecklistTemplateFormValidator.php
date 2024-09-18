<?php namespace Tobuli\Validation;

use Tobuli\Entities\ChecklistTemplate;

class ChecklistTemplateFormValidator extends Validator {

    public $rules = [
        'store' => [
            'name' => 'required|string',
            'type' => 'required|integer|in:'.ChecklistTemplate::TYPE_PRE_START.','.ChecklistTemplate::TYPE_SERVICE,
            'rows' => 'array|min:1|max:50',
        ],

        'update' => [
            'name' => 'required|string',
            'type' => 'required|integer|in:'.ChecklistTemplate::TYPE_PRE_START.','.ChecklistTemplate::TYPE_SERVICE,
            'rows' => 'array|min:1|max:50',
        ]
    ];
}
