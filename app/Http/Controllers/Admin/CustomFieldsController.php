<?php namespace App\Http\Controllers\Admin;

use App\Exceptions\ResourseNotFoundException;
use CustomFacades\Validators\CustomFieldFormValidator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Tobuli\Entities\CustomField;

class CustomFieldsController extends BaseController
{
    public function index($model) {
        $this->validateModel($model);
        $this->checkException('custom_field', 'view');
        $fields = CustomField::filterByModel($model)->paginate(15);

        return view('admin::CustomFields.index')
            ->with([
                'fields' => $fields,
                'model' => $model,
            ]);
    }

    public function table($model) {
        $this->validateModel($model);
        $this->checkException('custom_field', 'view');
        $fields = CustomField::filterByModel($model)->paginate(15);

        return view('admin::CustomFields.table')
            ->with([
                'fields' => $fields,
                'model' => $model,
            ]);
    }

    public function edit($id)
    {
        $field = CustomField::find($id);
        $this->checkException('custom_field', 'edit', $field);
        $dataTypes = CustomField::getDataTypes();

        return view('admin::CustomFields.edit')
            ->with([
                'field' => $field,
                'dataTypes' => $dataTypes,
            ]);
    }

    public function update($id)
    {
        $item = CustomField::find($id);
        $this->checkException('custom_field', 'update', $item);
        $this->data['id'] = $id;
        $this->data['slug'] = trim($this->data['slug']);

        CustomFieldFormValidator::validate('update', $this->data);

        $item->update($this->data);

        return ['status' => 1];
    }

    public function create($model)
    {
        $this->validateModel($model);
        $this->checkException('custom_field', 'create');
        $dataTypes = CustomField::getDataTypes();

        return view('admin::CustomFields.create')
            ->with([
                'dataTypes' => $dataTypes,
                'model' => $model,
            ]);
    }

    public function store()
    {
        $this->checkException('custom_field', 'store');
        $this->data['slug'] = trim($this->data['slug']);

        if (isset($this->data['options'])) {
            $this->data['options'] = array_combine(array_map(function($val) {
                return Str::slug($val, '_');
            }, $this->data['options']), $this->data['options']);
        }

        CustomFieldFormValidator::validate('create', $this->data);

        CustomField::create($this->data);

        return ['status' => 1];
    }

    public function destroy()
    {
        $id = request()->get('id');

        $field = CustomField::find($id);
        $this->checkException('custom_field', 'remove', $field);
        $field->delete();

        return ['status' => 1];
    }

    private function validateModel($model)
    {
        if (isset($model) && ! isset(Relation::morphMap()[$model])) {
            throw new ResourseNotFoundException(trans('validation.attributes.model'));
        }
    }
}
