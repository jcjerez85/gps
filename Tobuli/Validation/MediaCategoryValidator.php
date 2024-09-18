<?php

namespace Tobuli\Validation;

use Tobuli\Entities\MediaCategory;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;

class MediaCategoryValidator extends Validator
{
    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'title' => 'required|string|max:255',
            'user_id' => 'exists:users,id',
        ],
        'update' => [
            'title' => 'sometimes|required|string|max:255',
            'user_id' => 'exists:users,id',
        ]
    ];
}
