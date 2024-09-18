<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Transformers\Checklist\ChecklistTemplateFullTransformer;
use CustomFacades\Validators\ChecklistTemplateFormValidator;
use Tobuli\Entities\ChecklistTemplate;
use Tobuli\Exceptions\ValidationException;
use FractalTransformer;

class ChecklistTemplateController extends BaseController
{
    public function index()
    {
        $this->checkException('checklist_template', 'view');
        $templates = ChecklistTemplate::available()
            ->paginate(30);

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::paginate($templates, ChecklistTemplateFullTransformer::class)->toArray()
        ));
    }

    public function update($templateId)
    {
        ChecklistTemplateFormValidator::validate('update', $this->data);
        $this->validateRows();

        $template = ChecklistTemplate::find($templateId);

        $this->checkException('checklist_template', 'update', $template);

        $template->update($this->data);
        $template->saveRows($this->data['rows'] ?? []);

        return response()->json(['status' => 1]);
    }

    public function store()
    {
        ChecklistTemplateFormValidator::validate('store', $this->data);
        $this->validateRows();
        $this->checkException('checklist_template', 'store');

        $template = ChecklistTemplate::create($this->data);
        $template->saveRows($this->data['rows'] ?? []);

        return response()->json(['status' => 1]);
    }

    public function destroy()
    {
        $id = request()->get('id');

        $template = ChecklistTemplate::find($id);

        $this->checkException('checklist_template', 'remove', $template);

        $template->delete();

        return response()->json(['status' => 1]);
    }

    private function validateRows()
    {
        $rows = array_filter($this->data['rows'] ?? []);
        $newRows = array_filter($rows['new'] ?? []);
        unset($rows['new']);

        if (empty($rows) && empty($newRows)) {
            throw new ValidationException(trans('validation.min.array', [
                'attribute' => trans('front.activity'),
                'min' => 1,
            ]));
        }
    }
}
