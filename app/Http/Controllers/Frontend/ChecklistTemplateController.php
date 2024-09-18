<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\Validators\ChecklistTemplateFormValidator;
use Tobuli\Entities\ChecklistTemplate;
use Tobuli\Exceptions\ValidationException;

class ChecklistTemplateController extends Controller {
    public function index() {
        $this->checkException('checklist_template', 'view');

        $templates = ChecklistTemplate::available()
            ->paginate(15);

        return view('front::ChecklistTemplates.index_front')
            ->with([
                'data' => $templates
            ]);
    }

    public function indexAdmin() {
        $this->checkException('checklist_template', 'view');

        $templates = ChecklistTemplate::available()
            ->paginate(15);

        return view('front::ChecklistTemplates.index_admin')
            ->with([
                'data' => $templates
            ]);
    }

    public function table() {
        $this->checkException('checklist_template', 'view');

        $templates = ChecklistTemplate::available()
            ->paginate(15);

        return view('front::ChecklistTemplates.table')
            ->with([
                'data' => $templates
            ]);
    }

    public function edit($templateId)
    {
        $template = ChecklistTemplate::find($templateId);

        $this->checkException('checklist_template', 'edit', $template);
        $types = ChecklistTemplate::getTypes();
        $rows = $template->rows()->get();

        return view('front::ChecklistTemplates.edit')->with([
            'template' => $template,
            'types' => $types,
            'rows' => $rows,
            ]);
    }

    public function update($templateId)
    {
        ChecklistTemplateFormValidator::validate('update', $this->data);
        $this->validateRows();

        $template = ChecklistTemplate::find($templateId);

        $this->checkException('checklist_template', 'update', $template);

        $template->update($this->data);
        $template->saveRows($this->data['rows'] ?? []);

        return ['status' => 1];
    }

    public function create()
    {
        $this->checkException('checklist_template', 'create');
        $types = ChecklistTemplate::getTypes();

        return view('front::ChecklistTemplates.create')
            ->with([
                'types' => $types,
            ]);
    }

    public function store()
    {
        ChecklistTemplateFormValidator::validate('store', $this->data);
        $this->validateRows();
        $this->checkException('checklist_template', 'store');

        $template = ChecklistTemplate::create($this->data);
        $template->saveRows($this->data['rows'] ?? []);

        return ['status' => 1];
    }

    public function doDestroy($templateId)
    {
        $template = ChecklistTemplate::find($templateId);

        $this->checkException('checklist_template', 'remove', $template);

        return view('front::ChecklistTemplates.destroy')
            ->with([
                'id' => $templateId
            ]);
    }

    public function destroy()
    {
        $id = request()->get('id');

        $template = ChecklistTemplate::find($id);

        $this->checkException('checklist_template', 'remove', $template);

        $template->delete();

        return ['status' => 1];
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
