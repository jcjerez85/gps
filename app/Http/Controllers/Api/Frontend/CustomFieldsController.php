<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Transformers\CustomField\CustomFieldFullTransformer;
use FractalTransformer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Tobuli\Entities\CustomField;

class CustomFieldsController extends BaseController
{

    public function getCustomFields()
    {
        $model = Arr::get(request()->route()->getAction(), 'model');

        $this->validateModel($model);
        $this->checkException('custom_field', 'view');
        $fields = CustomField::filterByModel($model)
            ->get();

        return response()->json(array_merge(
            [
                'status' => 1,
                'model' => $model,
            ],
            FractalTransformer::collection($fields, CustomFieldFullTransformer::class)
                ->toArray()
        ));
    }

    private function validateModel($model)
    {
        if (isset($model) && ! isset(Relation::morphMap()[$model])) {
            throw new ResourseNotFoundException(trans('validation.attributes.model'));
        }
    }
}
